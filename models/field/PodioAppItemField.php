<?php

/**
 * App reference field
 */
class PodioAppItemField extends PodioItemField
{
  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value)
  {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a PodioCollection of PodioEmbed objects
   */
  public function __get($name)
  {
    $attribute = parent::__get($name);
    if ($name == 'values' && $attribute) {
      // Create PodioCollection from raw values
      $collection = new PodioCollection();
      foreach ($attribute as $value) {
        $collection[] = new PodioItem($value['value']);
      }
      return $collection;
    }
    return $attribute;
  }

  public function humanized_value()
  {
    if (!$this->values) {
      return '';
    }

    $values = array();
    foreach ($this->values as $value) {
      $values[] = $value->title;
    }
    return join(';', $values);
  }

  public function set_value($values)
  {
    if ($values) {
      // Ensure that we have an array of values
      if (is_a($values, 'PodioCollection')) {
        $values = $values->_get_items();
      }
      if (is_object($values) || (is_array($values) && !empty($values['item_id']))) {
        $values = array($values);
      }

      $values = array_map(function ($value) {
        if (is_object($value)) {
          return array('value' => $value->as_json(false));
        }
        return array('value' => $value);
      }, $values);

      parent::__set('values', $values);
    }
  }

  public function api_friendly_values()
  {
    if (!$this->values) {
      return array();
    }
    $list = array();
    foreach ($this->values as $value) {
      $list[] = $value->item_id;
    }
    return $list;
  }
}