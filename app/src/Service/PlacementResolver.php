<?php

namespace App\Service;

use App\Entity\ListItem;
use App\Entity\ProductPlacement;
use App\Entity\Supermarket;
use App\Enum\PlacementStatus;
use App\Enum\PlacementType;
use App\Repository\EdgeRepository;
use App\Repository\ProductPlacementRepository;
use Symfony\Bundle\SecurityBundle\Security;

final class PlacementResolver
{
    public function __construct(
        private ProductPlacementRepository $productPlacementRepo,
        private EdgeRepository $edgeRepo,
        private Security $security,
    ) {}

    public function resolve(
        ListItem $listItem,
        Supermarket $supermarket,
    ): ?ProductPlacement {
        $foodItem = $listItem->getFoodItem();
        $user = $this->security->getUser();

        /**
         * 1️⃣ USER placement for THIS user (highest precedence)
         */
        $userPlacement = $this->productPlacementRepo->findActiveUserPlacement($foodItem, $supermarket, $user);
        if ($userPlacement) {
            $listItem->setPlacementStatus(PlacementStatus::CONFIRMED);
            return $userPlacement;
        }

        /**
         * 2️⃣ SYSTEM placement
         */
        $systemPlacement = $this->productPlacementRepo->findActiveSystemPlacement($foodItem, $supermarket);
        if ($systemPlacement) {
            $listItem->setPlacementStatus(PlacementStatus::SYSTEM);
            return $systemPlacement;
        }

        /**
         * 3️⃣ OTHER USER placement → provisional
         */
        $otherUserPlacement = $this->productPlacementRepo->findActiveOtherUserPlacement($foodItem, $supermarket, $user);
        if ($otherUserPlacement) {
            $listItem->setPlacementStatus(PlacementStatus::PROVISIONAL);
            return $otherUserPlacement;
        }

        
        /**
         * 4️⃣ GROUP placement
         */
        $groupPlacement = $this->productPlacementRepo->findActiveGroupPlacement($foodItem, $supermarket);
        if ($groupPlacement) {
            $listItem->setPlacementStatus(PlacementStatus::GROUP);
            return $groupPlacement;
        }

        /**
         * 5️⃣ Nothing
         */
        $listItem->setPlacementStatus(PlacementStatus::NONE);
        return null;
    }

    public function inferPlacement(
        int $foodItemId,
        int $targetSupermarketId
    ): ?array {
    
        $bestMatch = [];
        $donors = $this->productPlacementRepo->findDonorSupermarkets($foodItemId);
    
        foreach ($donors as $donor) {
    
            $donorStoreId = $donor['supermarketId'];
            $donorPlacement = $this->productPlacementRepo->findPlacementForFoodInStore($foodItemId, $donorStoreId);
            if (!$donorPlacement) {
                continue;
            }

            $edge = $donorPlacement->getEdge();
    
            // --- SAME EDGE CHECK ---
            $placements = $this->productPlacementRepo->findPlacementsOnEdge($edge->getId(), $foodItemId);
            foreach ($placements as $placement) {
                $anchorFoodId = $placement->getFoodItem()->getId();
                $targetPlacement = $this->productPlacementRepo->findPlacementForFoodInStore($anchorFoodId, $targetSupermarketId);
                if (!$targetPlacement) {
                    continue;
                }
    
                $match = [
                    'edge' => $targetPlacement->getEdge(),
                    'aisleSide' => $placement->getAisleSide(),
                ];

                $type = $targetPlacement->getType();
    
                if ($type === PlacementType::SYSTEM) {
                    return $match;
                }
    
                if ($type === PlacementType::USER) {
                    $bestMatch = $match;
                }
    
                if ($type === PlacementType::GROUP && !count($bestMatch)) {
                    $bestMatch = $match;
                }
            }
        }
    
        if (count($bestMatch) > 0) {
            return $bestMatch;
        }
    
        // ---- NEIGHBOUR EDGE PHASE ----
    
        foreach ($donors as $donor) {
            $donorStoreId = $donor['supermarketId'];
            $donorPlacement = $this->productPlacementRepo->findPlacementForFoodInStore($foodItemId, $donorStoreId);
    
            if (!$donorPlacement) {
                continue;
            }
    
            $edge = $donorPlacement->getEdge();
            $neighbours = $this->edgeRepo->findNeighbourEdges($edge->getStart()->getId(), $edge->getEnd()->getId());
            
            foreach ($neighbours as $neighbour) {
                $placements = $this->productPlacementRepo->findPlacementsOnEdge($neighbour->getId(), $foodItemId);

                foreach ($placements as $placement) {
                    $anchorFoodId = $placement->getFoodItem()->getId();
                    $targetPlacement = $this->productPlacementRepo->findPlacementForFoodInStore($anchorFoodId, $targetSupermarketId);
                    if (!$targetPlacement) {
                        continue;
                    }

                    $match = [
                        'edge' => $targetPlacement->getEdge(),
                        'aisleSide' => $placement->getAisleSide(),
                    ];
    
                    $type = $targetPlacement->getType();
    
                    if ($type === PlacementType::SYSTEM) {
                        return $match;
                    }
    
                    if ($type === PlacementType::USER) {
                        $bestMatch = $match;
                    }
    
                    if ($type === PlacementType::GROUP && !count($bestMatch)) {
                        $bestMatch = $match;
                    }
                }
            }
        }
    
        return $bestMatch;
    }
}

