<?php

namespace App\Service;

use App\Entity\ListItem;
use App\Entity\ProductPlacement;
use App\Entity\Supermarket;
use App\Entity\User;
use App\Enum\PlacementStatus;
use App\Enum\PlacementType;
use App\Repository\ProductPlacementRepository;

final class PlacementResolver
{
    public function __construct(
        private ProductPlacementRepository $productPlacementRepository,
    ){}

    public function resolvePlacementStatus(
        ListItem $listItem,
        Supermarket $supermarket,
        User $user
    ): PlacementStatus {
        $foodItem = $listItem->getFoodItem();
    
        // Find all user placements for this food item in this supermarket
        $userPlacements = $this->productPlacementRepository->findUserPlacementsInSupermarket($foodItem, $supermarket, PlacementType::USER);
    
        // If the placements user matches the current user, CONFIRMED
        foreach ($userPlacements as $placement) {
            if ($placement->getUser()?->getId() === $user->getId()) {
                return PlacementStatus::CONFIRMED;
            }
        }
    
        // If not, some other user has placed it, PROVISIONAL
        if (!empty($userPlacements)) {
            return PlacementStatus::PROVISIONAL;
        }
    
        // Find system placement (there should only be one) for this food item in this supermarket
        $systemPlacement = $this->productPlacementRepository->findOneBy([
            'foodItem' => $foodItem,
            'supermarket' => $supermarket,
            'type' => PlacementType::SYSTEM,
            'supersededBy' => null,
        ]);
    
        if ($systemPlacement) {
            return PlacementStatus::SYSTEM;
        }
    
        // Find category placement for this food item in this supermarket
        $categoryPlacement = $this->productPlacementRepository->findOneBy([
            'foodItem' => $foodItem,
            'supermarket' => $supermarket,
            'type' => PlacementType::CATEGORY,
            'supersededBy' => null,
        ]);
    
        if ($categoryPlacement) {
            return PlacementStatus::CATEGORY;
        }
    
        // This item has no placement
        return PlacementStatus::NONE;
    }
}
