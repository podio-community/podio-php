<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioAppItemField;
use PodioCollection;
use PodioItem;

class PodioAppItemFieldTest extends TestCase
{
    /**
     * @var \PodioAppItemField
     */
    private $object;

    private $mockClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockClient = $this->createMock(\PodioClient::class);

        $this->object = new PodioAppItemField($this->mockClient, [
            '__api_values' => true,
            'values' => [
                ['value' => ['item_id' => 1, 'title' => 'Snap']],
                ['value' => ['item_id' => 2, 'title' => 'Crackle']],
                ['value' => ['item_id' => 3, 'title' => 'Pop']],
            ],
        ]);
    }

    public function test_can_construct_from_simple_value(): void
    {
        $object = new PodioAppItemField($this->mockClient, [
            'field_id' => 123,
            'values' => ['item_id' => 4, 'title' => 'Captain Crunch'],
        ]);
        $this->assertSame([
            ['value' => ['item_id' => 4, 'title' => 'Captain Crunch']],
        ], $object->__attribute('values'));
    }

    public function test_can_provide_value(): void
    {
        // Empty values
        $empty_values = new PodioAppItemField($this->mockClient, ['field_id' => 1]);
        $this->assertNull($empty_values->values);

        // Populated values
        $this->assertInstanceOf(PodioCollection::class, $this->object->values);
        $this->assertCount(3, $this->object->values);
        foreach ($this->object->values as $value) {
            $this->assertInstanceOf(PodioItem::class, $value);
        }
    }

    public function test_can_set_value_from_object(): void
    {
        $this->object->values = new PodioItem($this->mockClient, ['item_id' => 4, 'title' => 'Captain Crunch']);
        $this->assertSame([
            ['value' => ['item_id' => 4, 'title' => 'Captain Crunch']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_collection(): void
    {
        $this->object->values = new PodioCollection($this->mockClient, [new PodioItem($this->mockClient, ['item_id' => 4, 'title' => 'Captain Crunch'])]);

        $this->assertSame([
            ['value' => ['item_id' => 4, 'title' => 'Captain Crunch']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_hash(): void
    {
        $this->object->values = ['item_id' => 4, 'title' => 'Captain Crunch'];
        $this->assertSame([
            ['value' => ['item_id' => 4, 'title' => 'Captain Crunch']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_array_of_objects(): void
    {
        $this->object->values = [
            new PodioItem($this->mockClient, ['item_id' => 4, 'title' => 'Captain Crunch']),
            new PodioItem($this->mockClient, ['item_id' => 5, 'title' => 'Count Chocula']),
        ];
        $this->assertSame([
            ['value' => ['item_id' => 4, 'title' => 'Captain Crunch']],
            ['value' => ['item_id' => 5, 'title' => 'Count Chocula']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_array_of_hashes(): void
    {
        $this->object->values = [
            ['item_id' => 4, 'title' => 'Captain Crunch'],
            ['item_id' => 5, 'title' => 'Count Chocula'],
        ];
        $this->assertSame([
            ['value' => ['item_id' => 4, 'title' => 'Captain Crunch']],
            ['value' => ['item_id' => 5, 'title' => 'Count Chocula']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_humanize_value(): void
    {
        // Empty values
        $empty_values = new PodioAppItemField($this->mockClient, ['field_id' => 1]);
        $this->assertSame('', $empty_values->humanized_value());

        // Populated values
        $this->assertSame('Snap;Crackle;Pop', $this->object->humanized_value());
    }

    public function test_can_convert_to_api_friendly_json(): void
    {
        // Empty values
        $empty_values = new PodioAppItemField($this->mockClient, ['field_id' => 1]);
        $this->assertSame('[]', $empty_values->as_json());

        // Populated values
        $this->assertSame('[1,2,3]', $this->object->as_json());
    }
}
