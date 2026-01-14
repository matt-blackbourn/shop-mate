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
use Doctrine\Common\Collections\Collection;

class PathFinder
{
    public function __construct(
        private EdgeRepository $edgeRepository,
        private ProductLocationRepository $productLocationRepository,
        private NodeRepository $nodeRepository,
    ){}

    /**
     * Nearest-neighbour route (the core algorithm)
     */
    public function buildShoppingRoute(ShoppingList $shoppingList): array {
        // Convert collection to id-indexed array (your approach 👍), and separate by phase and unmapped items
        $unmappedItems = [];
        $mappedItems = [
            Edge::ENTRANCE_PHASE => [],
            Edge::POST_ENTRANCE_PHASE => [],
            Edge::MAIN_PHASE => [],
            Edge::END_PHASE => [],
        ];
        foreach ($shoppingList->getItems() as $foodItem) {
            $location = $this->productLocationRepository->findOneBy([
                'foodItem' => $foodItem,
                'supermarket' => $shoppingList->getSupermarket(),
            ]);

            if($location){
                $mappedItems[$location->getEdge()->getPhase()][$foodItem->getId()] = $foodItem;
            } else {
                $unmappedItems[$foodItem->getId()] = $foodItem;
            }
        }

        $graph = $this->buildGraph($shoppingList->getSupermarket());
        $route = [];
        $currentNodeId = $shoppingList->getSupermarket()->getEntranceNode()->getId();

        foreach([
            Edge::ENTRANCE_PHASE,
            Edge::POST_ENTRANCE_PHASE,
            Edge::MAIN_PHASE,
            Edge::END_PHASE,
        ] as $phase) {

            $remainingItems = $mappedItems[$phase];
      
            while (!empty($remainingItems)) {
                $distances = $this->dijkstra($graph, $currentNodeId);
    
                $closestItem = null;
                $closestNode = null;
                $closestDistance = INF;
    
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
    
                $currentNodeId = $closestNode;
                $route[] = $closestItem;
    
                unset($remainingItems[$closestItem->getId()]);
            }
        }

        // Append unmapped items at the end
        foreach ($unmappedItems as $foodItem) {
            $route[] = $foodItem;
        }

        return $route;
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
        $productLocation = $this->productLocationRepository->findOneBy([
            'foodItem' => $foodItem,
            'supermarket' => $supermarket,
        ]);

        if (!$productLocation) {
            return [
                'node' => $this->nodeRepository->findLastNodeInSupermarket($supermarket)->getId(),
                'distance' => INF,
            ];
        }

        $edge = $productLocation->getEdge();

        $startId = $edge->getStart()->getId();
        $endId   = $edge->getEnd()->getId();


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
    public function dijkstra(array $graph, string $start): array
    {
        $dist = [];
        $queue = new SplPriorityQueue();

        foreach ($graph as $node => $_) {
            $dist[$node] = INF;
        }

        $dist[$start] = 0;
        $queue->insert($start, 0);

        while (!$queue->isEmpty()) {
            $u = $queue->extract();

            foreach ($graph[$u] ?? [] as $v => $weight) {
                $alt = $dist[$u] + $weight;
                if ($alt < $dist[$v]) {
                    $dist[$v] = $alt;
                    $queue->insert($v, -$alt); // max-heap workaround
                }
            }
        }

        return $dist;
    }




}
