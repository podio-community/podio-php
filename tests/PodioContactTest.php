<?php

namespace Podio\Tests;

use PodioContact;
use PHPUnit\Framework\TestCase;

class PodioContactTest extends TestCase
{
    public function testGetWithSingleProfileId()
    {
        $mockClient = $this->createMock(\PodioClient::class);
        $podioResponse = new \PodioResponse();
        $podioResponse->body = '{"profile_id": 42, "name": "John Doe"}';
        $mockClient->method('get')->willReturn($podioResponse);
        $result = PodioContact::get($mockClient, 42);
        $this->assertInstanceOf(PodioContact::class, $result);
        $this->assertEquals(42, $result->profile_id);
    }

    public function testGetWithMultipleProfileIds()
    {
        $mockClient = $this->createMock(\PodioClient::class);
        $podioResponse = new \PodioResponse();
        $podioResponse->body = '[{"profile_id": 42, "name": "John Doe"}, {"profile_id": 43, "name": "Jane Doe"}]';
        $mockClient->method('get')->with("/contact/42,43/v2")->willReturn($podioResponse);
        $result = PodioContact::get($mockClient, [42, 43]);

        $this->assertEquals(2, count($result));
        $this->assertInstanceOf(PodioContact::class, $result[0]);
        $this->assertInstanceOf(PodioContact::class, $result[1]);
        $this->assertEquals(42, $result[0]->profile_id);
        $this->assertEquals(43, $result[1]->profile_id);
    }
}
