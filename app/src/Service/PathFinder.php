<?php

namespace App\Service;

use App\Entity\Edge;
use App\Entity\FoodItem;
use App\Entity\ShoppingList;
use App\Entity\Supermarket;
use App\Repository\EdgeRepository;
use App\Repository\NodeRepository;
use App\Repository\ProductLocationRepository;
use SplPriorityQueue;

class PathFinder
{
    public function __construct(
        private EdgeRepository $edgeRepository,
        private ProductLocationRepository $productLocationRepository,
        private NodeRepository $nodeRepository,
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
    public function buildShoppingRoute(ShoppingList $shoppingList): array {
        // Convert collection to id-indexed array, and separate by phase and unmapped items
        $unmappedItems = [];
        $mappedItems = array_fill_keys($this->phases, []);

        // Sort food with known location into phase buckets
        foreach ($shoppingList->getItems() as $foodItem) {
            $location = $this->productLocationRepository->findOneBy([
                'foodItem' => $foodItem,
                'supermarket' => $shoppingList->getSupermarket(),
            ]);
            
            if($location){
                $mappedItems[$location->getEdge()->getPhase()][$foodItem->getId()] = $foodItem;
            } else {
                $unmappedItems[$foodItem->getId()] = $foodItem; // We'll add these at the end
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
        $currentNodeId = $shoppingList->getSupermarket()->getEntranceNode()->getId();
        $graph = $this->buildGraph($shoppingList->getSupermarket());

        // Process each phase in order
        foreach($this->phases as $phase) {
            $remainingItems = $mappedItems[$phase];

            while (!empty($remainingItems)) {
                $closestItem = null;
                $closestNode = null;
                $closestDistance = INF;
                $distances = $this->dijkstra($graph, $currentNodeId); // get distances from current node to all others
    
                // Find the closest item out of the remaining food items in the phase
                foreach ($remainingItems as $foodItem) {
                    $result = $this->getClosestNodeToFoodItem($distances, $foodItem, $shoppingList->getSupermarket());
                    if ($result === null) {
                        continue;
                    }
    
                    if ($result['distance'] < $closestDistance) {
                        $closestDistance = $result['distance'];
                        $closestNode = $result['node'];
                        $closestItem = $foodItem;
                    }
                }
    
                // Safety check (should not happen, but avoids infinite loop)
                if ($closestItem === null) {
                    break;
                }
    
                // Update current position and add item to route. Remove this item from the remaining items before next iteration
                $currentNodeId = $closestNode;
                $orderedList[] = $closestItem;
                unset($remainingItems[$closestItem->getId()]);
            }
        }

        // Append unmapped items at the end
        foreach ($unmappedItems as $foodItem) {
            $orderedList[] = $foodItem;
        }

        return $orderedList;
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

            $graph[$start][$end] = $length;
            $graph[$end][$start] = $length; // both directions
        }

        return $graph;
    }


    /**
     * Distance from a node to a food item (edge-based)
     * A food item is on an edge, not a node — so we take the closest endpoint.
     */
    private function getClosestNodeToFoodItem(array $distances, FoodItem $foodItem, Supermarket $supermarket): ?array
    {
        $location = $this->productLocationRepository->findOneBy([
            'foodItem' => $foodItem,
            'supermarket' => $supermarket,
        ]);

        if (!$location) {
            return [
                'node' => $this->nodeRepository->findLastNodeInSupermarket($supermarket)->getId(),
                'distance' => INF,
            ];
        }

        $startId = $location->getEdge()->getStart()->getId();
        $endId   = $location->getEdge()->getEnd()->getId();

        $distanceToStart = $distances[$startId] ?? INF;
        $distanceToEnd   = $distances[$endId] ?? INF;

        if ($distanceToStart <= $distanceToEnd) {
            return [
                'node' => $startId,
                'distance' => $distanceToStart,
            ];
        }

        return [
            'node' => $endId,
            'distance' => $distanceToEnd,
        ];
    }


    /**
     * Dijkstra (distance from node → all nodes)
     * [1 => 0
     * 2 => 7
     * 3 => 14
     * 4 => 22...]
     */
    private function dijkstra(array $graph, string $start): array
    {
        $distances = [];
        $queue = new SplPriorityQueue();

        // Set all initial distances from the start node to infinity
        foreach ($graph as $node => $_) {
            $distances[$node] = INF;
        }

        // Set start (current) node distance to 0
        $distances[$start] = 0;

        // Insert the starting node into the priority queue with a priority of 0
        $queue->insert($start, 0);

        // Process the priority queue until it is empty
        while (!$queue->isEmpty()) {
            // Extract the node with the smallest distance (highest priority)
            $shortest = $queue->extract();

            // Iterate through all neighboring nodes of the current node
            foreach ($graph[$shortest] ?? [] as $nodeId => $length) {
                // Calculate the alternative distance to the neighboring node
                $alt = $distances[$shortest] + $length;

                // If the alternative distance is shorter, update the distance and reinsert into the queue
                if ($alt < $distances[$nodeId]) {
                    $distances[$nodeId] = $alt;

                    // Use negative distance to get min-heap behavior (SplPriorityQueue is a max-heap)
                    $queue->insert($nodeId, -$alt);
                }
            }
        }

        return $distances;
    }
}
