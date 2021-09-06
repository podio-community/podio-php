<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioCalculationItemField;

class PodioCalculationItemFieldTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->object = new PodioCalculationItemField([
            '__api_values' => true,
            'field_id' => 123,
            'values' => [
                ['value' => '1234.5600'],
            ],
        ]);
        $this->empty_values = new PodioCalculationItemField(['field_id' => 1]);
        $this->zero_value = new PodioCalculationItemField([
            '__api_values' => true,
            'field_id' => 2,
            'values' => [['value' => '0']],
        ]);
        $this->date_value = new PodioCalculationItemField([
            '__api_values' => true,
            'field_id' => 3,
            'values' => [
                [
                    'start' => '2016-11-11 00:00:00',
                    'start_date_utc' => '2016-11-11',
                    'start_time_utc' => '00:00:00',
                    'start_time' => '00:00:00',
                    'start_utc' => '2016-11-11 00:00:00',
                    'start_date' => '2016-11-11',
                ],
            ],
        ]);
    }

    public function test_can_provide_value(): void
    {
        $this->assertNull($this->empty_values->values);
        $this->assertSame('1234.5600', $this->object->values);
        $this->assertSame('0', $this->zero_value->values);
        $this->assertSame([
            'start' => '2016-11-11 00:00:00',
            'start_date_utc' => '2016-11-11',
            'start_time_utc' => '00:00:00',
            'start_time' => '00:00:00',
            'start_utc' => '2016-11-11 00:00:00',
            'start_date' => '2016-11-11',
        ], $this->date_value->values);
    }

    public function test_cannot_modify_value(): void
    {
        $this->object->values = '12.34';
        $this->assertSame([['value' => '1234.5600']], $this->object->__attribute('values'));
    }

    public function test_can_humanize_value(): void
    {
        $this->assertSame('', $this->empty_values->humanized_value());
        $this->assertSame('1234.56', $this->object->humanized_value());
        $this->assertSame('0', $this->zero_value->humanized_value());
        // cannot humanize value for date ($this->date_value)
    }

    public function test_can_convert_to_api_friendly_json(): void
    {
        $this->assertSame('null', $this->empty_values->as_json());
        $this->assertSame('"1234.5600"', $this->object->as_json());
        $this->assertSame('"0"', $this->zero_value->as_json());
        $date_value_json = '{"start":"2016-11-11 00:00:00","start_date_utc":"2016-11-11","start_time_utc":"00:00:00",'.'"start_time":"00:00:00","start_utc":"2016-11-11 00:00:00","start_date":"2016-11-11"}';
        $this->assertSame(''.$date_value_json.'', $this->date_value->as_json());
    }
}
