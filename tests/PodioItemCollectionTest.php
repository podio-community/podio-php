<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioItem;
use PodioItemCollection;
use PodioObject;

class PodioItemCollectionTest extends TestCase
{
    private $mockClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockClient = $this->createMock(\PodioClient::class);
    }
    public function test_cannot_add_object(): void
    {
        $this->expectException('PodioDataIntegrityError');
        $collection = new PodioItemCollection($this->mockClient);
        $collection[] = new PodioObject($this->mockClient);
    }

    public function test_can_add_item(): void
    {
        $collection = new PodioItemCollection($this->mockClient);
        $length = count($collection);
        $collection[] = new PodioItem($this->mockClient, ['item_id' => 1, 'external_id' => 'a']);

        $this->assertCount($length + 1, $collection);
    }
}
