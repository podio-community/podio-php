<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioCollection;
use PodioContact;
use PodioContactItemField;

class PodioContactItemFieldTest extends TestCase
{
    public function setUp(): void
    {
        $this->object = new PodioContactItemField([
            '__api_values' => true,
            'values' => [
                ['value' => ['profile_id' => 1, 'name' => 'Snap']],
                ['value' => ['profile_id' => 2, 'name' => 'Crackle']],
                ['value' => ['profile_id' => 3, 'name' => 'Pop']],
            ],
        ]);
    }

    public function test_can_construct_from_simple_value()
    {
        $object = new PodioContactItemField([
            'field_id' => 123,
            'values' => ['profile_id' => 4, 'name' => 'Captain Crunch'],
        ]);
        $this->assertSame([
            ['value' => ['profile_id' => 4, 'name' => 'Captain Crunch']],
        ], $object->__attribute('values'));
    }

    public function test_can_provide_value()
    {
        // Empty values
        $empty_values = new PodioContactItemField(['field_id' => 1]);
        $this->assertNull($empty_values->values);

        // Populated values
        $this->assertInstanceOf('PodioCollection', $this->object->values);
        $this->assertCount(3, $this->object->values);
        foreach ($this->object->values as $value) {
            $this->assertInstanceOf('PodioContact', $value);
        }
    }

    public function test_can_set_value_from_object()
    {
        $this->object->values = new PodioContact(['profile_id' => 4, 'name' => 'Captain Crunch']);
        $this->assertSame([
            ['value' => ['profile_id' => 4, 'name' => 'Captain Crunch']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_collection()
    {
        $this->object->values = new PodioCollection([
            new PodioContact([
                'profile_id' => 4,
                'name' => 'Captain Crunch',
            ]),
        ]);

        $this->assertSame([
            ['value' => ['profile_id' => 4, 'name' => 'Captain Crunch']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_hash()
    {
        $this->object->values = ['profile_id' => 4, 'name' => 'Captain Crunch'];
        $this->assertSame([
            ['value' => ['profile_id' => 4, 'name' => 'Captain Crunch']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_array_of_objects()
    {
        $this->object->values = [
            new PodioContact(['profile_id' => 4, 'name' => 'Captain Crunch']),
            new PodioContact(['profile_id' => 5, 'name' => 'Count Chocula']),
        ];
        $this->assertSame([
            ['value' => ['profile_id' => 4, 'name' => 'Captain Crunch']],
            ['value' => ['profile_id' => 5, 'name' => 'Count Chocula']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_array_of_hashes()
    {
        $this->object->values = [
            ['profile_id' => 4, 'name' => 'Captain Crunch'],
            ['profile_id' => 5, 'name' => 'Count Chocula'],
        ];
        $this->assertSame([
            ['value' => ['profile_id' => 4, 'name' => 'Captain Crunch']],
            ['value' => ['profile_id' => 5, 'name' => 'Count Chocula']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_humanize_value()
    {
        // Empty values
        $empty_values = new PodioContactItemField(['field_id' => 1]);
        $this->assertSame('', $empty_values->humanized_value());

        // Populated values
        $this->assertSame('Snap;Crackle;Pop', $this->object->humanized_value());
    }

    public function test_can_convert_to_api_friendly_json()
    {
        // Empty values
        $empty_values = new PodioContactItemField(['field_id' => 1]);
        $this->assertSame('[]', $empty_values->as_json());

        // Populated values
        $this->assertSame('[1,2,3]', $this->object->as_json());
    }
}
