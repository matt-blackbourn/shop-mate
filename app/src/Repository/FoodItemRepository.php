<?php

namespace App\Repository;

use App\Entity\FoodItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FoodItem>
 */
class FoodItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoodItem::class);
    }

    //    /**
    //     * @return FoodItem[] Returns an array of FoodItem objects
    //     */
    public function findAllByName(): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findAllGroupedByArea(): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.area', 'ASC')
            ->innerJoin('f.area', 'a')
            ->addOrderBy('a.orderBy', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    //    public function findOneBySomeField($value): ?FoodItem
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
