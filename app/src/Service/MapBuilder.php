<?php

namespace App\Service;

use App\Repository\EdgeRepository;
use App\Repository\NodeRepository;
use App\Repository\ShelfRepository;

final class MapBuilder
{
    public function __construct(
        private NodeRepository $nodeRepository,
        private EdgeRepository $edgeRepository,
        private ShelfRepository $shelfRepository,
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

    public function getAllShelves($supermarket): array{
        $shelves = []; 
        foreach ($this->shelfRepository->findBySupermarket($supermarket) as $shelf) {
            $shelves[] = [
                'id' => $shelf->getId(),
                'clientId' => bin2hex(random_bytes(16)),
                'width' => $shelf->getWidth(),
                'height' => $shelf->getHeight(),
                'x' => $shelf->getX(),
                'y' => $shelf->getY(),
                'deleted' => false,
            ];
        }
        return $shelves;
    }
}

