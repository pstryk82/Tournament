<?php

namespace Pstryk82\LeagueBundle\Exception;


use Symfony\Component\HttpFoundation\Response;

class LeagueNotFoundException extends ApiException
{
    /**
     * @var int
     */
    protected $statusCode = Response::HTTP_NOT_FOUND;
}
