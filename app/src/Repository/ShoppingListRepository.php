<?php

namespace App\Repository;

use App\Entity\ShoppingList;
use App\Entity\User;
use App\Enum\ListType;
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


    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('sl')
            ->innerJoin('sl.listMembers', 'lm')
            ->where('lm.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findRecipesByUser(User $user): array
    {
        return $this->createQueryBuilder('sl')
            ->innerJoin('sl.listMembers', 'lm')
            ->where('lm.user = :user')
            ->andWhere('sl.type = :type')
            ->setParameter('type', ListType::RECIPE->value)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
