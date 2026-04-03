<?php

namespace App\Repository;

use App\Entity\ShoppingSession;
use App\Entity\Supermarket;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Supermarket>
 */
class SupermarketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Supermarket::class);
    }

    public function findActiveMappedSupermarkets(User $user): array
    {
        $qb = $this->createQueryBuilder('s');

        return $qb
            ->leftJoin(ShoppingSession::class, 'sess', 'WITH', 'sess.supermarket = s')
            ->where('s.active = true')
            ->andWhere('s.entranceNode IS NOT NULL')

            ->andWhere($qb->expr()->exists('SELECT 1 FROM App\Entity\Edge e WHERE e.supermarket = s'))
            ->andWhere($qb->expr()->exists('SELECT 1 FROM App\Entity\Node n WHERE n.supermarket = s'))
            ->andWhere($qb->expr()->exists('SELECT 1 FROM App\Entity\Shelf sh WHERE sh.supermarket = s'))
            ->groupBy('s.id')

            ->addSelect('COUNT(sess.id) AS HIDDEN sessionCount')
            ->addSelect('CASE WHEN s.id = :defaultId THEN 1 ELSE 0 END AS HIDDEN isDefault')

            ->setParameter('defaultId', $user->getDefaultSupermarket()?->getId())

            ->orderBy('isDefault', 'DESC')      // default first
            ->addOrderBy('sessionCount', 'DESC') // then most sessions

            ->getQuery()->getResult();
    }

    public function findWithMostPlacements(): ?Supermarket
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.productPlacements', 'pp') // assumes relation exists
            ->groupBy('s.id')
            ->orderBy('COUNT(pp.id)', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
