<?php

namespace App\Repository;

use App\Entity\Edge;
use App\Entity\Supermarket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Edge>
 */
class EdgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Edge::class);
    }

    public function findAllInSupermarket(Supermarket $supermarket)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.supermarket = :supermarket')
            ->setParameter('supermarket', $supermarket)
            ->getQuery()
            ->getResult()
        ;
    }
}
