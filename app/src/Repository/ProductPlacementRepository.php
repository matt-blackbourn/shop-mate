<?php

namespace App\Repository;

use App\Entity\FoodCategory;
use App\Entity\FoodItem;
use App\Entity\ProductPlacement;
use App\Entity\Supermarket;
use App\Enum\PlacementType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductPlacement>
 */
class ProductPlacementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductPlacement::class);
    }

    public function findOnePlacedByCategoryInSupermarket(
        FoodCategory $category,
        Supermarket $supermarket
    ): ?ProductPlacement
    {
        return $this->createQueryBuilder('pp')
            ->innerJoin('pp.foodItem', 'fi')
            ->andWhere('fi.category = :category')
            ->andWhere('pp.supermarket = :supermarket')
    
            // IMPORTANT: only already-mapped placements
            ->andWhere('pp.edge IS NOT NULL')
    
            ->setParameter('category', $category)
            ->setParameter('supermarket', $supermarket)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function findUserPlacementsInSupermarket(
        FoodItem $foodItem,
        Supermarket $supermarket,
        PlacementType $type,
    ): array
    {
        return $this->createQueryBuilder('pp')
            ->andWhere('pp.foodItem = :foodItem')
            ->andWhere('pp.supermarket = :supermarket')
            ->andWhere('pp.type = :type')
            ->andWhere('pp.supersededBy IS NULL')
            ->setParameter('foodItem', $foodItem)
            ->setParameter('supermarket', $supermarket)
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }
}
