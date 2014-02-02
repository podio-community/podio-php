<?php
class PodioFieldCollection extends PodioCollection {
  private $__belongs_to;

  public function __construct($fields) {
    parent::__construct($fields);
  }

  // Array access with support for external ids.
  public function offsetSet($offset, $field) {

    // Allow you to set external id in the array offset.
    // E.g. $collection['external_id'] = $field;
    if (is_string($offset)) {
      $field->external_id = $offset;
      $offset = null;
    }

    if (!$field->id && !$field->external_id) {
      throw new PodioDataIntegrityError('Field must have id or external_id set.');
    }

    // Remove any existing field with this id
    $this->remove($field->id ? $field->id : $field->external_id);

    // Add to internal storage
    parent::offsetSet($offset, $field);
  }

  public function offsetExists($offset) {
    if (is_string($offset)) {
      return $this->get($offset) ? true : false;
    }
    return parent::offsetExists($offset);
  }
  public function offsetUnset($offset) {
    if (is_string($offset)) {
      $this->remove($offset);
    }
    else {
      parent::offsetUnset($offset);
    }
  }
  public function offsetGet($offset) {
    if (is_string($offset)) {
      return $this->get($offset);
    }
    return parent::offsetGet($offset);
  }

  /**
   * Get all fields of a specific type.
   */
  public function fields_of_type($type) {
    $list = array();
    foreach ($this as $field) {
      if ($field->type == $type) {
        $list[] = $field;
      }
    }
    return $list;
  }

  /**
   * Returns all external_ids in use on this item
   */
  public function external_ids() {
    return array_map(function($field){
      return $field->external_id;
    }, $this->_get_items());
  }

}
