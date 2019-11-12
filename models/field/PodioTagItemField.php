<?php

/**
 * Tag Field. Undocumented by Podio, but occurs as unremovable default field "Organization" in contact apps.
 */
class PodioTagItemField extends PodioItemField
{

  public function api_friendly_values()
  {
    return $this->values ? $this->values : array();
  }

  public function humanized_value()
  {
    if (!$this->values) {
      return '';
    }
    return join(';', array_map(function ($value) {
      return $value['value'];
    }, $this->values));
  }

}