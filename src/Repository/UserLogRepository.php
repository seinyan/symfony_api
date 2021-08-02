<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLog[]    findAll()
 * @method UserLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLog::class);
    }

    public function listAction(User $user)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user->getId())
            ->orderBy('c.id', 'DESC')
        ;
    }
}
