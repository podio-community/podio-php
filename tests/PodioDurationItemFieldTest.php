<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioDurationItemField;

class PodioDurationItemFieldTest extends TestCase
{
    public function setUp(): void
    {
        $this->object = new PodioDurationItemField([
            '__api_values' => true,
            'field_id' => 123,
            'values' => [
                ['value' => 3723],
            ],
        ]);

        $this->empty_values = new PodioDurationItemField([
            'field_id' => 456,
        ]);
    }

    public function test_can_construct_from_simple_value(): void
    {
        $object = new PodioDurationItemField([
            'field_id' => 123,
            'values' => 3600,
        ]);
        $this->assertSame(3600, $object->values);
    }

    public function test_can_provide_value(): void
    {
        $this->assertNull($this->empty_values->values);
        $this->assertSame(3723, $this->object->values);
    }

    public function test_can_provide_hours(): void
    {
        $this->assertSame(0, (int) $this->empty_values->hours);
        $this->assertSame(1, (int) $this->object->hours);
    }

    public function test_can_provide_minutes(): void
    {
        $this->assertSame(0, $this->empty_values->minutes);
        $this->assertSame(2, $this->object->minutes);
    }

    public function test_can_provide_seconds(): void
    {
        $this->assertSame(0, $this->empty_values->seconds);
        $this->assertSame(3, $this->object->seconds);
    }

    public function test_can_set_value(): void
    {
        $this->object->values = 123;
        $this->assertSame([['value' => 123]], $this->object->__attribute('values'));
    }

    public function test_can_humanize_value(): void
    {
        $this->assertSame('00:00:00', $this->empty_values->humanized_value());
        $this->assertSame('01:02:03', $this->object->humanized_value());
    }

    public function test_can_convert_to_api_friendly_json(): void
    {
        $this->assertSame('null', $this->empty_values->as_json());
        $this->assertSame(3723, (int) $this->object->as_json());
    }
}
