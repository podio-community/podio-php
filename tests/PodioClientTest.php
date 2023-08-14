<?php

namespace Podio\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PodioClient;
use PHPUnit\Framework\TestCase;

class PodioClientTest extends TestCase
{
    public function testDebugToStdOut()
    {
        $client = new PodioClient('test-client-id', 'test-client-secret');
        $client->set_debug(true);
        $httpClientMock = $this->createMock(Client::class);
        $httpClientMock->method('send')->willReturn(new Response(200, [], '{"message": "OK"}'));
        $client->http_client = $httpClientMock;

        $client->get('/test');

        $this->expectOutputRegex('/200 GET \/test.*{"message": "OK"}/ms');
    }
}
