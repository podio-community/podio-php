<?php
/**
 * @see https://developers.podio.com/doc/items
 */
class PodioItemField extends PodioObject {
  public function __construct($attributes = array()) {
    $this->property('field_id', 'integer');
    $this->property('type', 'string');
    $this->property('external_id', 'string');
    $this->property('label', 'string');
    $this->property('values', 'array');
    $this->property('config', 'hash');

    $this->init($attributes);
  }

  /**
   * @see https://developers.podio.com/doc/items/update-item-field-values-22367
   */
  public static function update($item_id, $field_id, $attributes = array()) {
    $url = "/item/{$item_id}/value/{$field_id}";
    if (isset($attributes['silent']) && $attributes['silent'] == 1) {
      $url .= '?silent=1';
      unset($attributes['silent']);
    }
    return Podio::put($url, $attributes)->json_body();
  }

  /**
   * @see https://developers.podio.com/doc/calendar/get-item-field-calendar-as-ical-10195681
   */
  public static function ical($item_id, $field_id) {
    return Podio::get("/calendar/item/{$item_id}/field/{$field_id}/ics/", $attributes)->body;
  }

}
