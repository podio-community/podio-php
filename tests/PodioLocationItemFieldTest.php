<?php
class PodioLocationItemFieldTest extends PHPUnit_Framework_TestCase {

  public function setup() {
    $this->object = new PodioLocationItemField(array(
      '__api_values' => true,
      'field_id' => 123,
      'values' => array(
        array('value' => '650 Townsend St., San Francisco, CA 94103'),
        array('value' => 'Vesterbrogade 34, 1620 Copenhagen'),
      )
    ));
  }

  public function test_can_construct_from_simple_value() {
    $object = new PodioLocationItemField(array(
      'field_id' => 123,
      'values' => array('1600 Pennsylvania Ave NW, Washington, DC 20500')
    ));
    $this->assertEquals(array(array('value' => '1600 Pennsylvania Ave NW, Washington, DC 20500')), $object->__attribute('values'));
  }

  public function test_can_provide_value() {
    // Empty values
    $empty_values = new PodioLocationItemField(array('field_id' => 1));
    $this->assertNull($empty_values->values);

    // Populated values
    $this->assertEquals(array('650 Townsend St., San Francisco, CA 94103', 'Vesterbrogade 34, 1620 Copenhagen'), $this->object->values);
  }

  public function test_can_set_values() {
    $this->object->values = array('1600 Pennsylvania Ave NW, Washington, DC 20500');
    $this->assertEquals(array(array('value' => '1600 Pennsylvania Ave NW, Washington, DC 20500')), $this->object->__attribute('values'));
  }

  public function test_can_add_value() {
    $this->object->add_value('1600 Pennsylvania Ave NW, Washington, DC 20500');
    $this->assertEquals(array(
      array('value' => '650 Townsend St., San Francisco, CA 94103'),
      array('value' => 'Vesterbrogade 34, 1620 Copenhagen'),
      array('value' => '1600 Pennsylvania Ave NW, Washington, DC 20500'),
    ), $this->object->__attribute('values'));
  }

  public function test_can_humanize_value() {
    // Empty values
    $empty_values = new PodioLocationItemField(array('field_id' => 1));
    $this->assertEquals('', $empty_values->humanized_value());

    // Populated values
    $this->assertEquals('650 Townsend St., San Francisco, CA 94103;Vesterbrogade 34, 1620 Copenhagen', $this->object->humanized_value());
  }

  public function test_can_convert_to_api_friendly_json() {
    // Empty values
    $empty_values = new PodioLocationItemField(array('field_id' => 1));
    $this->assertEquals('null', $empty_values->as_json());

    // Populated values
    $this->assertEquals('["650 Townsend St., San Francisco, CA 94103","Vesterbrogade 34, 1620 Copenhagen"]', $this->object->as_json());
  }

}
