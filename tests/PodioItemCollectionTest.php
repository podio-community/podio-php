<?php
class PodioItemCollectionTest extends \PHPUnit\Framework\TestCase {

  public function test_cannot_add_object() {
    $this->expectException('PodioDataIntegrityError');
    $collection = new PodioItemCollection();
    $collection[] = new PodioObject();
  }

  public function test_can_add_item() {
    $collection = new PodioItemCollection();
    $length = count($collection);
    $collection[] = new PodioItem(array('item_id' => 1, 'external_id' => 'a'));

    $this->assertEquals($length+1, count($collection));
  }

}
