<?php
class PodioItemFieldCollectionTest extends PHPUnit_Framework_TestCase {

  public function setup() {
    $this->collection = new PodioItemFieldCollection(array(
      new PodioItemField(array('field_id' => 1, 'external_id' => 'a', 'type' => 'text')),
      new PodioItemField(array('field_id' => 2, 'external_id' => 'b', 'type' => 'number')),
      new PodioItemField(array('field_id' => 3, 'external_id' => 'c', 'type' => 'calculation')),
    ));
  }

  public function test_can_construct_from_array() {
    $collection = new PodioItemFieldCollection(array(
      array('field_id' => 1, 'type' => 'text'),
      array('field_id' => 2, 'type' => 'number'),
      array('field_id' => 3, 'type' => 'calculation'),
    ));
    $this->assertEquals(3, count($collection));
    $this->assertEquals('PodioTextItemField', get_class($collection[0]));
    $this->assertEquals('PodioNumberItemField', get_class($collection[1]));
    $this->assertEquals('PodioCalculationItemField', get_class($collection[2]));
  }

  public function test_can_construct_from_objects() {
    $collection = new PodioItemFieldCollection(array(
      new PodioTextItemField(array('field_id' => 1, 'external_id' => 'a', 'type' => 'text')),
      new PodioNumberItemField(array('field_id' => 2, 'external_id' => 'b', 'type' => 'number')),
      new PodioCalculationItemField(array('field_id' => 3, 'external_id' => 'c', 'type' => 'calculation')),
    ));

    $this->assertEquals(3, count($collection));
  }

  public function test_can_add_unknown_type() {
    $collection = new PodioItemFieldCollection(array(
      array('field_id' => 1, 'type' => 'invalid_field_type'),
    ));

    $this->assertEquals(1, count($collection));
    $this->assertEquals('PodioItemField', get_class($collection[0]));
  }

  public function test_can_add_field() {
    $length = count($this->collection);
    $this->collection[] = new PodioTextItemField(array('field_id' => 4, 'external_id' => 'd'));

    $this->assertEquals($length+1, count($this->collection));
  }

  /**
    * @expectedException PodioDataIntegrityError
    */
  public function test_cannot_add_app_field() {
    $this->collection[] = new PodioAppField();
  }

}
