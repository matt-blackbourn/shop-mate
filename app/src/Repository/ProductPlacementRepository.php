<?php

namespace App\Repository;

use App\Entity\FoodItem;
use App\Entity\ProductPlacement;
use App\Entity\Supermarket;
use App\Entity\User;
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



    public function findActiveUserPlacement(
        FoodItem $foodItem,
        Supermarket $supermarket,
        User $user,
    ): ?ProductPlacement {
        return $this->createQueryBuilder('pp')
            ->andWhere('pp.foodItem = :foodItem')
            ->andWhere('pp.supermarket = :supermarket')
            ->andWhere('pp.type = :type')
            ->andWhere('pp.suggestedBy = :user')
            ->andWhere('pp.supersededBy IS NULL')
            ->setParameter('foodItem', $foodItem)
            ->setParameter('supermarket', $supermarket)
            ->setParameter('user', $user)
            ->setParameter('type', PlacementType::USER)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findActiveSystemPlacement(
        FoodItem $foodItem,
        Supermarket $supermarket,
    ): ?ProductPlacement {
        return $this->createQueryBuilder('pp')
            ->andWhere('pp.foodItem = :foodItem')
            ->andWhere('pp.supermarket = :supermarket')
            ->andWhere('pp.type = :type')
            ->andWhere('pp.supersededBy IS NULL')
            ->setParameter('foodItem', $foodItem)
            ->setParameter('supermarket', $supermarket)
            ->setParameter('type', PlacementType::SYSTEM)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findActiveOtherUserPlacement(
        FoodItem $foodItem,
        Supermarket $supermarket,
        User $user,
    ): ?ProductPlacement {
        return $this->createQueryBuilder('pp')
            ->andWhere('pp.foodItem = :foodItem')
            ->andWhere('pp.supermarket = :supermarket')
            ->andWhere('pp.type = :type')
            ->andWhere('pp.suggestedBy != :user')
            ->andWhere('pp.supersededBy IS NULL')
            ->setParameter('foodItem', $foodItem)
            ->setParameter('supermarket', $supermarket)
            ->setParameter('user', $user)
            ->setParameter('type', PlacementType::USER)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findActiveGroupPlacement(
        FoodItem $foodItem,
        Supermarket $supermarket,
    ): ?ProductPlacement {
        return $this->createQueryBuilder('pp')
            ->andWhere('pp.foodItem = :foodItem')
            ->andWhere('pp.supermarket = :supermarket')
            ->andWhere('pp.type = :type')
            ->andWhere('pp.supersededBy IS NULL')
            ->setParameter('foodItem', $foodItem)
            ->setParameter('supermarket', $supermarket)
            ->setParameter('type', PlacementType::GROUP)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPreferredPlacement(
        FoodItem $foodItem,
        Supermarket $supermarket
    ): ?ProductPlacement {
        return $this->createQueryBuilder('pp')
            ->andWhere('pp.foodItem = :foodItem')
            ->andWhere('pp.supermarket = :supermarket')
            ->andWhere('pp.supersededBy IS NULL')
            ->andWhere('pp.type IN (:types)')
            ->setParameter('foodItem', $foodItem)
            ->setParameter('supermarket', $supermarket)
            ->setParameter('types', [
                PlacementType::SYSTEM,
                PlacementType::USER,
            ])
            ->addOrderBy(
                'CASE WHEN pp.type = :systemType THEN 0 ELSE 1 END'
            )
            ->setParameter('systemType', PlacementType::SYSTEM)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //------------------------------------
    public function findDonorSupermarkets(int $foodItemId): array
    {
        return $this->createQueryBuilder('p')
            ->select('IDENTITY(p.supermarket) as supermarketId')
            ->where('p.foodItem = :foodItem')
            ->setParameter('foodItem', $foodItemId)
            ->groupBy('p.supermarket')
            ->getQuery()
            ->getScalarResult();
    }

    public function findPlacementsOnEdge(int $edgeId, int $excludeFoodId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.edge = :edge')
            ->andWhere('p.foodItem != :food')
            ->setParameter('edge', $edgeId)
            ->setParameter('food', $excludeFoodId)
            ->getQuery()
            ->getResult();
    }

    public function findPlacementForFoodInStore(int $foodId, int $supermarketId): ?ProductPlacement
    {
        return $this->createQueryBuilder('p')
            ->where('p.foodItem = :food')
            ->andWhere('p.supermarket = :store')
            ->setParameter('food', $foodId)
            ->setParameter('store', $supermarketId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
