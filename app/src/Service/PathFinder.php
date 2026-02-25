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
     * Nearest-neighbour route (the core algorithm)
     */
    public function orderShoppingList(ShoppingList $shoppingList, Supermarket $supermarket, ?int $startNode = null): array {
        // Convert collection to id-indexed array, and separate by phase and unmapped items
        $unmappedItems = [];
        $mappedItems = array_fill_keys($this->phases, []);

        foreach ($this->listItemRepository->findByShoppingList($shoppingList) as $listItem) {
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
        $previousNodeId = null;
        $graph = $this->buildGraph($supermarket);

        // Process each phase in order
        foreach($this->phases as $phase) {
            $remainingItems = $mappedItems[$phase];
            
            while (!empty($remainingItems)) {
                $result = $this->dijkstra($graph, $currentNodeId);  // get distances from current node to all other, plus prev nodes
                $distances = $result['dist'];
                $prevNodeArray = $result['prev'];
    
                $closestListItem  = null;
                $closestPlacement = null;
                $closestEntryNode = null;
                $closestExitNode = null;
                $closestDistance = INF;

                $currentAxis = null; // first move, or no movement

                if($previousNodeId){
                    $dx = $this->nodeCache[$currentNodeId]->getXValue() - $this->nodeCache[$previousNodeId]->getXValue();
                    $dy = $this->nodeCache[$currentNodeId]->getYValue() - $this->nodeCache[$previousNodeId]->getYValue();

                    if($dx){
                        $currentAxis = 'x';
                    } elseif($dy) {
                        $currentAxis = 'y';
                    } 
                }
                dump($currentAxis);
    
                // Find the closest item out of the remaining list items in the phase
                foreach ($remainingItems as $listItem) {
                    $result = $this->getEntryAndExitOfEdge($distances, $listItem, $supermarket);
                    if ($result === null) {
                        continue;
                    }

                    $entryNode = $result['entryNode'];
                    $distance  = $result['distance'];
              
                
                    $score = $distance;
                    if ($this->isStraightContinuation($currentNodeId, $entryNode, $currentAxis)) {
                        // dd($listItem->getFoodItem()->getName(), 'is straight continuation!');
                        $score = 0;
                    }

                    // if we find an item that is closer than our current closest, update closest item
                    // if ($result['distance'] < $closestDistance) {
                    //     $closestListItem = $listItem;
                    //     $closestPlacement = $result['placement'];
                    //     $closestEntryNode = $result['entryNode'];
                    //     $closestExitNode  = $result['exitNode'];
                    //     $closestDistance  = $result['distance'];
                    // }
                    if ($score < $closestDistance) {
                        $closestListItem = $listItem;
                        $closestPlacement = $result['placement'];
                        $closestEntryNode = $result['entryNode'];
                        $closestExitNode  = $result['exitNode'];
                        $closestDistance  = $score;
                    }
                }

                $pathToClosestNode = $this->reconstructPath($prevNodeArray, $closestEntryNode);
                if ($closestExitNode !== null) {
                    $pathToClosestNode[] = $closestExitNode; // Append the exit node to the path to ensure we traverse the whole edge where the item is located
                }
    
                // Safety check (should not happen, but avoids infinite loop)
                if ($closestListItem === null) {
                    break;
                }
    
                $finalNodeId = $closestExitNode ?? $closestEntryNode; // The node we will be at after picking this item
                $previousNodeId = $currentNodeId;
                $currentNodeId = $finalNodeId; // Update this for next iteration

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
        exit;
        // dd($orderedList);

        return array_merge($unmappedItems, $orderedList); // Show unmapped items at the start of the list to prompt user to place them
    }

    private function isStraightContinuation(
        int $currentNodeId,
        int $entryNodeId,
        ?string $currentAxis
    ): bool {
        if ($currentAxis === null) {
            return false; // first move
        }
    
        $current = $this->nodeCache[$currentNodeId];
        $entry   = $this->nodeCache[$entryNodeId];
    
        if ($currentAxis === 'x') {
            return $current->getYValue() === $entry->getYValue();
        }
    
        if ($currentAxis === 'y') {
            return $current->getXValue() === $entry->getXValue();
        }
    
        return false;
    }

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
            $start = $edge->getStart()->getId();
            $end = $edge->getEnd()->getId();
            $length = $edge->getLength();

            $graph[(int) $start][(int) $end] = $length;
            $graph[(int) $end][(int) $start] = $length; // both directions

            // build node cache at the same time to avoid redundant DB calls later
            if (!isset($this->nodeCache[$edge->getStart()->getId()])) {
                $this->nodeCache[$edge->getStart()->getId()] = $edge->getStart();
            }
            if (!isset($this->nodeCache[$edge->getEnd()->getId()])) {
                $this->nodeCache[$edge->getEnd()->getId()] = $edge->getEnd();
            }
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

        // Choose the node that is further away (furthest from current position)
        // if ($distanceStart >= $distanceEnd) {
        //     $furthestNode = $startId;
        //     $distance = $distanceStart;
        // } else {
        //     $furthestNode = $endId;
        //     $distance = $distanceEnd;
        // }

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
    public function dijkstra(array $graph, string $startNode): array
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
