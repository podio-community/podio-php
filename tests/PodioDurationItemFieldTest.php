<?php
class PodioDurationItemFieldTest extends PHPUnit_Framework_TestCase {

  public function setup() {
    $this->object = new PodioDurationItemField(array(
      'field_id' => 123,
      'values' => array(
        array('value' => 3723)
      )
    ));

    $this->empty_values = new PodioDurationItemField(array(
      'field_id' => 456
    ));

  }

  public function test_can_provide_value() {
    // Empty values
    $this->assertNull($this->empty_values->values);

    // Populated values
    $this->assertEquals(3723, $this->object->values);
  }

  public function test_can_provide_hours() {
    // Empty values
    $this->assertEquals(0, $this->empty_values->hours);

    // Populated values
    $this->assertEquals(1, $this->object->hours);
  }

  public function test_can_provide_minutes() {
    // Empty values
    $this->assertEquals(0, $this->empty_values->minutes);

    // Populated values
    $this->assertEquals(2, $this->object->minutes);
  }

  public function test_can_provide_seconds() {
    // Empty values
    $this->assertEquals(0, $this->empty_values->seconds);

    // Populated values
    $this->assertEquals(3, $this->object->seconds);
  }

  public function test_can_set_value() {
    $this->object->values = 123;
    $this->assertEquals(array(array('value' => 123)), $this->object->__attribute('values'));
  }

  public function test_can_humanize_value() {
    // Empty values
    $this->assertEquals('00:00:00', $this->empty_values->humanized_value());

    // Populated values
    $this->assertEquals('01:02:03', $this->object->humanized_value());
  }

  public function test_can_convert_to_api_friendly_json() {
    // Empty values
    $this->assertEquals('null', $this->empty_values->as_json());

    // Populated values
    $this->assertEquals(3723, $this->object->as_json());
  }

}
