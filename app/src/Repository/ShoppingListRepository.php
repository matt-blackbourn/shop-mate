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

    public function findAllOrderedByRecent(): array
    {
        return $this->createQueryBuilder('sl')
            // ->addSelect('COALESCE(sl.dateCompleted, sl.dateCreated) AS HIDDEN sortDate')
            ->addSelect('sl.dateModified AS HIDDEN sortDate')
            ->orderBy('sortDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
