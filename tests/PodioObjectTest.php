<?php
class PodioObjectTest extends PHPUnit_Framework_TestCase {

  public function setup() {
    $this->object = new PodioObject(array('field_id' => 1, 'external_id' => 'a'));
  }

  // public function test_can_construct_from_array() {
  // }

  // public function test_can_construct_with_relationship() {
  // }

  // public function test_can_set_attribute() {
  // }

  // public function test_can_unset_attribute() {
  // }

  // public function test_can_get_date_attribute() {
  // }

  // public function test_can_get_datetime_attribute() {
  // }

  // public function test_can_add_relationship() {
  // }

  // public function test_can_create_listing() {
  // }

  // public function test_can_create_member() {
  // }

  // public function test_can_create_collection() {
  // }

  // public function test_can_check_rights() {
  // }

  // public function test_can_check_attribute_existence() {
  // }

  public function test_can_check_property_existence() {
    $this->assertEquals(true, $this->object->has_property('external_id'));
    $this->assertEquals(false, $this->object->has_property('invalid_property_name'));
  }

  // public function test_can_check_relationship_existence() {
  // }

  // public function test_can_convert_to_json() {
  // }


}
