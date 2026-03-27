<?php

namespace App\Repository;

use App\Entity\Household;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Household>
 */
class HouseholdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Household::class);
    }

    public function findForUserOrdered(User $user): array
    {
        return $this->createQueryBuilder('h')
            ->innerJoin('h.householdMembers', 'hm')
            ->where('hm.user = :user')
            ->setParameter('user', $user)
            ->addSelect('CASE WHEN h = :defaultHousehold THEN 0 ELSE 1 END AS HIDDEN priority')
            ->setParameter('defaultHousehold', $user->getDefaultHousehold())
            ->orderBy('priority', 'ASC')
            ->addOrderBy('h.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
