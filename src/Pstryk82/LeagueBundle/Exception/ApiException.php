<?php

namespace Pstryk82\LeagueBundle\Exception;


use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiException extends AbstractException implements HttpExceptionInterface
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var array
     */
    protected $headers = [
        'content-type' => 'application/json'
    ];

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
