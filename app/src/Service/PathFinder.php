<?php

namespace App\Service;

use App\Entity\Edge;
use App\Entity\ListItem;
use App\Entity\ShoppingList;
use App\Entity\Supermarket;
use App\Repository\EdgeRepository;
use App\Repository\ListItemRepository;
use App\Repository\NodeRepository;
use App\Repository\ProductPlacementRepository;
use SplPriorityQueue;

class PathFinder
{
    public function __construct(
        private EdgeRepository $edgeRepository,
        private ProductPlacementRepository $productPlacementRepository,
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

    /**
     * Nearest-neighbour route (the core algorithm)
     */
    public function orderShoppingList(ShoppingList $shoppingList, Supermarket $supermarket): array {
        // Convert collection to id-indexed array, and separate by phase and unmapped items
        $unmappedItems = [];
        $mappedItems = array_fill_keys($this->phases, []);

        foreach ($this->listItemRepository->findByShoppingListOrderedByCategory($shoppingList) as $listItem) {
            $placement = $this->placementResolver->resolve($listItem, $supermarket);
            if($placement){
                $mappedItems[$placement->getEdge()->getPhase()][$listItem->getId()] = $listItem;
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
        if (empty($mappedItems[Edge::ENTRANCE_PHASE])) {
            $mappedItems[Edge::ENTRANCE_PHASE] = $mappedItems[Edge::MAIN_PHASE] ?? [];
            $mappedItems[Edge::MAIN_PHASE] = [];
        }

        // Set some variables before building the route
        $orderedList = [];
        $currentNodeId = (int) $supermarket->getEntranceNode()->getId();
        $graph = $this->buildGraph($supermarket);

        // Process each phase in order
        foreach($this->phases as $phase) {
            $remainingItems = $mappedItems[$phase];
            
            while (!empty($remainingItems)) {
                $result = $this->dijkstra($graph, $currentNodeId);  // get distances from current node to all other, plus prev nodes
                $distances = $result['dist'];
                $prev = $result['prev'];
    
                $closestListItem  = null;
                $closestNode = null;
                $closestDistance = INF;
                $pathToClosestNode = null;
                $closestPlacement = null;
    
                 // Find the closest item out of the remaining list items in the phase
                foreach ($remainingItems as $listItem) {
                    $result = $this->getClosestNodeToListItem($distances, $listItem, $supermarket);
                    if ($result === null) {
                        continue;
                    }

                    if ($result['distance'] < $closestDistance) {
                        $closestListItem = $listItem;
                        $closestPlacement = $result['placement'];
                        $closestNode = $result['node'];
                        $closestDistance = $result['distance'];
                        $pathToClosestNode = $this->reconstructPath($prev, $closestNode);
                    }
                }
    
                // Safety check (should not happen, but avoids infinite loop)
                if ($closestListItem === null) {
                    break;
                }
    
                $currentNodeId = $closestNode;

                $orderedList[] = new RoutedListItemDto(
                    item: $closestListItem,
                    placement: $closestPlacement,
                    targetNodeId: $closestNode,
                    distanceFromPrevious: $closestDistance,
                    path: $pathToClosestNode
                );
    
                unset($remainingItems[$closestListItem->getId()]);
            }
        }

        return array_merge($orderedList, $unmappedItems);
    }

    private function reconstructPath(array $prev, string $targetNode): array
    {
        $path = [];
        $node = $targetNode;

        while ($node !== null) {
            $path[] = (int) $node;  // ← cast every node to int
            $node = $prev[$node] ?? null;
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
        }

        return $graph;
    }


    /**
     * Distance from a node to a food item (edge-based)
     * A food item is on an edge, not a node — so we take the closest endpoint.
     */
    private function getClosestNodeToListItem(array $distances, ListItem $listItem, Supermarket $supermarket): ?array
    {
        $placement = $this->productPlacementRepository->findOneBy([
            'foodItem' => $listItem->getFoodItem(),
            'supermarket' => $supermarket,
        ]);

        if (!$placement) {
            return [
                'node' => $this->nodeRepository->findLastNodeInSupermarket($supermarket)->getId(),
                'distance' => INF,
            ];
        }
        
        $edge = $placement->getEdge();
        
        $startId = $edge->getStart()->getId();
        $endId   = $edge->getEnd()->getId();
        
        $distanceToStart = $distances[$startId] ?? INF;
        $distanceToEnd   = $distances[$endId] ?? INF;

        if($distanceToStart <= $distanceToEnd){
            return [
                'distance' => $distanceToStart,
                'node' => $startId, 
                'placement' => $placement,
            ];
        } else {
            return [
                'distance' => $distanceToEnd,
                'node' => $endId, 
                'placement' => $placement,
            ];
        }
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
