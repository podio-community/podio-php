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

  public function test_can_construct_from_array() {
    $object = new PodioObject();
    $object->property('id', 'integer');
    $object->property('external_id', 'string');
    $object->property('string_property', 'string');
    $object->init(array('id' => 1, 'external_id' => 'a', 'string_property' => 'FooBar'));

    $this->assertEquals(1, $object->id);
    $this->assertEquals('a', $object->external_id);
    $this->assertEquals('FooBar', $object->string_property);
  }

  public function test_can_construct_from_id() {
    $object = new PodioObject();
    $object->property('id', 'integer');
    $object->init(1);

    $this->assertEquals(1, $object->id);
  }

  public function test_can_construct_from_external_id() {
    $object = new PodioObject();
    $object->property('external_id', 'string');
    $object->init('a');

    $this->assertEquals('a', $object->external_id);
  }

  // public function test_can_construct_with_relationship() {
  // }

  // public function test_can_unset_attribute() {
  // }

  public function test_can_set_integer_attribute() {
    $object = new PodioObject();
    $object->property('int_property', 'integer');
    $object->init(array('int_property' => 1));

    $this->assertEquals(1, $object->int_property);
  }

  public function test_can_set_boolean_attribute() {
    $object = new PodioObject();
    $object->property('bool_property', 'boolean');
    $object->property('bool2_property', 'boolean');
    $object->init(array('bool_property' => true, 'bool2_property' => false));

    $this->assertTrue($object->bool_property);
    $this->assertFalse($object->bool2_property);

  }

  public function test_can_set_string_attribute() {
    $object = new PodioObject();
    $object->property('string_property', 'string');
    $object->init(array('string_property' => 'FooBar'));

    $this->assertEquals('FooBar', $object->string_property);
  }

  public function test_can_set_array_attribute() {
    $object = new PodioObject();
    $object->property('array_property', 'array');
    $object->init(array('array_property' => array('a', 'b', 'c')));

    $this->assertEquals(array('a', 'b', 'c'), $object->array_property);
  }

  public function test_can_set_hash_attribute() {
    $object = new PodioObject();
    $object->property('hash_property', 'hash');
    $object->init(array('hash_property' => array('a' => 'a', 'b' => 'b', 'c' => 'c')));

    $this->assertEquals(array('a' => 'a', 'b' => 'b', 'c' => 'c'), $object->hash_property);
  }

  public function test_can_set_date_attribute() {
    $tz = new DateTimeZone('UTC');

    $object = new PodioObject();
    $object->property('date_property', 'date');
    $object->init(array('date_property' => new DateTime('2014-01-01 12:00:00', $tz)));
    $this->assertInstanceOf('DateTime', $object->date_property);
    $this->assertEquals('2014-01-01', $object->date_property->format('Y-m-d'));

    $object->date_property = new DateTime('2014-01-02 14:00:00', $tz);
    $this->assertInstanceOf('DateTime', $object->date_property);
    $this->assertEquals('2014-01-02', $object->date_property->format('Y-m-d'));

    $object->date_property = '2014-01-03';
    $this->assertInstanceOf('DateTime', $object->date_property);
    $this->assertEquals('2014-01-03', $object->date_property->format('Y-m-d'));
  }

  public function test_can_set_datetime_attribute() {
    $tz = new DateTimeZone('UTC');

    $object = new PodioObject();
    $object->property('datetime_property', 'datetime');
    $object->init(array('datetime_property' => new DateTime('2014-01-01 12:00:00', $tz)));
    $this->assertInstanceOf('DateTime', $object->datetime_property);
    $this->assertEquals('2014-01-01 12:00:00', $object->datetime_property->format('Y-m-d H:i:s'));

    $object->datetime_property = new DateTime('2014-01-02 14:00:00', $tz);
    $this->assertInstanceOf('DateTime', $object->datetime_property);
    $this->assertEquals('2014-01-02 14:00:00', $object->datetime_property->format('Y-m-d H:i:s'));

    $object->datetime_property = '2014-01-03 14:00:00';
    $this->assertInstanceOf('DateTime', $object->datetime_property);
    $this->assertEquals('2014-01-03 14:00:00', $object->datetime_property->format('Y-m-d H:i:s'));
  }

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

  // public function test_can_convert_to_json() {
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

}
