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

    public function findByShoppingList(ShoppingList $shoppingList): array {
        return $this->createQueryBuilder('li')
            ->addSelect('fi')
            ->innerJoin('li.foodItem', 'fi')
            ->where('li.shoppingList = :shoppingList')
            ->andWhere('li.pickedAt IS NULL')
            ->setParameter('shoppingList', $shoppingList)
            ->addOrderBy('fi.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
