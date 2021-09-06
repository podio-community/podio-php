<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioAssetItemField;
use PodioCollection;
use PodioFile;

class PodioAssetItemFieldTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->object = new PodioAssetItemField([
            '__api_values' => true,
            'values' => [
                ['value' => ['file_id' => 1, 'name' => 'doge.jpg']],
                ['value' => ['file_id' => 2, 'name' => 'trollface.jpg']],
                ['value' => ['file_id' => 3, 'name' => 'YUNO.jpg']],
            ],
        ]);
    }

    public function test_can_construct_from_simple_value(): void
    {
        $object = new PodioAssetItemField([
            'field_id' => 123,
            'values' => ['file_id' => 4, 'name' => 'philosoraptor.jpg'],
        ]);
        $this->assertSame([
            ['value' => ['file_id' => 4, 'name' => 'philosoraptor.jpg']],
        ], $object->__attribute('values'));
    }

    public function test_can_provide_value(): void
    {
        // Empty values
        $empty_values = new PodioAssetItemField(['field_id' => 1]);
        $this->assertNull($empty_values->values);

        // Populated values
        $this->assertInstanceOf('PodioCollection', $this->object->values);
        $this->assertCount(3, $this->object->values);
        foreach ($this->object->values as $value) {
            $this->assertInstanceOf('PodioFile', $value);
        }
    }

    public function test_can_set_value_from_object(): void
    {
        $this->object->values = new PodioFile(['file_id' => 4, 'name' => 'philosoraptor.jpg']);
        $this->assertSame([
            ['value' => ['file_id' => 4, 'name' => 'philosoraptor.jpg']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_collection(): void
    {
        $this->object->values = new PodioCollection([new PodioFile(['file_id' => 4, 'name' => 'philosoraptor.jpg'])]);

        $this->assertSame([
            ['value' => ['file_id' => 4, 'name' => 'philosoraptor.jpg']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_hash(): void
    {
        $this->object->values = ['file_id' => 4, 'name' => 'philosoraptor.jpg'];
        $this->assertSame([
            ['value' => ['file_id' => 4, 'name' => 'philosoraptor.jpg']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_array_of_objects(): void
    {
        $this->object->values = [
            new PodioFile(['file_id' => 4, 'name' => 'philosoraptor.jpg']),
            new PodioFile(['file_id' => 5, 'name' => 'nyancat.jgp']),
        ];
        $this->assertSame([
            ['value' => ['file_id' => 4, 'name' => 'philosoraptor.jpg']],
            ['value' => ['file_id' => 5, 'name' => 'nyancat.jgp']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_array_of_hashes(): void
    {
        $this->object->values = [
            ['file_id' => 4, 'name' => 'philosoraptor.jpg'],
            ['file_id' => 5, 'name' => 'nyancat.jgp'],
        ];
        $this->assertSame([
            ['value' => ['file_id' => 4, 'name' => 'philosoraptor.jpg']],
            ['value' => ['file_id' => 5, 'name' => 'nyancat.jgp']],
        ], $this->object->__attribute('values'));
    }

    public function test_can_humanize_value(): void
    {
        // Empty values
        $empty_values = new PodioAssetItemField(['field_id' => 1]);
        $this->assertSame('', $empty_values->humanized_value());

        // Populated values
        $this->assertSame('doge.jpg;trollface.jpg;YUNO.jpg', $this->object->humanized_value());
    }

    public function test_can_convert_to_api_friendly_json(): void
    {
        // Empty values
        $empty_values = new PodioAssetItemField(['field_id' => 1]);
        $this->assertSame('[]', $empty_values->as_json());

        // Populated values
        $this->assertSame('[1,2,3]', $this->object->as_json());
    }
}
