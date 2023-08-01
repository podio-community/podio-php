<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioAppField;
use PodioAppFieldCollection;
use PodioItemField;

class PodioAppFieldCollectionTest extends TestCase
{
    /**
     * @var \PodioAppFieldCollection
     */
    private $collection;
    public function setUp(): void
    {
        parent::setUp();

        $this->collection = new PodioAppFieldCollection([
            new PodioAppField(['field_id' => 1, 'external_id' => 'a', 'type' => 'text']),
            new PodioAppField(['field_id' => 2, 'external_id' => 'b', 'type' => 'number']),
            new PodioAppField(['field_id' => 3, 'external_id' => 'c', 'type' => 'calculation']),
        ]);
    }

    public function test_can_construct_from_array(): void
    {
        $collection = new PodioAppFieldCollection([
            ['field_id' => 1],
            ['field_id' => 2],
            ['field_id' => 3],
        ]);
        $this->assertCount(3, $collection);
    }

    public function test_can_construct_from_objects(): void
    {
        $collection = new PodioAppFieldCollection([
            new PodioAppField(['field_id' => 1, 'external_id' => 'a', 'type' => 'text']),
            new PodioAppField(['field_id' => 2, 'external_id' => 'b', 'type' => 'number']),
            new PodioAppField(['field_id' => 3, 'external_id' => 'c', 'type' => 'calculation']),
        ]);

        $this->assertCount(3, $collection);
    }

    public function test_can_add_field(): void
    {
        $length = count($this->collection);
        $this->collection[] = new PodioAppField(['field_id' => 4, 'external_id' => 'd']);

        $this->assertCount($length + 1, $this->collection);
    }

    public function test_cannot_add_item_field(): void
    {
        $this->expectException('PodioDataIntegrityError');
        $this->collection[] = new PodioItemField();
    }
}
