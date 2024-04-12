<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AccessDeniedListener
{
    private $router;
    private $session;

    public function __construct(RouterInterface $router, SessionInterface $session)
    {
        $this->router = $router;
        $this->session = $session;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedHttpException || $exception instanceof AccessDeniedException) {
            // Agrega un mensaje de error a la sesión para mostrarlo más tarde
            $this->session->getFlashBag()->add('error', 'No está autorizado para acceder a esta página.');

            // Redirige al usuario a la página de inicio
            $response = new RedirectResponse($this->router->generate('app_main'));
            $event->setResponse($response);
        }
    }
}
