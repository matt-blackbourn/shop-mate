<?php

namespace App\Service;

use App\Entity\FoodItem;
use SplPriorityQueue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Collections\Collection;


class Order
{
    /**
     * Build adjacency list from edges
     */
    private function buildGraph(array $edges): array
    {
        $graph = [];

        foreach ($edges as $edge) {
            $a = $edge->getStartNodeId();
            $b = $edge->getEndNodeId();
            $w = $edge->getLength();

            $graph[$a][$b] = $w;
            $graph[$b][$a] = $w; // undirected
        }

        return $graph;
    }

    /**
     * Dijkstra (node → all nodes)
     */
    private function dijkstra(array $graph, string $start): array
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

    /**
     * Distance from a node to a food item (edge-based)
     * A food item is on an edge, not a node — so we take the closest endpoint.
     */
    private function distanceToFoodItem(
        array $distances,
        $foodItem
    ): float {
        $edge = $foodItem->getProductPlacement()->getEdge();
    
        $a = $edge->getStartNodeId();
        $b = $edge->getEndNodeId();
    
        return min($distances[$a], $distances[$b]);
    }


    /**
     * Nearest-neighbour route (the core algorithm)
     */
    public function buildShoppingRoute(
        array $graph,
        string $entranceNodeId,
        Collection $foodItems
    ): array {
        $remaining = $foodItems->toArray();
        $route = [];

        $currentNode = $entranceNodeId;

        while (!empty($remaining)) {
            $distances = $this->dijkstra($graph, $currentNode);

            $closestIndex = null;
            $closestDistance = INF;

            foreach ($remaining as $i => $foodItem) {
                $d = $this->distanceToFoodItem($distances, $foodItem);
                if ($d < $closestDistance) {
                    $closestDistance = $d;
                    $closestIndex = $i;
                }
            }

            $nextItem = $remaining[$closestIndex];
            $route[] = $nextItem;

            // Move current position to the item's edge (pick nearest node)
            $edge = $nextItem->getProductPlacement()->getEdge();
            $a = $edge->getStartNodeId();
            $b = $edge->getEndNodeId();

            $currentNode = ($distances[$a] < $distances[$b]) ? $a : $b;

            unset($remaining[$closestIndex]);
            $remaining = array_values($remaining);
        }

        return $route;
    }



}
