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

    public function findCurrentSession($listId, $supermarketId){
        $qb = $this->createQueryBuilder('s')
            ->where('s.shoppingList = :list')
            ->andWhere('s.completedAt IS NULL')
            ->setParameter('list', $listId)
            ->orderBy('s.startedAt', 'DESC')
            ->setMaxResults(1);
    
        if ($supermarketId === null || $supermarketId == 0) {
            $qb->andWhere('s.supermarket IS NULL');
        } else {
            $qb->andWhere('s.supermarket = :supermarket')
               ->setParameter('supermarket', $supermarketId);
        }
    
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findRecentActiveByListAndSupermarket($listId, $supermarketId){
        $cutoff =  new \DateTimeImmutable('-1 hour');

        $qb = $this->createQueryBuilder('s')
            ->where('s.shoppingList = :list')
            ->andWhere('s.completedAt IS NULL')
            ->andWhere('s.startedAt <= :cutoff')
            ->setParameter('list', $listId)
            ->setParameter('cutoff', $cutoff)
            ->orderBy('s.startedAt', 'DESC');

            if ($supermarketId === null || $supermarketId == 0) {
                $qb->andWhere('s.supermarket IS NULL');
            } else {
                $qb->andWhere('s.supermarket = :supermarket')
                   ->setParameter('supermarket', $supermarketId);
            }
        
            return $qb->getQuery()->getResult();
    }
}
