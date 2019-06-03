<?php
declare(strict_types=1);


namespace App\Listener;

use App\Authentication\AuthenticationHandler;
use FOS\OAuthServerBundle\Model\AccessTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class KernelEventListener
{
    /**
     * @var AccessTokenManagerInterface
     */
    private $tokenStorage;

    /**
     * @var AuthenticationHandler
     */
    private $protectedAware;

    /**
     * @param AccessTokenManagerInterface $tokenStorage
     * @param AuthenticationHandler $protectedAware
     */
    public function __construct(AccessTokenManagerInterface $tokenStorage, AuthenticationHandler $protectedAware)
    {
        $this->tokenStorage = $tokenStorage;
        $this->protectedAware = $protectedAware;
    }


    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        $body['error'] = 'An error occurred.';
        if ($event->getRequest()->server->get('APP_ENV') === 'dev') {
            $body['exception'] = $exception->getMessage();
        }
        $response = new JsonResponse($body);

        $event->setResponse($response);
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if ($event->getRequest()->server->has('HTTP_AUTHORIZATION')) {
            $bearer = str_replace('Bearer ', '', $event->getRequest()->server->get('HTTP_AUTHORIZATION'));
            $token = $this->tokenStorage->findTokenByToken($bearer);
            if ($token && strpos($event->getRequest()->getPathInfo(), '/cv') === 0) {
                $userId = $token->getUser();
                $this->protectedAware->setUserName($userId->getUsername());
            }
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event): void
    {
        if ($event->getRequest()->getMethod() === 'OPTIONS') {
            $event->setResponse(
                new Response(
                    '', 204, [
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Credentials' => 'true',
                    'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                    'Access-Control-Allow-Headers' => 'DNT, X-User-Token, Keep-Alive, User-Agent, X-Requested-With, If-Modified-Since, Cache-Control, Content-Type',
                    'Access-Control-Max-Age' => 1728000,
                    'Content-Type' => 'text/plain charset=UTF-8',
                    'Content-Length' => 0,
                ]
                )
            );

            return;
        }
    }
}
