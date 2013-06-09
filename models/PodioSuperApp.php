<?php
/**
 * This class contains convenience methods for working with apps.
 * Both PodioApp and PodioItem inherit from this class.
 * This structure is mainly in place to make it easier to work
 * with collections og PodioAppField and PodioItemField.
 */
class PodioSuperApp extends PodioObject {
  /**
   * Returns the field matching the given field_id or external_id
   */
  public function field($field_id_or_external_id) {
    $key = is_int($field_id_or_external_id) ? 'field_id' : 'external_id';
    foreach ($this->fields as $field) {
      if ($field->{$key} == $field_id_or_external_id) {
        return $field;
      }
    }
    return null;
  }

  /**
   * Adds a field. Will replace any current field with the same ID
   */
  public function add_field($field) {
    $this->fields = $this->fields ? $this->fields : array();

    if (!$field->id && !$field->external_id) {
      throw new PodioDataIntegrityError('Field must have id or external_id set.');
    }
    $this->remove_field($field->id ? $field->id : $field->external_id);

    $this->fields = array_merge($this->fields, array($field));

  }

  /**
   * Removes a field.
   */
  public function remove_field($field_id_or_external_id) {
    if (!$this->fields) {
      return true;
    }
    $this->fields = array_filter($this->fields, function($field) use ($field_id_or_external_id) {
      return !($field->id == $field_id_or_external_id || $field->external_id == $field_id_or_external_id);
    });
  }

  /**
   * Returns all fields of the given type on this item
   */
  public function fields_of_type($type) {
    $list = array();
    foreach ($this->fields as $field) {
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
    }, $this->fields);
  }
}
