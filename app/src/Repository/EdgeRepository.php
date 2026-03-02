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

    public function getNextAvailableAisleKey(Supermarket $supermarket): int
    {
        // Fetch all aisle keys for this supermarket, ordered ascending
        $qb = $this->createQueryBuilder('e')
            ->select('e.aisleKey')
            ->where('e.supermarket = :supermarket')
            ->setParameter('supermarket', $supermarket)
            ->orderBy('e.aisleKey', 'ASC');

        $existingKeys = array_map('intval', array_column($qb->getQuery()->getArrayResult(), 'aisleKey'));

        $nextKey = 1;
        foreach ($existingKeys as $key) {
            if ($key === $nextKey) {
                $nextKey++;
            } elseif ($key > $nextKey) {
                break; // found a gap
            }
        }

        return $nextKey;
    }

    public function clearAisleKeys(Supermarket $supermarket): void
    {
        $qb = $this->createQueryBuilder('e')
            ->update()
            ->set('e.aisleKey', ':null')
            ->where('e.supermarket = :supermarket')
            ->setParameter('supermarket', $supermarket)
            ->setParameter('null', null);

        $qb->getQuery()->execute();
    }
}
