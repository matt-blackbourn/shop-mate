<?php

namespace App\Service;

use App\Entity\Edge;
use App\Entity\ListItem;
use App\Entity\ShoppingList;
use App\Entity\Supermarket;
use App\Repository\EdgeRepository;
use App\Repository\ListItemRepository;
use App\Repository\NodeRepository;
use SplPriorityQueue;

class PathFinder
{
    public function __construct(
        private EdgeRepository $edgeRepository,
        private NodeRepository $nodeRepository,
        private PlacementResolver $placementResolver,
        private ListItemRepository $listItemRepository,
    ){}

    private $phases = [
        Edge::ENTRANCE_PHASE,
        Edge::POST_ENTRANCE_PHASE,
        Edge::MAIN_PHASE,
        Edge::END_PHASE,
    ];

    private $placementCache = []; // Cache for placements to avoid redundant DB calls
    private $nodeCache = []; // Cache for nodes to avoid redundant DB calls

    /**
     * Nearest-neighbour route 
     */
    public function orderShoppingList(
        ShoppingList $shoppingList, 
        Supermarket $supermarket, 
        ?int $startNode = null, 
        $reverseOder = false,
    ): array {
        // Convert collection to id-indexed array, and separate by phase and unmapped items
        $unmappedItems = [];
        $mappedItems = array_fill_keys($this->phases, []);

        foreach ($this->listItemRepository->findUnpickedByShoppingList($shoppingList) as $listItem) {
            $placement = $this->placementResolver->resolve($listItem, $supermarket);
            if($placement){
                $mappedItems[$placement->getEdge()->getPhase()][$listItem->getId()] = $listItem;
                $this->placementCache[$listItem->getId()] = $placement; // Cache placement for later use
            } else {
                $unmappedItems[] = new RoutedListItemDto(
                    item: $listItem,
                    placement: null,
                    targetNodeId: null,
                    distanceFromPrevious: 0,
                    path: []
                );
            }
        }

        // keep for debugging
        // $foodItems = [];
        // foreach ($mappedItems as $phase => $items) {
        //     foreach ($items as $id => $listItem) {
        //         $foodItems[$id] = $listItem->getFoodItem()->getName();
        //     }
        // }
        // dd($foodItems);

        // Apply phase processing rules
        // If we have no entrance phase items, main phase becomes entrance phase
        // Note, we only apply this when starting a shop from the entrance node; if starting mid-shop ($startNode !== null) we disregard
        if (empty($mappedItems[Edge::ENTRANCE_PHASE]) && !$startNode) {
            $mappedItems[Edge::ENTRANCE_PHASE] = $mappedItems[Edge::MAIN_PHASE] ?? [];
            $mappedItems[Edge::MAIN_PHASE] = [];
        }

        // Set some variables before building the route
        $orderedList = [];
        $currentNodeId = $startNode ?? (int) $supermarket->getEntranceNode()->getId();
        $backtrackAvoidanceNode = null;
        $graph = $this->buildGraph($supermarket);

        // Process each phase in order
        foreach($this->phases as $phase) {
            $remainingItems = $mappedItems[$phase];

            // State to track whether we are on the same edge as the previous item
            // If so, we can skip dijkstra etc
            $currentEdgeId = null;
            $currentEdgeEntryNode = null;
            $currentEdgeExitNode = null;
            
            while (!empty($remainingItems)) {

                // Check for items on the same edge as the previous item
                // If found, we just stay on this edge. Saves work and cleans up backtracking in route
                if ($currentEdgeId !== null) {
                    foreach ($remainingItems as $listItem) {

                        $placement = $this->placementCache[$listItem->getId()] ?? null;
                        if ($placement && $placement->getEdge()->getId() === $currentEdgeId) {
                
                            $finalNodeId = $currentEdgeExitNode ?? $currentEdgeEntryNode;
                
                            $orderedList[] = new RoutedListItemDto(
                                item: $listItem,
                                placement: $placement,
                                targetNodeId: $finalNodeId,
                                distanceFromPrevious: 0,
                                path: []
                            );
                
                            unset($remainingItems[$listItem->getId()]);
                            continue 2; // skip full routing logic
                        }
                    }
                    
                    $currentEdgeId = null; // No more items on this edge - continue with normal routing for next item
                }

                // Run Dijkstra from current node to get distances to all other nodes, and the previous node for each (for path reconstruction)
                $dijkstraResult = $this->dijkstra($graph, $currentNodeId, $backtrackAvoidanceNode); 
                $distances = $dijkstraResult['dist'];
                $prevNodeArray = $dijkstraResult['prev'];
    
                // State to track closest item in this iteration
                $closestListItem  = null;
                $closestPlacement = null;
                $closestEntryNode = null;
                $closestExitNode = null;
                $closestDistance = INF;
                $closestPath = null;

                // Aisle state tracking variables
                $nextAisle = $this->findLowestRemainingAisle($remainingItems);
                $currentAisleKey = null;
    
                // Find the closest item out of the remaining list items in the phase
                foreach ($remainingItems as $listItem) {
                    $result = $this->getEntryAndExitOfEdge($distances, $listItem, $supermarket);
                    if ($result === null) {
                        continue;
                    }
                    
                    $entryNode = $result['entryNode'];
                    $distance  = $result['distance'];
                    $placement = $result['placement'];
                    $edge = $placement->getEdge();
                    $aisleKey = $edge->getAisleKey();

                    // SCORING STARTS HERE - this is where we decide which item wins as the "closest" based on distance and various heuristics
                    $score = $distance;
                    
                    
                    // --- Straight path bonus
                    $path = $this->reconstructPath($prevNodeArray, $entryNode);
                    if ($this->isStraightPath($path)) {
                        $score *= 0.6;
                    }

                    // --- Aisle weighting starts here
                    // ✔ Starts with closest item
                    // ✔ If aisle 1 exists, it naturally wins via 0.5 multiplier
                    // ✔ Once in aisle 1, 0.1 multiplier keeps you there
                    // ✔ Won’t jump to aisle 3 before aisle 2
                    // ✔ Still allows non-aisle detours if genuinely closer

                    // If currently inside an aisle → strongly prefer staying
                    if ($currentAisleKey !== null) {
                        if ($aisleKey === $currentAisleKey) {
                            $score *= 0.3;   // stay on aisle (strong bias)
                        } else {
                            $score *= 1.5;     // discourage leaving aisle
                        }

                    } else {
                        // Not currently in aisle → prefer lowest numbered aisle
                        if ($nextAisle !== null) {
                            if($aisleKey === $nextAisle) {
                                $score *= 0.5;   // prefer next aisle
                            } elseif ($aisleKey !== null) {
                                $score *= 1.5;   // discourage skipping ahead
                            }
                            // non-aisle items remain neutral
                        }
                    }
                    
                    // if we find an item that is closer than our current closest, update closest item
                    if ($score < $closestDistance) {
                        $closestListItem = $listItem;
                        $closestPlacement = $result['placement'];
                        $closestEntryNode = $result['entryNode'];
                        $closestExitNode  = $result['exitNode'];
                        $closestDistance  = $score;
                        $closestPath = $path;
                        // if($listItem->getId() === 390){
                        //     dump($closestListItem, $placement);
                        //     dd($closestListItem->getFoodItem()->getName(), 'entry:', $closestEntryNode, 'exit', $closestExitNode,$closestPath);
                        // }
                    }
                }

                // Update state edge
                $currentEdgeId = $closestPlacement->getEdge()->getId();
                $currentAisleKey = $closestPlacement->getEdge()->getAisleKey();
                $currentEdgeEntryNode = $closestEntryNode;
                $currentEdgeExitNode = $closestExitNode;

                $pathToClosestNode = $closestPath;
                if ($closestExitNode !== null) {
                    $pathToClosestNode[] = $closestExitNode; // Append the exit node to the path to ensure we traverse the whole edge where the item is located
                }
                
                // Safety check (should not happen, but avoids infinite loop)
                if ($closestListItem === null) {
                    break;
                }
                
                $finalNodeId = $closestExitNode ?? $closestEntryNode; // The node we will be at after picking this item
                // $backtrackAvoidanceNode = $pathToClosestNode[count($pathToClosestNode) -2] ?? null; // The penultimate node in the path
                $backtrackAvoidanceNode = $closestPath[count($closestPath) -1] ?? null; // The penultimate node in the path
                // $backtrackAvoidanceNode = $closestPath[count($closestPath) -2] ?? null; // The penultimate node in the path
                $currentNodeId = $finalNodeId; // Update this for next iteration

                // if($closestEntryNode === 44){
                //     dd($closestListItem, $closestEntryNode, $closestExitNode, $closestPath, $pathToClosestNode, $finalNodeId, $currentNodeId, $backtrackAvoidanceNode);
                // }

                $orderedList[] = new RoutedListItemDto(
                    item: $closestListItem,
                    placement: $closestPlacement,
                    targetNodeId: $finalNodeId,
                    distanceFromPrevious: $closestDistance,
                    path: $pathToClosestNode
                );
    
                unset($remainingItems[$closestListItem->getId()]);
            }
        }

        // Show unmapped items at the start of the list to prompt user to place them
        // If reverseOrder, we are dealing with a no supermarket case, so we want unmapped items at the end
        return $reverseOder ? array_merge($orderedList, $unmappedItems) : array_merge($unmappedItems, $orderedList); 
    }

