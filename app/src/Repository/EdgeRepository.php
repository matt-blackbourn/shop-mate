<?php

namespace App\Repository;

use App\Entity\Edge;
use App\Entity\Node;
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

    public function findOneByNodes(Node|int|string $a, Node|int|string $b): ?Edge
    {
        $aId = $a instanceof Node ? $a->getId() : $a;
        $bId = $b instanceof Node ? $b->getId() : $b;

        return $this->createQueryBuilder('e')
            ->where('(e.start = :a AND e.end = :b)')
            ->orWhere('(e.start = :b AND e.end = :a)')
            ->setParameter('a', $aId)
            ->setParameter('b', $bId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
