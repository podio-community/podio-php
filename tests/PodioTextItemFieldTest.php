<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioTextItemField;

class PodioTextItemFieldTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->object = new PodioTextItemField([
            '__api_values' => true,
            'field_id' => 123,
            'values' => [
                ['value' => 'FooBar'],
            ],
        ]);
        $this->empty_values = new PodioTextItemField(['field_id' => 1]);
    }

    public function test_can_construct_from_simple_value(): void
    {
        $object = new PodioTextItemField([
            'field_id' => 123,
            'values' => 'FooBar',
        ]);
        $this->assertSame('FooBar', $object->values);
    }

    public function test_can_provide_value(): void
    {
        $this->assertNull($this->empty_values->values);
        $this->assertSame('FooBar', $this->object->values);
    }

    public function test_can_set_value(): void
    {
        $this->object->values = 'Baz';
        $this->assertSame([['value' => 'Baz']], $this->object->__attribute('values'));
    }

    public function test_can_humanize_value(): void
    {
        // Empty values
        $this->assertSame('', $this->empty_values->humanized_value());

        // HTML content
        $html_values = new PodioTextItemField(['field_id' => 1]);
        $html_values->values = '<p>FooBar</p>';
        $this->assertSame('FooBar', $html_values->humanized_value());

        // Populated values
        $this->assertSame('FooBar', $this->object->humanized_value());
    }

    public function test_can_convert_to_api_friendly_json(): void
    {
        $this->assertSame('null', $this->empty_values->as_json());
        $this->assertSame('"FooBar"', $this->object->as_json());
    }
}
