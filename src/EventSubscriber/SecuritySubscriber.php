<?php

namespace App\EventSubscriber;

use App\Services\UserLogServ;
use App\Services\UserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

/**
 * Class SecuritySubscriber
 * @package App\EventSubscriber
 */
class SecuritySubscriber implements EventSubscriberInterface
{
    /** @var  RequestStack */
    private $requestStack;

    /** @var UserLogServ */
    private $userLogServ;

    /** @var string|null */
    private $route;

    /**
     * SecuritySubscriber constructor.
     * @param RequestStack $requestStack
     * @param UserLogServ $userLogServ
     */
    public function __construct(RequestStack $requestStack, UserLogServ $userLogServ)
    {
        $this->requestStack = $requestStack;
        $this->userLogServ  = $userLogServ;
        $this->route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
    }

    public function onSecurityAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        if ($this->route === 'api_login') {
            $this->userLogServ->securityLoginAuthenticationSuccess($event->getAuthenticationToken()->getUser());
        }
    }

    public function onSecurityAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $credentials = $event->getAuthenticationToken()->getCredentials();

        if ($this->route === 'api_login') {
            if(array_key_exists('email', $credentials)) {
                $this->userLogServ->securityLoginAuthenticationFailure($credentials['email']);
            }
        }

        if(array_key_exists('token', $credentials)) {}

    }

    public static function getSubscribedEvents()
    {
        return [
            'security.authentication.success' => 'onSecurityAuthenticationSuccess',
            'security.authentication.failure' => 'onSecurityAuthenticationFailure',
        ];
    }
}
