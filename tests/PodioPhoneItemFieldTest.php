<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioPhoneItemField;

class PodioPhoneItemFieldTest extends TestCase
{
    /**
     * @var \PodioPhoneItemField
     */
    private $object;

    public function setUp(): void
    {
        parent::setUp();

        $this->object = new PodioPhoneItemField([
            '__api_values' => true,
            'values' => [
                ['type' => 'work', 'value' => '0123-1233333'],
                ['type' => 'other', 'value' => '0232-123123'],
            ],
        ]);
    }

    public function test_can_provide_value(): void
    {
        // Empty values
        $empty_values = new PodioPhoneItemField();
        $this->assertNull($empty_values->values);

        // Populated values
        $this->assertSame([
            ['type' => 'work', 'value' => '0123-1233333'],
            ['type' => 'other', 'value' => '0232-123123'],
        ], $this->object->values);
    }

    public function test_can_set_value_from_hash(): void
    {
        $this->object->values = [
            ['type' => 'work', 'value' => '0123-999'],
            ['type' => 'other', 'value' => '0232-999'],
        ];
        $this->assertSame([
            ['type' => 'work', 'value' => '0123-999'],
            ['type' => 'other', 'value' => '0232-999'],
        ], $this->object->__attribute('values'));
    }

    public function test_can_humanize_value(): void
    {
        // Empty values
        $empty_values = new PodioPhoneItemField();
        $this->assertSame('', $empty_values->humanized_value());

        // Populated values
        $this->assertSame('work: 0123-1233333;other: 0232-123123', $this->object->humanized_value());
    }

    public function test_can_convert_to_api_friendly_json(): void
    {
        // Empty values
        $empty_values = new PodioPhoneItemField();
        $this->assertSame('[]', $empty_values->as_json());

        // Populated values
        $this->assertSame('[{"type":"work","value":"0123-1233333"},{"type":"other","value":"0232-123123"}]', $this->object->as_json());
    }
}
