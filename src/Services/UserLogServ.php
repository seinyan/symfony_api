<?php

namespace App\Services;

use App\Entity\User;
use App\Entity\UserLog;
use App\Utils\UserAgentManger;
use Doctrine\ORM\EntityManager;

/**
 * Class UserLogServ
 * @package App\Services
 */
class UserLogServ
{
    /** @var UserAgentManger  */
    private $uaManger;

    /** @var EntityManager */
    private $em;

    /**
     * UserLogServ constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em       = $entityManager;
        $this->uaManger = new UserAgentManger();
    }


    public function getUserAgent()
    {
        return $this->uaManger->getUserAgentData();
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function clearAllSession(User $user):void
    {
        $log = new UserLog(UserLog::USER_CLEAR_ALL_SESSION);
        $log->setUser($user);
        $log->setUserAgent($this->uaManger->getUserAgentData());

        $this->em->persist($log);
        $this->em->flush();
    }


    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(User $user):void
    {
        $log = new UserLog(UserLog::USER_UPDATE);
        $log->setUser($user);
        $log->setUserAgent($this->uaManger->getUserAgentData());

        $this->em->persist($log);
        $this->em->flush();
    }


    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updatePass(User $user):void
    {
        $log = new UserLog(UserLog::USER_UPDATE_PASS);
        $log->setUser($user);
        $log->setUserAgent($this->uaManger->getUserAgentData());

        $this->em->persist($log);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function logout(User $user):void
    {
        $log = new UserLog(UserLog::USER_LOGOUT);
        $log->setUser($user);
        $log->setUserAgent($this->uaManger->getUserAgentData());

        $this->em->persist($log);
        $this->em->flush();
    }


    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function register(User $user):void
    {
        $log = new UserLog(UserLog::USER_REGISTER);
        $log->setUser($user);
        $log->setUserAgent($this->uaManger->getUserAgentData());

        $this->em->persist($log);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function restore(User $user):void
    {
        $log = new UserLog(UserLog::USER_RESTORE);
        $log->setUser($user);
        $log->setUserAgent($this->uaManger->getUserAgentData());

        $this->em->persist($log);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function securityLoginAuthenticationSuccess(User $user):void
    {
        $log = new UserLog(UserLog::SECURITY_LOGIN_SUCCESS);
        $log->setUser($user);
        $log->setUserAgent($this->uaManger->getUserAgentData());

        $this->em->persist($log);
        $this->em->flush();
    }

    /**
     * @param $userName
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function securityLoginAuthenticationFailure($userName):void
    {
        $log = new UserLog(UserLog::SECURITY_LOGIN_FAILURE);
        $log->setUser($this->getUser($userName));
        $log->setUserAgent($this->uaManger->getUserAgentData());

        $this->em->persist($log);
        $this->em->flush();
    }


    /**
     * @param $userName
     * @return mixed
     */
    public function getUser($userName)
    {
        return $this->Repository(User::class)->loadUserByUsername($userName);
    }

    /**
     * @param $model
     * @return \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository
     */
    public function Repository($model)
    {
        return $this->em->getRepository($model);
    }
}