<?php

namespace App\Services;

use App\Entity\UserPerson;
use App\Entity\UserToken;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\DateTime;

class UserService
{
    /** @var EntityManager */
    private $em;

    /** @var UserPasswordEncoderInterface  */
    private $passwordEncoder;

    /** @var EmailService */
    private $emailService;

    /** @var JWTService */
    public $JWTService;

    /** @var UserLogServ  */
    private $userLogServ;

    /**
     * UserService constructor.
     * @param EmailService $emailService
     * @param JWTService $JWTService
     * @param EntityManager $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserLogServ $userLogServ
     */
    public function __construct(
        EmailService $emailService, JWTService $JWTService, EntityManager $entityManager,
        UserPasswordEncoderInterface $passwordEncoder, UserLogServ $userLogServ
    )
    {
        $this->JWTService      = $JWTService;
        $this->emailService    = $emailService;
        $this->em              = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->userLogServ     = $userLogServ;
    }


    /**
     * @param User $user
     */
    public function clearAllSession(User $user):void
    {
       $this->em->getRepository(UserToken::class)->clearAllSession($user);

        $this->userLogServ->clearAllSession($user);
    }


    /**
     * @param User $user
     * @param Request $request
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function logout(User $user, Request $request)
    {
        $token = str_replace("Bearer", "", $request->headers->get('authorization'));
        $token = trim($token);

        $jwtData = $this->JWTService->decode($token);
        /** @var \App\Entity\UserToken $token */
        $token = $this->em->getRepository(UserToken::class)
            ->isActiveToken($jwtData['data']['u_id'], $jwtData['data']['t_id'], $jwtData['data']['key']);

        $token->setIsActive(false);
        $this->em->flush();

        $this->em->flush();
        $this->userLogServ->logout($user);
    }

    /**
     * @param User $user
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function register(User $user)
    {
        $em = $this->em;
        if(!$user->getPassword()) {
            $user->setPassword($this->generatePassword());
        }

        $user->setRole(User::ROLE_USER);
        $user->setRegisteredAt(new \DateTime());

        $password_form = $user->getPassword();
        $password = $this->encodePassword($user);
        $user->setPassword($password);

        if(!$user->getPerson()) {
            $userPerson = new UserPerson();
            $user->setPerson($userPerson);
            $userPerson->setUser($user);
            $em->persist($userPerson);
        }

        $em->persist($user);
        $em->flush();

        $this->userLogServ->register($user);

        $this->emailService->send(
            "Emails/security/register.html.twig",
            "Спасибо за регистрацию!",
            $user->getEmail(), [
                "password" => $password_form,
                "username" => $user->getUsername(),
            ]
        );

        return $user;
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function restore(User $user)
    {
        $newPassword = $this->generatePassword();

        $em = $this->em;
        $user->setPassword($newPassword);
        $password = $this->encodePassword($user);
        $user->setPassword($password);
        $em->flush();

        $this->userLogServ->restore($user);

        $this->emailService->send(
            "Emails/security/restore.html.twig",
            "Ваш новый пароль!",
            $user->getEmail(), [
                "password" => $newPassword,
                "username" => $user->getUsername(),
            ]
        );

        return true;
    }


    /**
     * @param User $user
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(User $user)
    {
        $em = $this->em;
        $em->flush();

        $this->userLogServ->update($user);

        return $user;
    }

    /**
     * @param User $user
     * @param $newPassword
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function updatePassword(User $user, $newPassword)
    {
        $em = $this->em;
        $user->setPassword($newPassword);
        $password = $this->encodePassword($user);
        $user->setPassword($password);
        $em->flush();

        $this->userLogServ->updatePass($user);

        $this->emailService->send(
            "Emails/security/restore.html.twig",
            "Ваш новый пароль!",
            $user->getEmail(), [
                "password" => $newPassword,
                "username" => $user->getUsername(),
            ]
        );

        return $user;
    }


    /**
     * @param User $user
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function registerByAdmin(User $user)
    {
        $em = $this->em;
        if(!$user->getPassword()) {
            $user->setPassword($this->generatePassword());
        }

        $user->setRegisteredAt(new \DateTime());

        $password_form = $user->getPassword();
        $password = $this->encodePassword($user);
        $user->setPassword($password);

        if(!$user->getPerson()) {
            $userPerson = new UserPerson();
            $user->setPerson($userPerson);
            $userPerson->setUser($user);
            $em->persist($userPerson);
        }

        $em->persist($user);
        $em->flush();

        $this->userLogServ->register($user);

        $this->emailService->send(
            "Emails/security/register.html.twig",
            "Спасибо за регистрацию!",
            $user->getEmail(), [
                "password" => $password_form,
                "username" => $user->getUsername(),
            ]
        );

        return $user;
    }


    /**
     * @param User $user
     * @return string
     */
    public function encodePassword(User $user)
    {
        return $this->passwordEncoder->encodePassword($user, $user->getPassword());
    }

    /** @return string */
    public function generatePassword()
    {
        if ($_ENV['APP_ENV'] === 'dev') {
            return '111111';
        }

        return uniqid();
    }


    /**
     * @param User $user
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function generateNewToken(User $user)
    {
        $userToken = new UserToken();
        $userToken->setIsActive(true);
        $userToken->setUserAgent($this->userLogServ->getUserAgent());
        $userToken->setLastAt(new \DateTime());
        $userToken->setUser($user);
        $userToken->setToken($user->getId().bin2hex(random_bytes(16)));

        $this->em->persist($userToken);
        $this->em->flush();

        $data = [
            'u_id' => $user->getId(),
            't_id' => $userToken->getId(),
            'key'  => $userToken->getToken(),
        ];

        $jwtToken = $this->JWTService->generateToken($data);

        return $jwtToken;
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