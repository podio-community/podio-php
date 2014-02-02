<?php
class PodioAppFieldCollection extends PodioFieldCollection {

  public function __construct($attributes) {

    // Make default array into array of proper objects
    $fields = array();

    foreach ($attributes as $field_attributes) {
      $field = is_object($field_attributes) ? $field_attributes : new PodioAppField($field_attributes);
      $fields[] = $field;
    }

    // Add to internal storage
    parent::__construct($fields);
  }

  public function offsetSet($offset, $field) {

    if (!is_a($field, PodioAppField)) {
      throw new Exception("Objects in PodioAppFieldCollection must be of class PodioAppField");
    }

    parent::offsetSet($offset, $field);
  }

}
