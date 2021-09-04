<?php
class PodioObjectTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->object = new PodioObject();
        $this->object->property('id', 'integer');
        $this->object->property('external_id', 'string');
        $this->object->property('subscribed', 'boolean');
        $this->object->property('date', 'date');
        $this->object->property('created_on', 'datetime');
        $this->object->property('rights', 'array');
        $this->object->property('data', 'hash');
        $this->object->has_many('fields', 'Object');
        $this->object->has_one('created_by', 'Object');
        $this->object->has_one('reference_with_target', 'Object', array('json_target' => 'reference_target'));
        $this->object->init(array(
      'id' => 1,
      'external_id' => 'a',
      'rights' => array('view', 'update'),
      'subscribed' => true,
      'date' => '2011-05-31',
      'created_on' => '2012-12-24 14:00:00',
      'data' => array('item' => 'value')
    ));
    }

    public function test_can_construct_from_array()
    {
        $object = new PodioObject();
        $object->property('id', 'integer');
        $object->property('external_id', 'string');
        $object->property('string_property', 'string');
        $object->init(array('id' => 1, 'external_id' => 'a', 'string_property' => 'FooBar'));

        $this->assertEquals(1, $object->id);
        $this->assertEquals('a', $object->external_id);
        $this->assertEquals('FooBar', $object->string_property);
    }

    public function test_can_construct_from_id()
    {
        $object = new PodioObject();
        $object->property('id', 'integer');
        $object->init(1);

        $this->assertEquals(1, $object->id);
    }

    public function test_can_construct_from_external_id()
    {
        $object = new PodioObject();
        $object->property('external_id', 'string');
        $object->init('a');

        $this->assertEquals('a', $object->external_id);
    }

    public function test_can_construct_one_to_one_relationship()
    {
        $object = new PodioObject();
        $object->has_one('field', 'Object');
        $object->init(array('field' => array('id' => 1)));

        $this->assertInstanceOf('PodioObject', $object->field);
    }

    public function test_can_construct_one_to_many_relationship()
    {
        $object = new PodioObject();
        $object->has_many('fields', 'Object');
        $object->init(array('fields' => array(array('id' => 1), array('id' => 1))));

        $this->assertInstanceOf('PodioCollection', $object->fields);
        foreach ($object->fields as $member) {
            $this->assertInstanceOf('PodioObject', $member);
        }
    }

    public function test_can_provide_properties()
    {
        $this->assertEquals(array(
      'id' => array('type' => 'integer', 'options' => array()),
      'external_id' => array('type' => 'string', 'options' => array()),
      'subscribed' => array('type' => 'boolean', 'options' => array()),
      'date' => array('type' => 'date', 'options' => array()),
      'created_on' => array('type' => 'datetime', 'options' => array()),
      'rights' => array('type' => 'array', 'options' => array()),
      'data' => array('type' => 'hash', 'options' => array()),
      'fields' => array('type' => 'Object', 'options' => array()),
      'created_by' => array('type' => 'Object', 'options' => array()),
      'reference_with_target' => array('type' => 'Object', 'options' => array('json_target' => 'reference_target')),
    ), $this->object->properties());
    }

    public function test_can_provide_relationships()
    {
        $this->assertEquals(array(
      'fields' => 'has_many',
      'created_by' => 'has_one',
      'reference_with_target' => 'has_one'
    ), $this->object->relationships());
    }

    public function test_can_convert_to_json()
    {
        $created_by = new PodioObject();
        $created_by->property('id', 'integer');
        $created_by->property('name', 'string');
        $created_by->init(array('id' => 4, 'name' => 'Captain Crunch'));
        $this->object->created_by = $created_by;

        $reference_with_target = new PodioObject();
        $reference_with_target->property('id', 'integer');
        $reference_with_target->property('name', 'string');
        $reference_with_target->init(array('id' => 5, 'name' => 'Count Chocula'));
        $this->object->reference_with_target = $reference_with_target;

        $collection = new PodioCollection();
        for ($i=0;$i<3;$i++) {
            $field = new PodioObject();
            $field->property('id', 'integer');
            $field->init(array('id' => ($i+3)));
            $collection[] = $field;
        }
        $this->object->fields = $collection;

        $this->assertEquals('{"id":1,"external_id":"a","subscribed":true,"date":"2011-05-31","created_on":"2012-12-24 14:00:00","rights":["view","update"],"data":{"item":"value"},"fields":[{"id":3},{"id":4},{"id":5}],"created_by":{"id":4,"name":"Captain Crunch"},"reference_target":{"id":5,"name":"Count Chocula"}}', $this->object->as_json());
    }

    public function test_can_unset_attribute()
    {
        $this->assertEquals(1, $this->object->id);
        unset($this->object->id);
        $this->assertNull($this->object->id);
    }

    public function test_can_see_attribute_presence()
    {
        $this->assertTrue(isset($this->object->id));
        $this->assertFalse(isset($object->unknown_attribute));
    }

    public function test_can_set_integer_attribute()
    {
        $object = new PodioObject();
        $object->property('int_property', 'integer');
        $object->init(array('int_property' => 1));

        $this->assertEquals(1, $object->int_property);
    }

    public function test_can_set_boolean_attribute()
    {
        $object = new PodioObject();
        $object->property('bool_property', 'boolean');
        $object->property('bool2_property', 'boolean');
        $object->init(array('bool_property' => true, 'bool2_property' => false));

        $this->assertTrue($object->bool_property);
        $this->assertFalse($object->bool2_property);
    }

    public function test_can_set_string_attribute()
    {
        $object = new PodioObject();
        $object->property('string_property', 'string');
        $object->init(array('string_property' => 'FooBar'));

        $this->assertEquals('FooBar', $object->string_property);
    }

    public function test_can_set_array_attribute()
    {
        $object = new PodioObject();
        $object->property('array_property', 'array');
        $object->init(array('array_property' => array('a', 'b', 'c')));

        $this->assertEquals(array('a', 'b', 'c'), $object->array_property);
    }

    public function test_can_set_hash_attribute()
    {
        $object = new PodioObject();
        $object->property('hash_property', 'hash');
        $object->init(array('hash_property' => array('a' => 'a', 'b' => 'b', 'c' => 'c')));

        $this->assertEquals(array('a' => 'a', 'b' => 'b', 'c' => 'c'), $object->hash_property);
    }

    public function test_can_set_date_attribute_in_constructor()
    {
        $tz = new DateTimeZone('UTC');

        $object = new PodioObject();
        $object->property('date_property', 'date');
        $object->init(array('date_property' => new DateTime('2014-01-01 12:00:00', $tz)));
        $this->assertInstanceOf('DateTime', $object->date_property);
        $this->assertEquals('2014-01-01', $object->date_property->format('Y-m-d'));
    }

    public function test_can_set_date_attribute_from_datetime()
    {
        $tz = new DateTimeZone('UTC');

        $object = new PodioObject();
        $object->property('date_property', 'date');
        $object->date_property = new DateTime('2014-01-02 14:00:00', $tz);
        $this->assertInstanceOf('DateTime', $object->date_property);
        $this->assertEquals('2014-01-02', $object->date_property->format('Y-m-d'));
    }

    public function test_can_set_date_attribute_from_string()
    {
        $tz = new DateTimeZone('UTC');

        $object = new PodioObject();
        $object->property('date_property', 'date');
        $object->date_property = '2014-01-03';
        $this->assertInstanceOf('DateTime', $object->date_property);
        $this->assertEquals('2014-01-03', $object->date_property->format('Y-m-d'));
    }

    public function test_can_set_datetime_attribute_in_constructor()
    {
        $tz = new DateTimeZone('UTC');

        $object = new PodioObject();
        $object->property('datetime_property', 'datetime');
        $object->init(array('datetime_property' => new DateTime('2014-01-01 12:00:00', $tz)));
        $this->assertInstanceOf('DateTime', $object->datetime_property);
        $this->assertEquals('2014-01-01 12:00:00', $object->datetime_property->format('Y-m-d H:i:s'));
    }

    public function test_can_set_datetime_attribute_from_datetime()
    {
        $tz = new DateTimeZone('UTC');

        $object = new PodioObject();
        $object->property('datetime_property', 'datetime');
        $object->datetime_property = new DateTime('2014-01-02 14:00:00', $tz);
        $this->assertInstanceOf('DateTime', $object->datetime_property);
        $this->assertEquals('2014-01-02 14:00:00', $object->datetime_property->format('Y-m-d H:i:s'));
    }

    public function test_can_set_datetime_attribute_from_string()
    {
        $tz = new DateTimeZone('UTC');

        $object = new PodioObject();
        $object->property('datetime_property', 'datetime');
        $object->datetime_property = '2014-01-03 14:00:00';
        $this->assertInstanceOf('DateTime', $object->datetime_property);
        $this->assertEquals('2014-01-03 14:00:00', $object->datetime_property->format('Y-m-d H:i:s'));
    }

    public function test_can_create_listing()
    {
        $listing = PodioObject::listing(array(array('id' => 1), array('id' => 2)));
        $this->assertTrue(is_array($listing));
        foreach ($listing as $member) {
            $this->assertInstanceOf('PodioObject', $member);
        }
    }

    public function test_can_create_member()
    {
        $member = PodioObject::member(array('id' => 1));
        $this->assertInstanceOf('PodioObject', $member);
    }

    public function test_can_check_rights()
    {
        $this->assertTrue($this->object->can('view'));
        $this->assertFalse($this->object->can('delete'));
    }

    public function test_can_check_attribute_existence()
    {
        $this->assertTrue($this->object->has_attribute('rights'));
        $this->assertFalse($this->object->has_attribute('fields'));
    }

    public function test_can_check_property_existence()
    {
        $this->assertTrue($this->object->has_property('external_id'));
        $this->assertFalse($this->object->has_property('invalid_property_name'));
    }

    public function test_can_check_relationship_existence()
    {
        $this->assertTrue($this->object->has_relationship('fields'));
        $this->assertFalse($this->object->has_relationship('external_id'));
    }

    public function test_can_add_child_relationship()
    {
        $instance = new PodioObject();
        $this->object->add_relationship($instance, 'fields');

        $relationship = $this->object->relationship();
        $this->assertEquals($instance, $relationship['instance']);
        $this->assertEquals('fields', $relationship['property']);
    }
}
