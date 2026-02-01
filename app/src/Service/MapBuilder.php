<?php

namespace App\Service;

use App\Repository\EdgeRepository;
use App\Repository\NodeRepository;

final class MapBuilder
{
    public function __construct(
        private NodeRepository $nodeRepository,
        private EdgeRepository $edgeRepository,
    ) {}

    public function getAllNodes($supermarket){
        $nodes = []; 
        foreach ($this->nodeRepository->findBySupermarket($supermarket) as $node) {
            $nodes[] = [
                'id' => $node->getId(),
                'x' => $node->getXValue(),
                'y' => $node->getYValue()
            ];
        }

        return $nodes;
    }

    public function getAllEdges($supermarket){
        $edges = []; 
        foreach ($this->edgeRepository->findBySupermarket($supermarket) as $edge) {
            $edges[] = [
                'id' => $edge->getId(),
                'from' => $edge->getStart()->getId(),
                'to'   => $edge->getEnd()->getId(),
                'x1'   => $edge->getStart()->getXValue(),
                'y1'   => $edge->getStart()->getYValue(),
                'x2'   => $edge->getEnd()->getXValue(),
                'y2'   => $edge->getEnd()->getYValue(),
                'phase'=> $edge->getPhase(),
                'element'=> null, // placeholder for front-end use
            ];
        }
        return $edges;
    }
}

