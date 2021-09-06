<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioNumberItemField;

class PodioNumberItemFieldTest extends TestCase
{
    public function setUp(): void
    {
        $this->object = new PodioNumberItemField([
            '__api_values' => true,
            'field_id' => 123,
            'values' => [
                ['value' => '1234.5600'],
            ],
        ]);
        $this->empty_values = new PodioNumberItemField(['field_id' => 1]);
        $this->zero_value = new PodioNumberItemField([
            '__api_values' => true,
            'field_id' => 2,
            'values' => [['value' => '0']],
        ]);
    }

    public function test_can_construct_from_simple_value()
    {
        $object = new PodioNumberItemField([
            'field_id' => 123,
            'values' => '12.34',
        ]);
        $this->assertSame('12.34', $object->values);
    }

    public function test_can_provide_value()
    {
        $this->assertNull($this->empty_values->values);
        $this->assertSame('1234.5600', $this->object->values);
        $this->assertSame('0', $this->zero_value->values);
    }

    public function test_can_set_value()
    {
        $this->object->values = '12.34';
        $this->assertSame([['value' => '12.34']], $this->object->__attribute('values'));

        $this->object->values = '0';
        $this->assertSame('0', $this->zero_value->values);
    }

    public function test_can_humanize_value()
    {
        $this->assertSame('', $this->empty_values->humanized_value());
        $this->assertSame('1234.56', $this->object->humanized_value());
        $this->assertSame('0', $this->zero_value->humanized_value());
    }

    public function test_can_convert_to_api_friendly_json()
    {
        $this->assertSame('null', $this->empty_values->as_json());
        $this->assertSame('"1234.5600"', $this->object->as_json());
        $this->assertSame('"0"', $this->zero_value->as_json());
    }
}
