<?php

namespace Podio\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PodioClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

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

    public function test_should_retry_after_401_and_token_refresh_and_return_stream()
    {
        $httpClientMock = $this->createMock(Client::class);
        $httpClientMock->expects($this->exactly(3))->method('send')->willReturnOnConsecutiveCalls(
            new Response(401, [], Utils::streamFor('{"error_description": "expired_token"}')),
            new Response(200, [], Utils::streamFor('{"access_token": "new-token", "refresh_token": "new-refresh-token", "expires_in": 42000, "ref": "test-ref", "scope": "test-scope"}')),
            new Response(200, [], Utils::streamFor('{"items": []}'))
        );
        $client = new PodioClient('test-client', 'test-secret');
        $client->oauth = new \PodioOAuth('test-token', 'test-refresh-token');
        $client->http_client = $httpClientMock;

        $result = $client->request('GET', '/test', [], ['return_raw_as_resource_only' => true]);

        $this->assertInstanceOf(StreamInterface::class, $result);
        $this->assertEquals('{"items": []}', $result->getContents());
    }

    public function test_throw_exception_on_400()
    {
        $client = new PodioClient('test-client', 'test-secret');
        $httpClientMock = $this->createMock(Client::class);
        $httpClientMock->method('send')->willReturn(new Response(400, [], Utils::streamFor('{"error": "some reason"}')));
        $client->http_client = $httpClientMock;

        $this->expectException(\PodioBadRequestError::class);
        $client->get('/test');
    }

    public function test_throw_exception_on_400_when_return_raw_as_resource_only_is_true()
    {
        $client = new PodioClient('test-client-id', 'test-client-secret');
        $httpClientMock = $this->createMock(Client::class);
        $httpClientMock->method('send')->willReturn(new Response(400, [], Utils::streamFor('{"error": "some reason"}')));
        $client->http_client = $httpClientMock;

        $this->expectException(\PodioBadRequestError::class);
        $client->get('/test', [], ['return_raw_as_resource_only' => true]);
    }

    public function test_throw_exception_with_body_on_500()
    {
        $client = new PodioClient('test-client-id', 'test-client-secret');
        $httpClientMock = $this->createMock(Client::class);
        $httpClientMock->method('send')->willReturn(new Response(500, [], Utils::streamFor('{"error": "some reason"}')));
        $client->http_client = $httpClientMock;

        try {
            $client->get('/test');
            $this->fail('Exception not thrown');
        } catch (\PodioServerError $e) {
            $this->assertEquals(['error' => 'some reason'], $e->body);
        }
    }

    public function test_throw_exception_with_body_on_500_when_return_raw_as_resource_only_is_true()
    {
        $client = new PodioClient('test-client-id', 'test-client-secret');
        $httpClientMock = $this->createMock(Client::class);
        $httpClientMock->method('send')->willReturn(new Response(500, [], Utils::streamFor('{"error": "some reason"}')));
        $client->http_client = $httpClientMock;

        try {
            $client->get('/test', [], ['return_raw_as_resource_only' => true]);
            $this->fail('Exception not thrown');
        } catch (\PodioServerError $e) {
            $this->assertEquals(['error' => 'some reason'], $e->body);
        }
    }
}

class TestSessionManager
{
    public function set($oauth, $authType)
    {
        // empty method stub
    }

    public function get()
    {
        // empty method stub
    }
}
