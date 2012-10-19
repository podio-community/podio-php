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

  /**
   * Returns API friendly values for item field for use when saving item
   */
  public function api_friendly_values() {
    if (!$this->values) {
      return null;
    }
    switch ($this->type) {
      case 'contact': // profile_id
      case 'app': // item_id
      case 'question': // id
      case 'category': //id
      case 'image': // file_id
      case 'video': // file_id
      case 'file': // file_id
        $list = array();
        foreach ($this->values as $value) {
          if (!empty($value['value']['id'])) {
            $list[] = $value['value']['id'];
          }
          elseif (!empty($value['value']['file_id'])) {
            $list[] = $value['value']['file_id'];
          }
          elseif (!empty($value['value']['item_id'])) {
            $list[] = $value['value']['item_id'];
          }
          elseif (!empty($value['value']['profile_id'])) {
            $list[] = $value['value']['profile_id'];
          }
        }
        return $list;
        break;
      case 'embed':
        $list = array();
        foreach ($this->values as $value) {
          $list[] = array('embed' => $value['embed']['embed_id'], 'file' => $value['file']['file_id']);
        }
        return $list;
        break;
      case 'location':
        $list = array();
        foreach ($this->values as $value) {
          $list[] = $value['value'];
        }
        return $list;
        break;
      case 'text':
      case 'number':
      case 'money':
      case 'date':
      case 'progress':
      case 'state':
      case 'duration':
      default:
        return $this->values[0]['value'];
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
