<?php

namespace App\Service;

use App\Entity\ListItem;
use App\Entity\ProductPlacement;
use App\Entity\Supermarket;
use App\Enum\PlacementStatus;
use App\Repository\ProductPlacementRepository;
use Symfony\Bundle\SecurityBundle\Security;

final class PlacementResolver
{
    public function __construct(
        private ProductPlacementRepository $placements,
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
        $userPlacement = $this->placements->findActiveUserPlacement($foodItem, $supermarket, $user);
        if ($userPlacement) {
            $listItem->setPlacementStatus(PlacementStatus::CONFIRMED);
            return $userPlacement;
        }

        /**
         * 2️⃣ SYSTEM placement
         */
        $systemPlacement = $this->placements->findActiveSystemPlacement($foodItem, $supermarket);
        if ($systemPlacement) {
            $listItem->setPlacementStatus(PlacementStatus::SYSTEM);
            return $systemPlacement;
        }

        /**
         * 3️⃣ OTHER USER placement → provisional
         */
        $otherUserPlacement = $this->placements->findActiveOtherUserPlacement($foodItem, $supermarket, $user);
        if ($otherUserPlacement) {
            $listItem->setPlacementStatus(PlacementStatus::PROVISIONAL);
            return $otherUserPlacement;
        }

        
        /**
         * 4️⃣ GROUP placement
         */
        $groupPlacement = $this->placements->findActiveGroupPlacement($foodItem, $supermarket);
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
}

