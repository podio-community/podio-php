<?php
class PodioCollection implements IteratorAggregate, ArrayAccess, Countable {
  private $__items = array();

  /**
   * @param $items An array of PodioItem objects
   */
  public function __construct($items = array()) {
    foreach ($items as $item) {
      $this->offsetSet(null, $item);
    }
  }

  public function __toString() {
    $items = array();
    foreach($this->__items as $item) {
      $items[] = $item->as_json(false);
    }
    return print_r($items, true);
  }

  // Countable
  public function count() {
    return count($this->__items);
  }

  // Iterator
  public function getIterator() {
    return new ArrayIterator($this->__items);
  }

  // Array access
  public function offsetSet($offset, $value) {
    if (!is_a($value, PodioObject)) {
      throw new Exception("Objects in PodioCollection must be of class PodioObject");
    }
    if (is_null($offset)) {
      $this->__items[] = $value;
    }
    else {
      $this->__items[$offset] = $value;
    }
  }
  public function offsetExists($offset) {
    return isset($this->__items[$offset]);
  }
  public function offsetUnset($offset) {
      unset($this->__items[$offset]);
  }
  public function offsetGet($offset) {
    return isset($this->__items[$offset]) ? $this->__items[$offset] : null;
  }

}
