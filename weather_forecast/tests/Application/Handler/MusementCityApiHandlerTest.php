<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Handler\MusementCityApiHandler;
use App\Utils\RequestParams;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MusementCityApiHandlerTest extends TestCase
{
    private MockObject $httpClient;
    private MockObject $serializer;
    private MockObject $response;

    public function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    public function testEmptyApiResponse(): void
    {
        $this->response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $this->response->method('getContent')->willReturn('');

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $apiHandler = new MusementCityApiHandler($this->httpClient, $this->serializer, '');
        $response = $apiHandler->fetch();

        self::assertEmpty($response);
    }

    public function testHttpExceptionResponse(): void
    {
        $this->expectExceptionMessage(sprintf('Cities request finished with error HTTP code: %d', Response::HTTP_INTERNAL_SERVER_ERROR));

        $this->response->method('getStatusCode')->willReturn(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->response->method('getContent')->willReturn('');

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $apiHandler = new MusementCityApiHandler($this->httpClient, $this->serializer, '');
        $response = $apiHandler->fetch();

        self::assertEmpty($response);
    }

    public function testResponseWithData(): void
    {
        $this->response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $this->response->method('getContent')->willReturn('');

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $apiHandler = new MusementCityApiHandler($this->httpClient, $this->serializer, '');
        $response = $apiHandler->fetch();

        self::assertEmpty($response);
    }
}
