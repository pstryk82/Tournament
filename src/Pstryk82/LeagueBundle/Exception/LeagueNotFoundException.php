<?php

namespace Pstryk82\LeagueBundle\Exception;


use Symfony\Component\HttpFoundation\Response;

class LeagueNotFoundException extends ApiException
{
    protected $statusCode = Response::HTTP_NOT_FOUND;
}
