<?php

namespace App\Repository;

use App\Entity\Node;
use App\Entity\Supermarket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Node>
 */
class NodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Node::class);
    }

    //    /**
    //     * @return Node[] Returns an array of Node objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

       public function findLastNodeInSupermarket(Supermarket $supermarket): ?Node
       {
           return $this->createQueryBuilder('n')
                ->andWhere('n.supermarket = :supermarket')
                ->setParameter('supermarket', $supermarket)
                ->orderBy('n.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
           ;
       }
}
