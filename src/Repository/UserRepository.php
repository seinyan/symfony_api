<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param $username
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($email)
    {

        return $this->createQueryBuilder('c')
//            ->andWhere('c.isRemoved = :isRemoved')
//            ->setParameter('isRemoved', false)
            ->andWhere('c.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $token
     * @param $id
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByToken($token, $id)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->andWhere('c.isActive = :isActive')
            ->setParameter('isActive', true)
            ->andWhere('c.token = :token')
            ->setParameter('token', $token)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Request $request
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listAction(Request $request)
    {
        $qb = $this->createQueryBuilder('c');

        if ($request->query->get('role')) {
            $qb
                ->andWhere('c.role = :role')
                ->setParameter('role', $request->query->get('role'));
        }
        if ($request->query->get('moderation_status')) {
            $qb
                ->andWhere('c.moderationStatus = :moderation_status')
                ->setParameter('moderation_status', $request->query->get('moderation_status'));
        }

        return  $qb->orderBy('c.registeredAt', 'DESC');
    }

}
