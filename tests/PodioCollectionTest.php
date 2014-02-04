<?php
class PodioCollectionTest extends PHPUnit_Framework_TestCase {

  public function setup() {
    $this->collection = new PodioCollection(array(
      new PodioObject(array('id' => 1)),
      new PodioObject(array('id' => 2)),
      new PodioObject(array('id' => 3))
    ));
  }

  public function test_can_get_by_offset() {
    $item = $this->collection[1];
    $this->assertEquals(2, $item->id);
  }

  public function test_can_iterate() {
    $checklist = array(1, 2, 3);
    foreach ($this->collection as $offset => $item) {
      $this->assertEquals($checklist[$offset], $item->id);
    }
  }

  public function test_can_provide_length() {
    $this->assertEquals(3, count($this->collection));
  }

  /**
    * @expectedException PodioDataIntegrityError
    */
  public function test_cannot_add_string() {
    $this->collection[] = 'Sample String';
  }

  public function test_can_add_object() {
    $length = count($this->collection);
    $this->collection[] = new PodioObject();

    $this->assertEquals($length+1, count($this->collection));
  }

}
