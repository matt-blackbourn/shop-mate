<?php

namespace App\Service;

use App\Entity\FoodItem;
use App\Entity\ListItem;
use App\Entity\PlacementType;
use App\Entity\Supermarket;
use App\Entity\User;
use App\Enum\PlacementStatus;
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
    
        /*
         * 1️⃣ USER placements (user-relative truth)
         */
        $userPlacements = $this->createQueryBuilder('pp')
            ->andWhere('pp.foodItem = :foodItem')
            ->andWhere('pp.supermarket = :supermarket')
            ->andWhere('pp.type = :type')
            ->andWhere('pp.supersededBy IS NULL')
            ->setParameters([
                'foodItem' => $foodItem,
                'supermarket' => $supermarket,
                'type' => PlacementType::USER,
            ])
            ->getQuery()
            ->getResult();
    
        foreach ($userPlacements as $placement) {
            if ($placement->getUser()?->getId() === $user->getId()) {
                return PlacementStatus::CONFIRMED;
            }
        }
    
        if (!empty($userPlacements)) {
            return PlacementStatus::PROVISIONAL;
        }
    
        /*
         * 2️⃣ SYSTEM placement (global truth unless user disagrees)
         */
        $systemPlacement = $this->findOneBy([
            'foodItem' => $foodItem,
            'supermarket' => $supermarket,
            'type' => PlacementType::SYSTEM,
            'supersededBy' => null,
        ]);
    
        if ($systemPlacement) {
            return PlacementStatus::SYSTEM;
        }
    
        /*
         * 3️⃣ CATEGORY placement (fallback inference)
         */
        $categoryPlacement = $this->findOneBy([
            'foodItem' => $foodItem,
            'supermarket' => $supermarket,
            'type' => PlacementType::CATEGORY,
            'supersededBy' => null,
        ]);
    
        if ($categoryPlacement) {
            return PlacementStatus::CATEGORY;
        }
    
        return PlacementStatus::NONE;
    }
    
    
    
}
