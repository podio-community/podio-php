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
    /**
     * @var \PodioItemFieldCollection
     */
    private $collection;

    private $mockClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockClient = $this->createMock(\PodioClient::class);

        $this->collection = new PodioItemFieldCollection($this->mockClient, [
            new PodioItemField($this->mockClient, ['field_id' => 1, 'external_id' => 'a', 'type' => 'text']),
            new PodioItemField($this->mockClient, ['field_id' => 2, 'external_id' => 'b', 'type' => 'number']),
            new PodioItemField($this->mockClient, ['field_id' => 3, 'external_id' => 'c', 'type' => 'calculation']),
        ]);
    }

    public function test_can_construct_with_api_values(): void
    {
        $collection = new PodioItemFieldCollection($this->mockClient, [
            ['field_id' => 1, 'type' => 'text', 'values' => [['value' => 'FooBar']]],
        ], true);
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(PodioTextItemField::class, $collection[0]);
        $this->assertSame('FooBar', $collection[0]->values);
    }

    public function test_can_construct_from_array(): void
    {
        $collection = new PodioItemFieldCollection($this->mockClient, [
            ['field_id' => 1, 'type' => 'text', 'values' => 'FooBar'],
            ['field_id' => 2, 'type' => 'number'],
            ['field_id' => 3, 'type' => 'calculation'],
        ]);
        $this->assertCount(3, $collection);
        $this->assertInstanceOf(PodioTextItemField::class, $collection[0]);
        $this->assertInstanceOf(PodioNumberItemField::class, $collection[1]);
        $this->assertInstanceOf(PodioCalculationItemField::class, $collection[2]);
        $this->assertSame('FooBar', $collection[0]->values);
    }

    public function test_can_construct_from_objects(): void
    {
        $collection = new PodioItemFieldCollection($this->mockClient, [
            new PodioTextItemField($this->mockClient, ['field_id' => 1, 'external_id' => 'a', 'type' => 'text', 'values' => 'FooBar']),
            new PodioNumberItemField($this->mockClient, ['field_id' => 2, 'external_id' => 'b', 'type' => 'number']),
            new PodioCalculationItemField($this->mockClient, ['field_id' => 3, 'external_id' => 'c', 'type' => 'calculation']),
        ]);

        $this->assertCount(3, $collection);
        $this->assertSame('FooBar', $collection[0]->values);
    }

    public function test_can_add_unknown_type(): void
    {
        $collection = new PodioItemFieldCollection($this->mockClient, [
            ['field_id' => 1, 'type' => 'invalid_field_type'],
        ]);

        $this->assertCount(1, $collection);
        $this->assertInstanceOf(PodioItemField::class, $collection[0]);
    }

    public function test_can_add_field(): void
    {
        $length = count($this->collection);
        $this->collection[] = new PodioTextItemField($this->mockClient, ['field_id' => 4, 'external_id' => 'd']);

        $this->assertCount($length + 1, $this->collection);
    }

    public function test_cannot_add_app_field(): void
    {
        $this->expectException('PodioDataIntegrityError');
        $this->collection[] = new PodioAppField($this->mockClient);
    }
}
