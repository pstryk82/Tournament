<?php

namespace Pstryk82\LeagueBundle\Exception;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ApiExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if (!$exception instanceof ApiException) {
            return;
        }
        $content = [
            'exceptionMessage' => $exception->getMessage(),
        ];

        $response = new JsonResponse($content, $exception->getStatusCode(), $exception->getHeaders());

        $event->setResponse($response);
    }
}
