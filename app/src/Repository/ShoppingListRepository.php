<?php

namespace App\Repository;

use App\Entity\ShoppingList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShoppingList>
 */
class ShoppingListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShoppingList::class);
    }

    public function findOneByUser($user): ?ShoppingList
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.quickAddList = false')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findQuickAddListByUser($user): ?ShoppingList
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.quickAddList = true')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
