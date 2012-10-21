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
   * Saves the value of the field
   */
  public function save($silent = false) {
    if ($this->belongs_to) {
      $attributes = $this->as_json(false);
      if ($silent) {
        $attributes['silent'] = true;
      }
      return self::update($this->belongs_to['instance']->id, $this->id, $attributes);
    }
    else {
      throw new PodioMissingRelationshipError('{"error_description":"Field is missing relationship to item"}', null, null);
    }
  }

  /**
   * Overwrites normal as_json to use api_friendly_values
   */
  public function as_json($encoded = true) {
    $result = $this->api_friendly_values();
    return $encoded ? json_encode($result) : $result;
  }

  /**
   * Set the value of the field
   */
  public function set_value($values) {
    if (!$values) {
      $this->attributes['values'] = array();
    }
    else {
      switch ($this->type) {
        case 'contact': // profile_id
        case 'app': // item_id
        case 'question': // id
        case 'category': // id
        case 'image': // file_id
        case 'video': // file_id
        case 'file': // file_id
          $list = array();
          $id_key = 'file_id';
          if (in_array($this->type, array('category', 'question'))) {
            $id_key = 'id';
          }
          elseif ($this->type == 'app') {
            $id_key = 'item_id';
          }
          elseif ($this->type == 'contact') {
            $id_key = 'profile_id';
          }

          if (!is_array($values) || (is_array($values) && isset($values[$id_key]))) {
            $values = array($values);
          }
          foreach ($values as $value) {
            if (is_int($value)) {
              $list[] = array('value' => array($id_key => $value));
            }
            elseif (is_array($value)) {
              // We have a hash, just let it pass through
              $list[] = array('value' => $value);
            }
          }
          $this->attributes['values'] = $list;
          break;
        case 'embed':
          if (!isset($values['embed'])) {
            // Multiple values
            $this->attributes['values'] = $values;
          }
          else {
            // Single value
            $this->attributes['values'] = array($values);
          }
          break;
        case 'location':
          if (is_array($values)) {
            $formatted_values = array_map(function($value){
              return array('value' => $value);
            }, $values);
            $this->attributes['values'] = $formatted_values;
          }
          else {
            $this->attributes['values'] = array(array('value' => $values));
          }
          break;
        case 'date':
          $this->attributes['values'] = array($values);
          break;
        // Fields without multiple values
        case 'text':
        case 'number':
        case 'money':
        case 'progress':
        case 'state':
        case 'duration':
        case 'calculation':
        default:
          $this->attributes['values'] = array(array('value' => $values));
          break;
      }
    }
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
      case 'date':
        return array('start' => $this->values[0]['start'], 'end' => $this->values[0]['end']);
        break;
      case 'text':
      case 'number':
      case 'money':
      case 'progress':
      case 'state':
      case 'duration':
      case 'calculation':
      default:
        return $this->values;
        break;
    }
  }

  /**
   * Displays a human-friendly value for the field
   */
  public function humanized_value() {
    if (!$this->values || sizeof($this->values) == 0) {
      return '';
    }
    switch ($this->type) {
      case 'question':
      case 'category':
        return join('; ', array_map(function($value){
            return $value['value']['text'];
          }, $this->attributes['values']));
        break;
      case 'image':
      case 'video':
      case 'file':
      case 'contact':
        return join('; ', array_map(function($value){
          return $value['value']['name'];
        }, $this->attributes['values']));
        break;
      case 'app':
        return join('; ', array_map(function($value){
          return $value['value']['title'];
        }, $this->attributes['values']));
        break;
      case 'embed':
        return join('; ', array_map(function($value){
          return $value['embed']['original_url'];
        }, $this->attributes['values']));
        break;
      case 'location':
        return join('; ', array_map(function($value){
                  return $value['value'];
                }, $this->attributes['values']));
        break;
      case 'date':
        $value = $this->attributes['values'][0];
        // Remove seconds from start and end times since they are always '00' anyway.
        if (!empty($value['start_time'])) {
          $value['start_time'] = substr($value['start_time'], 0, strrpos($value['start_time'], ':'));
        }
        if (!empty($value['end_time'])) {
          $value['end_time'] = substr($value['end_time'], 0, strrpos($value['end_time'], ':'));
        }
        // Variants:

        // Same date
        // 2012-12-12
        // 2012-12-12 14:00
        // 2012-12-12 14:00 - 15:00

        // Different dates
        // 2012-12-12 - 2012-12-14
        // 2012-12-12 14:00 - 2012-12-14
        // 2012-12-12 14:00 - 2012-12-12 15:00

        if (empty($value['end_date']) || $value['start_date'] == $value['end_date']) {
          if (!empty($value['start_time']) && !empty($value['end_time']) && $value['start_time'] != $value['end_time']) {
            return "{$value['start_date']} {$value['start_time']}-{$value['end_time']}";
          }
          elseif (!empty($value['start_time']) && (empty($value['end_time']) || $value['start_time'] == $value['end_time'])) {
            return "{$value['start_date']} {$value['start_time']}";
          }
          else {
            return "{$value['start_date']}";
          }
        }
        else {
          if (!empty($value['start_time']) && !empty($value['end_time']) && $value['end_time'] != '00:00') {
            return "{$value['start_date']} {$value['start_time']} - {$value['end_date']} {$value['end_time']}";
          }
          elseif (!empty($value['end_time']) || $value['end_time'] == '00:00') {
            return "{$value['start_date']} {$value['start_time']} - {$value['end_date']}";
          }
          else {
            return "{$value['start_date']} - {$value['end_date']}";
          }
        }
        break;
      case 'progress':
        return $this->attributes['values'][0]['value'].'%';
        break;
      case 'money':
        $amount = number_format($this->attributes['values'][0]['value'], 2, '.', '');
          switch ($this->attributes['values'][0]['currency']) {
            case 'USD':
              $currency = '$';
            case 'EUR':
              $currency = '€';
              break;
            case 'GBP':
              $currency = '£';
              break;
            default:
              $currency = $this->attributes['values'][0]['currency'].' ';
              break;
          }
          return $currency.$amount;
        break;
      case 'number':
      case 'calculation':
        return rtrim(rtrim(number_format($this->attributes['values'][0]['value'], 4, '.', ''), '0'), '.');
        break;
      case 'text':
        return strip_tags($this->attributes['values'][0]['value']);
        break;
      case 'state':
      case 'duration':
      default:
        return $this->attributes['values'][0]['value'];
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
