<?php
class PodioAppFieldCollectionTest extends PHPUnit_Framework_TestCase {

  public function setup() {
    $this->collection = new PodioAppFieldCollection(array(
      new PodioAppField(array('field_id' => 1, 'external_id' => 'a', 'type' => 'text')),
      new PodioAppField(array('field_id' => 2, 'external_id' => 'b', 'type' => 'number')),
      new PodioAppField(array('field_id' => 3, 'external_id' => 'c', 'type' => 'calculation')),
    ));
  }

  public function test_can_construct_from_array() {
    $collection = new PodioAppFieldCollection(array(
      array('field_id' => 1),
      array('field_id' => 2),
      array('field_id' => 3),
    ));
    $this->assertEquals(3, count($collection));
  }

  public function test_can_construct_from_objects() {
    $collection = new PodioAppFieldCollection(array(
      new PodioAppField(array('field_id' => 1, 'external_id' => 'a', 'type' => 'text')),
      new PodioAppField(array('field_id' => 2, 'external_id' => 'b', 'type' => 'number')),
      new PodioAppField(array('field_id' => 3, 'external_id' => 'c', 'type' => 'calculation')),
    ));

    $this->assertEquals(3, count($collection));
  }

  public function test_can_add_field() {
    $length = count($this->collection);
    $this->collection[] = new PodioAppField(array('field_id' => 4, 'external_id' => 'd'));

    $this->assertEquals($length+1, count($this->collection));
  }

  /**
    * @expectedException PodioDataIntegrityError
    */
  public function test_cannot_add_item_field() {
    $this->collection[] = new PodioItemField();
  }

}