    private function findLowestRemainingAisle(array $remainingItems): ?int
    {
        $aisles = [];

        foreach ($remainingItems as $item) {
            $placement = $this->placementCache[$item->getId()];
            $aisleKey = $placement->getEdge()->getAisleKey();
            if ($aisleKey !== null) {
                $aisles[] = $aisleKey;
            }
        }

        return empty($aisles) ? null : min($aisles);
    }

    private function isStraightPath(array $path): bool
    {
        if (count($path) < 2) {
            return false;
        }

        $first = $this->nodeCache[$path[0]];
        $second = $this->nodeCache[$path[1]];

        $dx = $second->getXValue() - $first->getXValue();
        $dy = $second->getYValue() - $first->getYValue();

        // Determine axis of first segment
        if (abs($dx) > 0 && abs($dy) === 0) {
            $axis = 'x';
        } elseif (abs($dy) > 0 && abs($dx) === 0) {
            $axis = 'y';
        } else {
            return false; // diagonal or weird
        }

        // Check all segments
        for ($i = 1; $i < count($path) - 1; $i++) {
            $a = $this->nodeCache[$path[$i]];
            $b = $this->nodeCache[$path[$i + 1]];

            $dx = $b->getXValue() - $a->getXValue();
            $dy = $b->getYValue() - $a->getYValue();

            if ($axis === 'x' && abs($dy) > 0) {
                return false;
            }

            if ($axis === 'y' && abs($dx) > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Convert prev array of nodes to nodeId array in the correct order (Dijkstra gives us breadcrumbs in reverse, so we need to reverse them back)
     */
    private function reconstructPath(array $prevNodeArray, string $targetNode): array
    {
        $path = [];
        $node = $targetNode;

        while ($node !== null) {
            $path[] = (int) $node;  // ← cast every node to int
            $node = $prevNodeArray[$node] ?? null;
        }

        return array_reverse($path);
    }

    /**
     * Build adjacency list from edges
     * 
     * Structure nodeId =>[neighbourNodeId => length, ...]
     */
    private function buildGraph(Supermarket $supermarket): array
    {
        $graph = [];

        foreach ($this->edgeRepository->findAllInSupermarket($supermarket) as $edge) {
            $startNode = $edge->getStart();
            $endNode   = $edge->getEnd();

            $startId = $startNode->getId();
            $endId   = $endNode->getId();
            
            $length = $edge->getLength();

            $graph[$startId][$endId] = $length;
            $graph[$endId][$startId] = $length; // both directions

            // build node cache at the same time to avoid redundant DB calls later
            $this->nodeCache[$startId] ??= $startNode;
            $this->nodeCache[$endId]   ??= $endNode;
        }

        return $graph;
    }


    /**
     * Distance from a node to a food item (edge-based)
     * A food item is on an edge, not a node — so we take the closest endpoint.
     */
    private function getEntryAndExitOfEdge(array $distances, ListItem $listItem, Supermarket $supermarket): ?array
    {
        // Look up the placement for this item from the cache
        $placement = $this->placementCache[$listItem->getId()] ?? null;

        // If there is no placement, return a default "last node in supermarket"
        if (!$placement) {
            return [
                'entryNode' => $this->nodeRepository->findLastNodeInSupermarket($supermarket)->getId(),
                'exitNode' => null,
                'distance' => INF,
                'placement' => null,
            ];
        }

        // Get both nodes of the placement edge
        $startId = $placement->getEdge()->getStart()->getId();
        $endId   = $placement->getEdge()->getEnd()->getId();

        // Lookup distances to both nodes from current position
        $distanceStart = $distances[$startId] ?? INF;
        $distanceEnd   = $distances[$endId] ?? INF;

        // Choose NEAREST as entry
        if ($distanceStart <= $distanceEnd) {
            $entryNode = $startId;
            $exitNode  = $endId;
            $distance  = $distanceStart;
        } else {
            $entryNode = $endId;
            $exitNode  = $startId;
            $distance  = $distanceEnd;
        }

        return [
            'entryNode' => $entryNode,
            'exitNode'  => $exitNode,
            'distance'  => $distance,
            'placement' => $placement,
        ];
    }


    /**
     * Dijkstra (distance from node → all nodes)
     * [1 => 0
     * 2 => 7
     * 3 => 14
     * 4 => 22...]
     */
    public function dijkstra(array $graph, string $startNode, ?int $backtrackAvoidanceNode = null): array
    {
        $distances = [];
        $prev = []; // tracks breadcrumbs for the shortest path
        $queue = new SplPriorityQueue();

        // Set all initial distances from the start node to infinity
        foreach ($graph as $node => $_) {
            $distances[$node] = INF;
        }

        // Set start (current) node distance to 0
        $distances[$startNode] = 0;

        // Insert the starting node into the priority queue with a priority of 0
        $queue->insert($startNode, 0);

        while (!$queue->isEmpty()) {
            // Extract the node with the smallest distance (highest priority)
            $shortest = (int) $queue->extract(); //normalize here

            // Iterate through all neighboring nodes of the current node
            foreach ($graph[$shortest] ?? [] as $nodeId => $length) {
                $nodeId = (int) $nodeId; //normalize here

                // Skip moving back to the node we just came from
                if ($backtrackAvoidanceNode !== null && $nodeId === $backtrackAvoidanceNode) {
                    continue;
                }
                
                // Calculate the alternative distance to the neighboring node
                $alt = $distances[$shortest] + $length;

                // If the alternative distance is shorter, update the distance and reinsert into the queue
                if ($alt < $distances[$nodeId]) {
                    $distances[$nodeId] = $alt;
                    $prev[$nodeId] = $shortest;
    
                    // Use negative distance to get min-heap behavior (SplPriorityQueue is a max-heap)
                    $queue->insert($nodeId, -$alt);
                }
            }
        }

        return [
            'dist' => $distances,
            'prev' => $prev,
        ];
    }
}
