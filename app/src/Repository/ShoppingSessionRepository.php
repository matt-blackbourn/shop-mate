<?php

namespace App\Repository;

use App\Entity\ShoppingList;
use App\Entity\ShoppingSession;
use App\Entity\Supermarket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShoppingSession>
 */
class ShoppingSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShoppingSession::class);
    }

    public function findActiveByListAndSupermarket(
        ShoppingList $list,
        Supermarket $supermarket
    ): ?ShoppingSession {
        return $this->createQueryBuilder('s')
            ->where('s.shoppingList = :list')
            ->andWhere('s.supermarket = :supermarket')
            ->andWhere('s.completedAt IS NULL')
            ->setParameter('list', $list)
            ->setParameter('supermarket', $supermarket)
            ->orderBy('s.startedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findRecentActiveByListAndSupermarket(
        ShoppingList $list,
        Supermarket $supermarket,
    ): ?ShoppingSession {
        $cutoff =  new \DateTimeImmutable('-1 hour');

        return $this->createQueryBuilder('s')
            ->where('s.shoppingList = :list')
            ->andWhere('s.supermarket = :supermarket')
            ->andWhere('s.completedAt IS NULL')
            ->andWhere('s.startedAt <= :cutoff')
            ->setParameter('list', $list)
            ->setParameter('supermarket', $supermarket)
            ->setParameter('cutoff', $cutoff)
            ->orderBy('s.startedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
