<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioMoneyItemField;

class PodioMoneyItemFieldTest extends TestCase
{
    /**
     * @var \PodioMoneyItemField
     */
    private $object;

    /**
     * @var \PodioMoneyItemField
     */
    private $empty_values;

    /**
     * @var \PodioMoneyItemField
     */
    private $zero_value;

    public function setUp(): void
    {
        parent::setUp();

        $this->object = new PodioMoneyItemField([
            '__api_values' => true,
            'field_id' => 123,
            'values' => [
                ['value' => '123.5568', 'currency' => 'USD'],
            ],
        ]);

        $this->empty_values = new PodioMoneyItemField([
            'field_id' => 456,
        ]);

        $this->zero_value = new PodioMoneyItemField([
            '__api_values' => true,
            'field_id' => 789,
            'values' => [
                ['value' => '0', 'currency' => 'USD'],
            ],
        ]);
    }

    public function test_can_construct_from_simple_value(): void
    {
        $object = new PodioMoneyItemField([
            'field_id' => 123,
            'values' => ['value' => '456.67', 'currency' => 'BTC'],
        ]);
        $this->assertSame([['value' => '456.67', 'currency' => 'BTC']], $object->__attribute('values'));
    }

    public function test_can_provide_value(): void
    {
        $this->assertNull($this->empty_values->values);
        $this->assertSame(['value' => '123.5568', 'currency' => 'USD'], $this->object->values);
        $this->assertSame(['value' => '0', 'currency' => 'USD'], $this->zero_value->values);
    }

    public function test_can_provide_amount(): void
    {
        $this->assertNull($this->empty_values->amount);
        $this->assertSame('123.5568', $this->object->amount);
        $this->assertSame('0', $this->zero_value->amount);
    }

    public function test_can_provide_currency(): void
    {
        // $this->assertNull($this->empty_values->currency);
        $this->assertSame('USD', $this->object->currency);
        $this->assertSame('USD', $this->zero_value->currency);
    }

    public function test_can_set_value(): void
    {
        $this->object->values = ['value' => '456.67', 'currency' => 'BTC'];
        $this->assertSame([['value' => '456.67', 'currency' => 'BTC']], $this->object->__attribute('values'));

        $this->object->values = ['value' => '0', 'currency' => 'BTC'];
        $this->assertSame([['value' => '0', 'currency' => 'BTC']], $this->object->__attribute('values'));
    }

    public function test_can_set_amount(): void
    {
        $this->object->amount = '456.67';
        $this->assertSame([['currency' => 'USD', 'value' => '456.67']], $this->object->__attribute('values'));

        $this->object->amount = '0';
        $this->assertSame([['currency' => 'USD', 'value' => '0']], $this->object->__attribute('values'));
    }

    public function test_can_set_currency(): void
    {
        $this->object->currency = 'BTC';
        $this->assertSame([['currency' => 'BTC', 'value' => '123.5568']], $this->object->__attribute('values'));
    }

    public function test_can_humanize_value(): void
    {
        $this->assertSame('', $this->empty_values->humanized_value());
        $this->assertSame('$123.56', $this->object->humanized_value());
        $this->assertSame('$0.00', $this->zero_value->humanized_value());

        $this->object->currency = 'GBP';
        $this->assertSame('£123.56', $this->object->humanized_value());

        $this->object->currency = 'EUR';
        $this->assertSame('€123.56', $this->object->humanized_value());

        $this->object->currency = 'DKK';
        $this->assertSame('DKK 123.56', $this->object->humanized_value());
    }

    public function test_can_convert_to_api_friendly_json(): void
    {
        $this->assertSame('null', $this->empty_values->as_json());
        $this->assertSame('{"value":"123.5568","currency":"USD"}', $this->object->as_json());
        $this->assertSame('{"value":"0","currency":"USD"}', $this->zero_value->as_json());
    }
}
