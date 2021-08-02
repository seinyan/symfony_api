<?php

namespace App\Repository;

use App\Entity\UserAgentData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAgentData|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAgentData|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAgentData[]    findAll()
 * @method UserAgentData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAgentDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAgentData::class);
    }

    // /**
    //  * @return UserAgentData[] Returns an array of UserAgentData objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserAgentData
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
