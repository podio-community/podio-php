<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioCollection;
use PodioItem;
use PodioObject;

class PodioCollectionTest extends TestCase
{
    /**
     * @var \PodioCollection
     */
    protected $collection;

    private $mockClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockClient = $this->createMock(\PodioClient::class);

        $this->collection = new PodioCollection($this->mockClient);

        $external_ids = ['a', 'b', 'c'];
        for ($i = 1; $i < 4; $i++) {
            $item = new PodioItem($this->mockClient);
            $item->property('id', 'integer');
            $item->property('external_id', 'string');
            $item->init();

            $item->id = $i;
            $item->external_id = $external_ids[$i - 1];

            $this->collection[] = $item;
        }
    }

    public function test_can_get_by_offset(): void
    {
        $item = $this->collection[1];
        $this->assertSame(2, $item->id);
    }

    public function test_can_iterate(): void
    {
        $checklist = [1, 2, 3];
        foreach ($this->collection as $offset => $item) {
            $this->assertSame($checklist[$offset], $item->id);
        }
    }

    public function test_can_provide_length(): void
    {
        $this->assertCount(3, $this->collection);
    }

    public function test_can_check_existence(): void
    {
        $this->assertTrue(isset($this->collection[0]));
        $this->assertFalse(isset($this->collection[3]));
    }

    public function test_cannot_add_string(): void
    {
        $this->expectException('PodioDataIntegrityError');
        $this->collection[] = 'Sample String';
    }

    public function test_can_add_object(): void
    {
        $length = count($this->collection);
        $this->collection[] = new PodioObject($this->mockClient);

        $this->assertCount($length + 1, $this->collection);
    }

    public function test_can_remove_by_offset(): void
    {
        unset($this->collection[0]);
        $this->assertCount(2, $this->collection);
        $this->assertFalse(isset($this->collection[0]));
    }

    public function test_cannot_access_by_id_after_remove_by_offset(): void
    {
        unset($this->collection[0]);
        $this->assertCount(2, $this->collection);
        $this->assertFalse(isset($this->collection[0]));
        $this->assertNull($this->collection->get('a'));
        $this->assertNull($this->collection->get(1));
    }

    public function test_can_remove_by_id(): void
    {
        $this->collection->remove(1);
        $this->assertCount(2, $this->collection);
        $this->assertNull($this->collection->get(1));
    }

    public function test_can_remove_by_external_id(): void
    {
        $this->collection->remove('a');
        $this->assertCount(2, $this->collection);
        $this->assertNull($this->collection->get(1));
    }

    public function test_can_get_by_id(): void
    {
        $this->assertSame('b', $this->collection->get(2)->external_id);
    }

    public function test_can_get_by_external_id(): void
    {
        $this->assertSame(2, $this->collection->get('b')->id);
    }

    public function test_can_add_relationship(): void
    {
        $instance = new PodioObject($this->mockClient);

        $this->collection->add_relationship($instance);

        $relationship = $this->collection->relationship();
        $this->assertSame($instance, $relationship['instance']);
        $this->assertSame('fields', $relationship['property']);

        foreach ($this->collection as $object) {
            $relationship = $object->relationship();
            $this->assertSame($instance, $relationship['instance']);
            $this->assertSame('fields', $relationship['property']);
        }
    }
}
