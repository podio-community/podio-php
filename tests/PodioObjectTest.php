<?php
class PodioObjectTest extends PHPUnit_Framework_TestCase {

  public function setup() {
    $this->object = new PodioObject();
    $this->object->property('id', 'integer');
    $this->object->property('external_id', 'string');
    $this->object->property('rights', 'array');
    $this->object->has_many('fields', 'PodioObject');
    $this->object->init(array(
      'field_id' => 1,
      'external_id' => 'a',
      'rights' => array('view', 'update')
    ));
  }

  // public function test_can_construct_from_array() {
  // }

  // public function test_can_construct_from_id() {
  // }

  // public function test_can_construct_from_external_id() {
  // }

  // public function test_can_construct_with_relationship() {
  // }

  // public function test_can_unset_attribute() {
  // }

  // public function test_can_set_integer_attribute() {
  // }

  // public function test_can_set_boolean_attribute() {
  // }

  // public function test_can_set_string_attribute() {
  // }

  // public function test_can_set_array_attribute() {
  // }

  // public function test_can_set_hash_attribute() {
  // }

  // public function test_can_set_date_attribute() {
  // }

  // public function test_can_set_datetime_attribute() {
  // }

  // public function test_can_get_date_attribute() {
  // }

  // public function test_can_get_datetime_attribute() {
  // }

  // public function test_can_create_listing() {
  // }

  // public function test_can_create_member() {
  // }

  // public function test_can_create_collection() {
  // }

  public function test_can_check_rights() {
    $this->assertEquals(true, $this->object->can('view'));
    $this->assertEquals(false, $this->object->can('delete'));
  }

  public function test_can_check_attribute_existence() {
    $this->assertEquals(true, $this->object->has_attribute('rights'));
    $this->assertEquals(false, $this->object->has_attribute('fields'));
  }

  public function test_can_check_property_existence() {
    $this->assertEquals(true, $this->object->has_property('external_id'));
    $this->assertEquals(false, $this->object->has_property('invalid_property_name'));
  }

  public function test_can_check_relationship_existence() {
    $this->assertEquals(true, $this->object->has_relationship('fields'));
    $this->assertEquals(false, $this->object->has_relationship('external_id'));
  }

  public function test_can_add_child_relationship() {
    $instance = new PodioObject();
    $this->object->add_relationship($instance, 'fields');

    $relationship = $this->object->relationship();
    $this->assertEquals($instance, $relationship['instance']);
    $this->assertEquals('fields', $relationship['property']);
  }

  /**
    * @expectedException PodioDataIntegrityError
    */
  public function test_cannot_add_child_relationship_on_non_property() {
    $instance = new PodioObject();
    $this->object->add_relationship($instance, 'invalid_property_name');
  }

  // public function test_can_convert_to_json() {
  // }

  // TODO: Test datetime getter/setter



}
