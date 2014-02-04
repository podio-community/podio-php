<?php
class PodioItemCollection extends PodioCollection {
  public $filtered;
  public $total;

  /**
   * @param $items An array of PodioItem objects
   * @param $filtered Count of items in current selected
   * @param $total Total number of items if no filters were to apply
   */
  public function __construct($items, $filtered, $total) {
    $this->filtered = $filtered;
    $this->total = $total;

    parent::__construct($items);
  }

  // Array access
  public function offsetSet($offset, $value) {
    if (!is_a($value, 'PodioItem')) {
      throw new Exception("Objects in PodioItemCollection must be of class PodioItem");
    }
    parent::offsetSet($offset, $value);
  }

}
