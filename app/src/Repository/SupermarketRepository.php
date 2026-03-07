<?php

namespace App\Repository;

use App\Entity\Supermarket;
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

    public function findActiveMappedSupermarkets(): array
    {
        $qb = $this->createQueryBuilder('s');

        return $qb
            ->where('s.active = true')
            ->andWhere('s.entranceNode IS NOT NULL')

            ->andWhere($qb->expr()->exists(
                'SELECT 1 FROM App\Entity\Edge e WHERE e.supermarket = s'
            ))
            ->andWhere($qb->expr()->exists(
                'SELECT 1 FROM App\Entity\Node n WHERE n.supermarket = s'
            ))
            ->andWhere($qb->expr()->exists(
                'SELECT 1 FROM App\Entity\Shelf sh WHERE sh.supermarket = s'
            ))

            ->getQuery()
            ->getResult();
    }
}
