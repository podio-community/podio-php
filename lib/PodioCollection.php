<?php
/**
 * Provides a very simple iterator and array access interface to a collection
 * of PodioObject models.
 */
class PodioCollection implements IteratorAggregate, ArrayAccess, Countable {
  private $__items = array();
  private $__belongs_to;

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

    // If the collection has a relationship with a parent, add it to the item as well.
    $relationship = $this->relationship();
    if ($relationship) {
      $value->add_relationship($relationship['instance'], $relationship['property']);
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

  public function _get_items() {
    return $this->__items;
  }

  public function _set_items($items) {
    $this->__items = $items;
  }

  public function relationship() {
    return $this->__belongs_to;
  }

  public function add_relationship($instance, $property = 'fields') {
    $this->__belongs_to = array('property' => $property, 'instance' => $instance);

    // Add relationship to all individual fields as well.
    foreach ($this as $item) {
      $item->add_relationship($instance, $property);
    }
  }

  public function get($id_or_external_id) {
    $key = is_int($id_or_external_id) ? 'id' : 'external_id';
    foreach ($this as $items) {
      if ($items->{$key} == $id_or_external_id) {
        return $items;
      }
    }
    return null;
  }

  public function remove($id_or_external_id) {
    if (count($this) === 0) {
      return true;
    }
    $this->_set_items(array_filter($this->_get_items(), function($item) use ($id_or_external_id) {
      return !($item->id == $id_or_external_id || $item->external_id == $id_or_external_id);
    }));
  }


}
