<?php

namespace App\Service;

use App\Entity\FoodItem;
use App\Entity\Supermarket;
use App\Enum\PlacementStatus;

final class PlacementResolver
{
    public function resolve(
        FoodItem $foodItem,
        Supermarket $supermarket
    ): PlacementStatus {

        foreach ($foodItem->getProductPlacements() as $placement) {
            if ($placement->getSupermarket() !== $supermarket) {
                continue;
            }

            if ($placement->getType()->getId() === 1) { // type=system placement
                return PlacementStatus::SYSTEM;
            }
        }

        foreach ($foodItem->getProductPlacements() as $placement) {
            if ($placement->getSupermarket() !== $supermarket) {
                continue;
            }

            if ($placement->isCategoryBased() || $placement->isShared()) {
                return PlacementStatus::APPROX;
            }
        }

        return PlacementStatus::MISSING;
    }
}
