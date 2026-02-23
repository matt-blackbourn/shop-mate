<?php

namespace App\Repository;

use App\Entity\ListItem;
use App\Entity\ShoppingList;
use App\Entity\ShoppingSession;
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

    public function findLastPicked(ShoppingList $shoppingList): ?ListItem {
        return $this->createQueryBuilder('li')
            ->where('li.shoppingList = :list')
            ->andWhere('li.pickedAt >= :today')
            ->setParameter('list', $shoppingList)
            ->setParameter('today', new \DateTimeImmutable('today'))
            ->orderBy('li.pickedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLastPickedInSession(ShoppingSession $session): ?ListItem
    {
        return $this->createQueryBuilder('li')
            ->where('li.session = :session')
            ->andWhere('li.pickedAt IS NOT NULL')
            ->setParameter('session', $session)
            ->orderBy('li.pickedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
