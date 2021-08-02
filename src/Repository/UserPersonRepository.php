<?php

namespace App\Repository;

use App\Entity\UserPerson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserPerson|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPerson|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPerson[]    findAll()
 * @method UserPerson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPerson::class);
    }

    // /**
    //  * @return UserPerson[] Returns an array of UserPerson objects
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
    public function findOneBySomeField($value): ?UserPerson
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
