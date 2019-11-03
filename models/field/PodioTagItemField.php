<?php

/**
 * Tag Field. Undocumented by Podio, but occurs as unremovable default field "Organization" in contact apps.
 */
class PodioTagItemField extends PodioItemField
{

  public function api_friendly_values()
  {
    return $this->values !== null ? $this->values : null;
  }

}