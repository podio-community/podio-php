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

    public function test_url_with_options_for_known_options()
    {
        $client = new PodioClient('test-client-id', 'test-client-secret');
        $url = $client->url_with_options('/test', ['silent' => true, 'hook' => false, 'fields' => 'foo,bar']);
        $this->assertEquals('/test?silent=1&hook=false&fields=foo,bar', $url);
    }

    public function test_url_with_options_ignores_unknown_options()
    {
        $client = new PodioClient('test-client-id', 'test-client-secret');
        $url = $client->url_with_options('/test', ['foo' => 'bar']);
        $this->assertEquals('/test', $url);
    }

    public function test_clear_authentication_clears_oauth()
    {
        $client = new PodioClient('test-client-id', 'test-client-secret');
        $client->oauth = new \PodioOAuth('test-token');

        $client->clear_authentication();

        $this->assertEquals(new \PodioOAuth(), $client->oauth);
    }

    public function test_clear_authentication_sets_session_manger_auth()
    {
        $session_manager_mock = $this->createMock(TestSessionManager::class);
        $session_manager_mock->expects($this->atLeast(2))->method('set')->with($this->equalTo(new \PodioOAuth()), $this->equalTo(['type' => 'password']));
        $session_manager_mock->expects($this->once())->method('get');
        $client = new PodioClient('test-client-id', 'test-client-secret', ['session_manager' => $session_manager_mock]);
        $client->oauth = new \PodioOAuth('test-token');
        $client->auth_type = ['type' => 'password'];

        $client->clear_authentication();
    }
}

class TestSessionManager
{
    public function set($oauth, $authType)
    {
    }

    public function get()
    {
    }
}

