<?php
class PodioStateItemFieldTest extends PHPUnit_Framework_TestCase {

  public function setup() {
    $this->object = new PodioStateItemField(array(
      '__api_values' => true,
      'field_id' => 123,
      'values' => array(
        array('value' => 'FooBar')
      )
    ));
    $this->empty_values = new PodioStateItemField(array('field_id' => 1));
  }

  public function test_can_construct_from_simple_value() {
    $object = new PodioStateItemField(array(
      'field_id' => 123,
      'values' => 'FooBar'
    ));
    $this->assertEquals('FooBar', $object->values);
  }

  public function test_can_provide_value() {
    $this->assertNull($this->empty_values->values);
    $this->assertEquals('FooBar', $this->object->values);
  }

  public function test_can_set_value() {
    $this->object->values = 'Baz';
    $this->assertEquals(array(array('value' => 'Baz')), $this->object->__attribute('values'));
  }

  public function test_can_humanize_value() {
    $this->assertEquals('', $this->empty_values->humanized_value());
    $this->assertEquals('FooBar', $this->object->humanized_value());
  }

  public function test_can_convert_to_api_friendly_json() {
    $this->assertEquals('null', $this->empty_values->as_json());
    $this->assertEquals('"FooBar"', $this->object->as_json());
  }

}
