<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioAppField;
use PodioCalculationItemField;
use PodioItemField;
use PodioItemFieldCollection;
use PodioNumberItemField;
use PodioTextItemField;

class PodioItemFieldCollectionTest extends TestCase
{
    public function setUp(): void
    {
        $this->collection = new PodioItemFieldCollection([
            new PodioItemField(['field_id' => 1, 'external_id' => 'a', 'type' => 'text']),
            new PodioItemField(['field_id' => 2, 'external_id' => 'b', 'type' => 'number']),
            new PodioItemField(['field_id' => 3, 'external_id' => 'c', 'type' => 'calculation']),
        ]);
    }

    public function test_can_construct_with_api_values()
    {
        $collection = new PodioItemFieldCollection([
            ['field_id' => 1, 'type' => 'text', 'values' => [['value' => 'FooBar']]],
        ], true);
        $this->assertCount(1, $collection);
        $this->assertSame('PodioTextItemField', get_class($collection[0]));
        $this->assertSame('FooBar', $collection[0]->values);
    }

    public function test_can_construct_from_array()
    {
        $collection = new PodioItemFieldCollection([
            ['field_id' => 1, 'type' => 'text', 'values' => 'FooBar'],
            ['field_id' => 2, 'type' => 'number'],
            ['field_id' => 3, 'type' => 'calculation'],
        ]);
        $this->assertCount(3, $collection);
        $this->assertSame('PodioTextItemField', get_class($collection[0]));
        $this->assertSame('PodioNumberItemField', get_class($collection[1]));
        $this->assertSame('PodioCalculationItemField', get_class($collection[2]));
        $this->assertSame('FooBar', $collection[0]->values);
    }

    public function test_can_construct_from_objects()
    {
        $collection = new PodioItemFieldCollection([
            new PodioTextItemField(['field_id' => 1, 'external_id' => 'a', 'type' => 'text', 'values' => 'FooBar']),
            new PodioNumberItemField(['field_id' => 2, 'external_id' => 'b', 'type' => 'number']),
            new PodioCalculationItemField(['field_id' => 3, 'external_id' => 'c', 'type' => 'calculation']),
        ]);

        $this->assertCount(3, $collection);
        $this->assertSame('FooBar', $collection[0]->values);
    }

    public function test_can_add_unknown_type()
    {
        $collection = new PodioItemFieldCollection([
            ['field_id' => 1, 'type' => 'invalid_field_type'],
        ]);

        $this->assertCount(1, $collection);
        $this->assertSame('PodioItemField', get_class($collection[0]));
    }

    public function test_can_add_field()
    {
        $length = count($this->collection);
        $this->collection[] = new PodioTextItemField(['field_id' => 4, 'external_id' => 'd']);

        $this->assertCount($length + 1, $this->collection);
    }

    public function test_cannot_add_app_field()
    {
        $this->expectException('PodioDataIntegrityError');
        $this->collection[] = new PodioAppField();
    }
}
