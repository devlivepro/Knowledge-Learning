<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class NoCacheListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // List of sensitive routes without cache
        $routesNoCache = [
            'app_login',
            'app_register',
            'app_forgot_password_request',
        ];

        if (in_array($request->attributes->get('_route'), $routesNoCache, true)) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
    }
}
