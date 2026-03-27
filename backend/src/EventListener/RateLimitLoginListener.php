<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 10)]
class RateLimitLoginListener
{
    public function __construct(
        private readonly RateLimiterFactory $loginLimiterFactory,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getPathInfo() !== '/api/auth/login' || $request->getMethod() !== 'POST') {
            return;
        }

        $limiter = $this->loginLimiterFactory->create($request->getClientIp() ?? 'unknown');
        $limit = $limiter->consume(1);

        if (!$limit->isAccepted()) {
            $event->setResponse(new JsonResponse(
                [
                    'message' => 'Trop de tentatives de connexion. Réessayez dans 15 minutes.',
                    'retry_after' => $limit->getRetryAfter()->getTimestamp(),
                ],
                Response::HTTP_TOO_MANY_REQUESTS
            ));
        }
    }
}
