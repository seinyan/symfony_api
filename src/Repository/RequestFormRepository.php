<?php

namespace App\Repository;

use App\Entity\RequestForm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RequestForm|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestForm|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestForm[]    findAll()
 * @method RequestForm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestFormRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestForm::class);
    }


    public function listAction()
    {
        return $this->createQueryBuilder('c')

        ;
    }


    /*
    public function findOneBySomeField($value): ?RequestForm
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
