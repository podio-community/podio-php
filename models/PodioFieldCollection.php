<?php
class PodioFieldCollection extends PodioCollection {

  // Array access
  public function offsetSet($offset, $field) {

    if (!(is_a($field, PodioItemField) || is_a($field, PodioAppField))) {
      throw new Exception("Objects in PodioFieldCollection must be of class PodioItemField or PodioAppField");
    }

    if (!$field->id && !$field->external_id) {
      throw new PodioDataIntegrityError('Field must have id or external_id set.');
    }

    // TODO: Remove any existing field
    // TODO: Add relationship to parent Item or App
    // TODO: Add field to $this->__items internal array

    parent::offsetSet($offset, $field);
  }

  /**
   * Get item from collection by item_id or external_id
   */
  public function get($item_id_or_external_id) {
    // TODO: Do it
    // Could alternatively be done by overwriting offsetGet so you can do $field_collection["external_id"] or $field_collection[field_id]
    // Not sure if that's a good idea or not.
  }

  // TODO: Replace methods in SuperApp with methods here. add_field -> offsetSet, remove_field -> offsetUnset, fields_of_type, external_ids, field methods

}
