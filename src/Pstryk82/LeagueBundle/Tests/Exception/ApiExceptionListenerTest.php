<?php

namespace Pstryk82\LeagueBundle\Tests\Exception;

use Pstryk82\LeagueBundle\Exception\ApiExceptionListener;
use Pstryk82\LeagueBundle\Exception\LeagueNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ApiExceptionListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApiExceptionListener
     */
    private $listener;

    /**
     * @var GetResponseForExceptionEvent
     */
    private $event;

    public function setUp()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $requestMock = $this->createMock(Request::class);
        $requestType = HttpKernelInterface::MASTER_REQUEST;
        $exceptionMock = $this->createMock(\Exception::class);
        $this->event = new GetResponseForExceptionEvent($kernelMock, $requestMock, $requestType, $exceptionMock);
        $this->listener = new ApiExceptionListener();
    }

    public function tearDown()
    {
        unset($this->listener, $this->event);
    }

    public function testOnKernelException()
    {
        $exception = new LeagueNotFoundException('Inexistent league');
        $this->event->setException($exception);

        $this->listener->onKernelException($this->event);

        $response = $this->event->getResponse();
        $this->assertEquals('{"exceptionMessage":"Inexistent league"}', $response->getContent());
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertArraySubset(
            ['content-type' => ['application/json']],
            $response->headers->all()
        );
    }

    public function testOnKernelExceptionUnsupportedException()
    {
        $exception = new \RuntimeException('other exception');
        $this->event->setException($exception);

        $this->listener->onKernelException($this->event);

        $this->assertNull($this->event->getResponse());
    }
}
