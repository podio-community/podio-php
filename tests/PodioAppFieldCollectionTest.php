<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioAppField;
use PodioAppFieldCollection;
use PodioItemField;

class PodioAppFieldCollectionTest extends TestCase
{
    public function setUp(): void
    {
        $this->collection = new PodioAppFieldCollection([
            new PodioAppField(['field_id' => 1, 'external_id' => 'a', 'type' => 'text']),
            new PodioAppField(['field_id' => 2, 'external_id' => 'b', 'type' => 'number']),
            new PodioAppField(['field_id' => 3, 'external_id' => 'c', 'type' => 'calculation']),
        ]);
    }

    public function test_can_construct_from_array()
    {
        $collection = new PodioAppFieldCollection([
            ['field_id' => 1],
            ['field_id' => 2],
            ['field_id' => 3],
        ]);
        $this->assertEquals(3, count($collection));
    }

    public function test_can_construct_from_objects()
    {
        $collection = new PodioAppFieldCollection([
            new PodioAppField(['field_id' => 1, 'external_id' => 'a', 'type' => 'text']),
            new PodioAppField(['field_id' => 2, 'external_id' => 'b', 'type' => 'number']),
            new PodioAppField(['field_id' => 3, 'external_id' => 'c', 'type' => 'calculation']),
        ]);

        $this->assertEquals(3, count($collection));
    }

    public function test_can_add_field()
    {
        $length = count($this->collection);
        $this->collection[] = new PodioAppField(['field_id' => 4, 'external_id' => 'd']);

        $this->assertEquals($length + 1, count($this->collection));
    }

    public function test_cannot_add_item_field()
    {
        $this->expectException('PodioDataIntegrityError');
        $this->collection[] = new PodioItemField();
    }
}
