<?php
class PodioAppItemFieldTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->object = new PodioAppItemField(array(
      '__api_values' => true,
      'values' => array(
        array('value' => array('item_id' => 1, 'title' => 'Snap')),
        array('value' => array('item_id' => 2, 'title' => 'Crackle')),
        array('value' => array('item_id' => 3, 'title' => 'Pop')),
      )
    ));
    }

    public function test_can_construct_from_simple_value()
    {
        $object = new PodioAppItemField(array(
      'field_id' => 123,
      'values' => array('item_id' => 4, 'title' => 'Captain Crunch')
    ));
        $this->assertEquals(array(
      array('value' => array('item_id' => 4, 'title' => 'Captain Crunch')),
    ), $object->__attribute('values'));
    }

    public function test_can_provide_value()
    {
        // Empty values
        $empty_values = new PodioAppItemField(array('field_id' => 1));
        $this->assertNull($empty_values->values);

        // Populated values
        $this->assertInstanceOf('PodioCollection', $this->object->values);
        $this->assertEquals(3, count($this->object->values));
        foreach ($this->object->values as $value) {
            $this->assertInstanceOf('PodioItem', $value);
        }
    }

    public function test_can_set_value_from_object()
    {
        $this->object->values = new PodioItem(array('item_id' => 4, 'title' => 'Captain Crunch'));
        $this->assertEquals(array(
      array('value' => array('item_id' => 4, 'title' => 'Captain Crunch'))
    ), $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_collection()
    {
        $this->object->values = new PodioCollection(array(new PodioItem(array('item_id' => 4, 'title' => 'Captain Crunch'))));

        $this->assertEquals(array(
      array('value' => array('item_id' => 4, 'title' => 'Captain Crunch'))
    ), $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_hash()
    {
        $this->object->values = array('item_id' => 4, 'title' => 'Captain Crunch');
        $this->assertEquals(array(
      array('value' => array('item_id' => 4, 'title' => 'Captain Crunch')),
    ), $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_array_of_objects()
    {
        $this->object->values = array(
      new PodioItem(array('item_id' => 4, 'title' => 'Captain Crunch')),
      new PodioItem(array('item_id' => 5, 'title' => 'Count Chocula'))
    );
        $this->assertEquals(array(
      array('value' => array('item_id' => 4, 'title' => 'Captain Crunch')),
      array('value' => array('item_id' => 5, 'title' => 'Count Chocula')),
    ), $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_array_of_hashes()
    {
        $this->object->values = array(
      array('item_id' => 4, 'title' => 'Captain Crunch'),
      array('item_id' => 5, 'title' => 'Count Chocula')
    );
        $this->assertEquals(array(
      array('value' => array('item_id' => 4, 'title' => 'Captain Crunch')),
      array('value' => array('item_id' => 5, 'title' => 'Count Chocula')),
    ), $this->object->__attribute('values'));
    }

    public function test_can_humanize_value()
    {
        // Empty values
        $empty_values = new PodioAppItemField(array('field_id' => 1));
        $this->assertEquals('', $empty_values->humanized_value());

        // Populated values
        $this->assertEquals('Snap;Crackle;Pop', $this->object->humanized_value());
    }

    public function test_can_convert_to_api_friendly_json()
    {
        // Empty values
        $empty_values = new PodioAppItemField(array('field_id' => 1));
        $this->assertEquals('[]', $empty_values->as_json());

        // Populated values
        $this->assertEquals('[1,2,3]', $this->object->as_json());
    }
}
