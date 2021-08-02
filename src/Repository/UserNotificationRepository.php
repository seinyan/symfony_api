<?php

namespace App\Repository;

use App\Entity\UserNotification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserNotification[]    findAll()
 * @method UserNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserNotification::class);
    }


    public function getItems(User $user)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isRead = :is_read')
            ->setParameter('is_read', false)
            ->andWhere('c.user = :user_id')
            ->setParameter('user_id', $user->getId())
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }



    public function listAction()
    {
        return $this->createQueryBuilder('c')

        ;
    }

}
