<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioLocationItemField;

class PodioLocationItemFieldTest extends TestCase
{
    /**
     * @var \PodioLocationItemField
     */
    private $object;

    /**
     * @var \PodioLocationItemField
     */
    private $empty_values;

    private $mockClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockClient = $this->createMock(\PodioClient::class);

        $this->object = new PodioLocationItemField($this->mockClient, [
            '__api_values' => true,
            'field_id' => 123,
            'values' => [
                ['value' => '650 Townsend St., San Francisco, CA 94103', 'lat' => 37.7710325, 'lng' => -122.4033069],
            ],
        ]);

        $this->empty_values = new PodioLocationItemField($this->mockClient, ['field_id' => 1]);
    }

    public function test_can_construct_from_simple_value(): void
    {
        $object = new PodioLocationItemField($this->mockClient, [
            'field_id' => 123,
            'values' => [
                'value' => '650 Townsend St., San Francisco, CA 94103',
                'lat' => 37.7710325,
                'lng' => -122.4033069,
            ],
        ]);
        $this->assertSame([
            [
                'value' => '650 Townsend St., San Francisco, CA 94103',
                'lat' => 37.7710325,
                'lng' => -122.4033069,
            ],
        ], $object->__attribute('values'));
    }

    public function test_can_provide_value(): void
    {
        // Empty values
        $this->assertNull($this->empty_values->values);

        // Populated values
        $this->assertSame([
            'value' => '650 Townsend St., San Francisco, CA 94103',
            'lat' => 37.7710325,
            'lng' => -122.4033069,
        ], $this->object->values);
    }

    public function test_can_provide_text(): void
    {
        $this->assertNull($this->empty_values->text);
        $this->assertSame('650 Townsend St., San Francisco, CA 94103', $this->object->text);
    }

    public function test_can_set_value(): void
    {
        $this->object->values = [
            'value' => 'Vesterbrogade 34, 1620 Copenhagen V, Denmark',
            'lat' => 55.6725581,
            'lng' => 12.5564512,
        ];
        $this->assertSame([
            [
                'value' => 'Vesterbrogade 34, 1620 Copenhagen V, Denmark',
                'lat' => 55.6725581,
                'lng' => 12.5564512,
            ],
        ], $this->object->__attribute('values'));
    }

    public function test_can_set_text(): void
    {
        $this->object->text = 'Vesterbrogade 34, 1620 Copenhagen V, Denmark';
        $this->assertSame([
            [
                'value' => 'Vesterbrogade 34, 1620 Copenhagen V, Denmark',
                'lat' => 37.7710325,
                'lng' => -122.4033069,
            ],
        ], $this->object->__attribute('values'));
    }

    public function test_can_humanize_value(): void
    {
        // Empty values
        $this->assertSame('', $this->empty_values->humanized_value());

        // Populated values
        $this->assertSame('650 Townsend St., San Francisco, CA 94103', $this->object->humanized_value());
    }

    public function test_can_convert_to_api_friendly_json(): void
    {
        // Empty values
        $this->assertSame('null', $this->empty_values->as_json());

        // Populated values
        $this->assertSame('{"value":"650 Townsend St., San Francisco, CA 94103","lat":37.7710325,"lng":-122.4033069}', $this->object->as_json());
    }
}
