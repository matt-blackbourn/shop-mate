<?php

namespace App\Repository;

use App\Entity\FoodItem;
use App\Entity\ProductLocation;
use App\Entity\Supermarket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductLocation>
 */
class ProductLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductLocation::class);
    }

    public function findOneByFoodAndSupermarket(FoodItem $food, Supermarket $supermarket): ?ProductLocation
    {
        return $this->createQueryBuilder('pl')
            ->innerJoin('pl.edge', 'e')
            ->innerJoin('e.supermarket', 's')
            ->where('pl.foodItem = :food')
            ->andWhere('s.id = :supermarket')
            ->setParameter('food', $food)        // Use setParameter, singular
            ->setParameter('supermarket', $supermarket) // Use the entity itself
            ->getQuery()
            ->getOneOrNullResult();
    }
}
