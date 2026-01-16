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

    /**
     * select f.id, COALESCE (userfooditem.customName, f.name) as name
     * COALESCE ufo.customCategory_id, f.category_id as category_id
     * FROM food_item f
     * INNER JOIN user_food_item ufo ON ufo.food_item_id = f.id AND ufo.user_id = ?
     * Where f.owner_id = ='system' OR f.owner_id = 'user' and f.user_id = :userid
     */
    //    public function findAllFoodsByUser($user): ?FoodItem
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
