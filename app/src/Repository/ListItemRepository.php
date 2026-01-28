<?php

namespace App\Repository;

use App\Entity\ListItem;
use App\Entity\ShoppingList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListItem>
 */
class ListItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListItem::class);
    }

    public function findByShoppingListOrderedByCategory(ShoppingList $shoppingList): array {
        return $this->createQueryBuilder('li')
            ->addSelect('fi', 'c')
            ->innerJoin('li.foodItem', 'fi')
            ->leftJoin('fi.category', 'c')
            ->where('li.shoppingList = :shoppingList')
            ->andWhere('li.pickedAt IS NULL')
            ->setParameter('shoppingList', $shoppingList)
            // Push NULL categories to the end
            ->orderBy('CASE WHEN c.id IS NULL THEN 1 ELSE 0 END', 'ASC')
            ->addOrderBy('c.orderBy', 'ASC')
            ->addOrderBy('fi.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
