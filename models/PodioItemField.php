<?php
/**
 * @see https://developers.podio.com/doc/items
 */
class PodioItemField extends PodioObject {
  public function __construct($attributes = array()) {
    $this->property('field_id', 'integer', array('id' => true));
    $this->property('type', 'string');
    $this->property('external_id', 'string');
    $this->property('label', 'string');
    $this->property('values', 'array');
    $this->property('config', 'hash');

    $this->init($attributes);
  }

  public function api_friendly_values() {
    if (!$this->values) {
      return null;
    }
    switch ($this->type) {
      case 'contact':
      case 'app':
      case 'image':
      case 'question':
      case 'category':
      case 'video':
      case 'file':
        // Array of ints
        $list = array();
        foreach ($this->values as $value) {

        }
        break;
      case 'embed':
        break;
      case 'text':
      case 'number':
      case 'money':
      case 'date':
      case 'progress':
      case 'state':
      case 'duration':
      case 'location':
      default:
        return $this->values[0];
        break;
    }
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
    return Podio::get("/calendar/item/{$item_id}/field/{$field_id}/ics/")->body;
  }

  /**
   * @see https://developers.podio.com/doc/calendar/get-item-field-calendar-as-ical-10195681
   */
  public static function ical_field($item_id, $field_id) {
    return Podio::get("/calendar/item/{$item_id}/field/{$field_id}/ics/")->body;
  }

}
