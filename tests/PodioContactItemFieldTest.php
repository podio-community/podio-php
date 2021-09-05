<?php

class PodioContactItemFieldTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->object = new PodioContactItemField(array(
      '__api_values' => true,
      'values' => array(
        array('value' => array('profile_id' => 1, 'name' => 'Snap')),
        array('value' => array('profile_id' => 2, 'name' => 'Crackle')),
        array('value' => array('profile_id' => 3, 'name' => 'Pop')),
      )
    ));
    }

    public function test_can_construct_from_simple_value()
    {
        $object = new PodioContactItemField(array(
      'field_id' => 123,
      'values' => array('profile_id' => 4, 'name' => 'Captain Crunch')
    ));
        $this->assertEquals(array(
      array('value' => array('profile_id' => 4, 'name' => 'Captain Crunch')),
    ), $object->__attribute('values'));
    }

    public function test_can_provide_value()
    {
        // Empty values
        $empty_values = new PodioContactItemField(array('field_id' => 1));
        $this->assertNull($empty_values->values);

        // Populated values
        $this->assertInstanceOf('PodioCollection', $this->object->values);
        $this->assertEquals(3, count($this->object->values));
        foreach ($this->object->values as $value) {
            $this->assertInstanceOf('PodioContact', $value);
        }
    }

    public function test_can_set_value_from_object()
    {
        $this->object->values = new PodioContact(array('profile_id' => 4, 'name' => 'Captain Crunch'));
        $this->assertEquals(array(
      array('value' => array('profile_id' => 4, 'name' => 'Captain Crunch'))
    ), $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_collection()
    {
        $this->object->values = new PodioCollection(array(new PodioContact(array('profile_id' => 4, 'name' => 'Captain Crunch'))));

        $this->assertEquals(array(
      array('value' => array('profile_id' => 4, 'name' => 'Captain Crunch'))
    ), $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_hash()
    {
        $this->object->values = array('profile_id' => 4, 'name' => 'Captain Crunch');
        $this->assertEquals(array(
      array('value' => array('profile_id' => 4, 'name' => 'Captain Crunch')),
    ), $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_array_of_objects()
    {
        $this->object->values = array(
      new PodioContact(array('profile_id' => 4, 'name' => 'Captain Crunch')),
      new PodioContact(array('profile_id' => 5, 'name' => 'Count Chocula'))
    );
        $this->assertEquals(array(
      array('value' => array('profile_id' => 4, 'name' => 'Captain Crunch')),
      array('value' => array('profile_id' => 5, 'name' => 'Count Chocula')),
    ), $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_array_of_hashes()
    {
        $this->object->values = array(
      array('profile_id' => 4, 'name' => 'Captain Crunch'),
      array('profile_id' => 5, 'name' => 'Count Chocula')
    );
        $this->assertEquals(array(
      array('value' => array('profile_id' => 4, 'name' => 'Captain Crunch')),
      array('value' => array('profile_id' => 5, 'name' => 'Count Chocula')),
    ), $this->object->__attribute('values'));
    }

    public function test_can_humanize_value()
    {
        // Empty values
        $empty_values = new PodioContactItemField(array('field_id' => 1));
        $this->assertEquals('', $empty_values->humanized_value());

        // Populated values
        $this->assertEquals('Snap;Crackle;Pop', $this->object->humanized_value());
    }

    public function test_can_convert_to_api_friendly_json()
    {
        // Empty values
        $empty_values = new PodioContactItemField(array('field_id' => 1));
        $this->assertEquals('[]', $empty_values->as_json());

        // Populated values
        $this->assertEquals('[1,2,3]', $this->object->as_json());
    }
}
