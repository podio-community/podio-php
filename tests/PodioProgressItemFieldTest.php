<?php
class PodioProgressItemFieldTest extends PHPUnit_Framework_TestCase {

  public function setup() {
    $this->object = new PodioProgressItemField(array(
      'field_id' => 123,
      'values' => array(
        array('value' => 55)
      )
    ));
    $this->empty_values = new PodioProgressItemField(array('field_id' => 1));
    $this->zero_value = new PodioProgressItemField(array('field_id' => 2, 'values' => array(array('value' => 0))));
  }

  public function test_can_provide_value() {
    $this->assertNull($this->empty_values->values);
    $this->assertEquals(55, $this->object->values);
    $this->assertEquals(0, $this->zero_value->values);
  }

  public function test_can_set_value() {
    $this->object->values = 75;
    $this->assertEquals(array(array('value' => 75)), $this->object->__attribute('values'));

    $this->object->values = 0;
    $this->assertEquals(0, $this->zero_value->values);
  }

  public function test_can_humanize_value() {
    $this->assertEquals('', $this->empty_values->humanized_value());
    $this->assertEquals('55%', $this->object->humanized_value());
    $this->assertEquals('0%', $this->zero_value->humanized_value());
  }

  public function test_can_convert_to_api_friendly_json() {
    $this->assertEquals('null', $this->empty_values->as_json());
    $this->assertEquals('55', $this->object->as_json());
    $this->assertEquals('0', $this->zero_value->as_json());
  }

}
