<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserToken[]    findAll()
 * @method UserToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserToken::class);
    }

    public function clearSession(User $user, $id)
    {
        $r = $this->createQueryBuilder('c')
            ->update()
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->andWhere('c.user = :user')
            ->setParameter('user', $user->getId())
            ->set('c.isActive', ':isActive')
            ->setParameter('isActive', false)
            ->getQuery()->execute();
    }

    public function clearAllSession(User $user)
    {
        $r = $this->createQueryBuilder('c')
            ->update()
            ->andWhere('c.user = :user')
            ->setParameter('user', $user->getId())
            ->set('c.isActive', ':isActive')
            ->setParameter('isActive', false)
            ->getQuery()->execute();
    }

    public function getItems(User $user)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user->getId())
            ->andWhere('c.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }


    public function isActiveToken($userId, $id, $token)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->andWhere('c.user = :user')
            ->setParameter('user', $userId)
            ->andWhere('c.token = :token')
            ->setParameter('token', $token)
            ->andWhere('c.isActive = :isActive')
            ->setParameter('isActive', true)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
