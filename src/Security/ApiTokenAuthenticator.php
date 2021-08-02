<?php

namespace App\Security;

use App\AppConsts;
use App\Entity\User;
use App\Entity\UserNotification;
use App\Entity\UserToken;
use App\Services\JWTService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Validator\Constraints\DateTime;
use function Symfony\Component\Translation\t;


class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    /** @var EntityManager */
    private $em;

    /** @var JWTService */
    private $JWTService;


    /**
     * TokenAuthenticator constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, JWTService $JWTService)
    {
        $this->em         = $em;
        $this->JWTService = $JWTService;
    }

    public function supports(Request $request)
    {
        return $request->headers->has('authorization');
    }

    public function getCredentials(Request $request)
    {
        // todo
        $token = str_replace("Bearer", "", $request->headers->get('authorization'));
        $token = trim($token);

        return [
            'token' => $token,
//            '_security_main' => $request->getSession()->get('_security_main'),
            '_security.last_username' => $request->getSession()->get('_security.last_username'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!$credentials['_security.last_username'] || $credentials['_security.last_username'] === "") {
            //throw new CustomUserMessageAuthenticationException('session could not be found.');
        }

        if (!$credentials['token'] || $credentials['token'] === "") {
            throw new CustomUserMessageAuthenticationException('token could not be found.');
        }

        if(!$this->JWTService->validate($credentials['token'])) {
            throw new CustomUserMessageAuthenticationException('token inactive.');
        }


        $jwtData = $this->JWTService->decode($credentials['token']);
        /** @var \App\Entity\UserToken $token */
        $token = $this->em->getRepository(UserToken::class)
            ->isActiveToken($jwtData['data']['u_id'], $jwtData['data']['t_id'], $jwtData['data']['key']);

        if (!$token) {
            throw new CustomUserMessageAuthenticationException('token inactive.');
        }

        $token->setLastAt(new \DateTime());
        $this->em->flush();

        return $this->em->getRepository(User::class)->find($jwtData['data']['u_id']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // todo
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
//        dump('onAuthenticationFailure');

        // todo
        return new JsonResponse([
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ], AppConsts::CODE_FORBIDDEN_403);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // todo
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // todo
        return new JsonResponse(
            ['message' => 'Authentication Required'],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function supportsRememberMe()
    {
        // todo
        return false;
    }
}
