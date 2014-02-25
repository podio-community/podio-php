<?php
/**
 * @see https://developers.podio.com/doc/items
 */
class PodioItemField extends PodioObject {
  public function __construct($attributes = array(), $force_type = null) {
    $this->property('field_id', 'integer', array('id' => true));
    $this->property('type', 'string');
    $this->property('external_id', 'string');
    $this->property('label', 'string');
    $this->property('values', 'array');
    $this->property('config', 'hash');
    $this->property('status', 'string');

    $this->init($attributes);

    $this->set_type_from_class_name();
  }

  /**
   * Saves the value of the field
   */
  public function save($options = array()) {
    $relationship = $this->relationship();
    if (!$relationship) {
      throw new PodioMissingRelationshipError('{"error_description":"Field is missing relationship to item"}', null, null);
    }
    if (!$this->id && !$this->external_id) {
      throw new PodioDataIntegrityError('Field must have id or external_id set.');
    }
    $attributes = $this->as_json(false);
    return self::update($relationship['instance']->id, $this->id ? $this->id : $this->external_id, $attributes, $options);
  }

  /**
   * Calling parent so we get all field attributes printed instead of only api_friendly_values
   */
  public function __toString() {
    return print_r(parent::as_json(false), true);
  }

  /**
   * Overwrites normal as_json to use api_friendly_values
   */
  public function as_json($encoded = true) {
    $result = $this->api_friendly_values();
    return $encoded ? json_encode($result) : $result;
  }

  /**
   * @see https://developers.podio.com/doc/items/update-item-field-values-22367
   */
  public static function update($item_id, $field_id, $attributes = array(), $options = array()) {
    $url = Podio::url_with_options("/item/{$item_id}/value/{$field_id}", $options);
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

  public function set_type_from_class_name() {
    switch (get_class($this)) {
      case 'PodioTextItemField':
        $this->type = 'text';
        break;
      case 'PodioEmbedItemField':
        $this->type = 'embed';
        break;
      case 'PodioLocationItemField':
        $this->type = 'location';
        break;
      case 'PodioDateItemField':
        $this->type = 'date';
        break;
      case 'PodioContactItemField':
        $this->type = 'contact';
        break;
      case 'PodioAppItemField':
        $this->type = 'app';
        break;
      case 'PodioQuestionItemField':
        $this->type = 'question';
        break;
      case 'PodioCategoryItemField':
        $this->type = 'category';
        break;
      case 'PodioImageItemField':
        $this->type = 'image';
        break;
      case 'PodioVideoItemField':
        $this->type = 'video';
        break;
      case 'PodioFileItemField':
        $this->type = 'file';
        break;
      case 'PodioNumberItemField':
        $this->type = 'number';
        break;
      case 'PodioProgressItemField':
        $this->type = 'progress';
        break;
      case 'PodioStateItemField':
        $this->type = 'state';
        break;
      case 'PodioDurationItemField':
        $this->type = 'duration';
        break;
      case 'PodioCalculationItemField':
        $this->type = 'calculation';
        break;
      case 'PodioMoneyItemField':
        $this->type = 'money';
        break;
      default:
        break;
    }
  }

}

/**
 * Text field
 */
class PodioTextItemField extends PodioItemField {

  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a string
   */
  public function __get($name) {
    $attribute = parent::__get($name);
    if ($name == 'values' && $attribute) {
      return $attribute[0]['value'];
    }
    return $attribute;
  }

  public function set_value($values) {
    parent::__set('values', $values ? array(array('value' => $values)) : array());
  }

  public function humanized_value() {
    return strip_tags($this->values);
  }

  public function api_friendly_values() {
    return $this->values ? $this->values : null;
  }

}

/**
 * Embed field
 */
class PodioEmbedItemField extends PodioItemField {

  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a PodioCollection of PodioEmbed objects
   */
  public function __get($name) {
    $attribute = parent::__get($name);
    if ($name == 'values' && $attribute) {
      // Create PodioCollection from raw values
      $embeds = new PodioCollection();
      foreach ($attribute as $value) {
        $embed = new PodioEmbed($value['embed']);
        if (!empty($value['file'])) {
          $embed->files = new PodioCollection(array(new PodioFile($value['file'])));
        }
        $embeds[] = $embed;
      }
      return $embeds;
    }
    return $attribute;
  }

  public function humanized_value() {
    if (!$this->values) {
      return '';
    }

    $values = array();
    foreach ($this->values as $value) {
      $values[] = $value->original_url;
    }
    return join(';', $values);
  }

  public function set_value($values) {
    if ($values) {
      // Ensure that we have an array of values
      if (is_a($values, 'PodioCollection')) {
        $values = $values->_get_items();
      }
      if (is_object($values) || (is_array($values) && !empty($values['embed']))) {
        $values = array($values);
      }

      $values = array_map(function($value) {
        if (is_object($value)) {
          $file = $value->files ? $value->files[0] : null;
          unset($value->files);

          return array('embed' => $value->as_json(false), 'file' => $file ? $file->as_json(false) : null);
        }
        return $value;
      }, $values);

      parent::__set('values', $values);
    }
  }

  public function api_friendly_values() {
    if (!$this->values) {
      return array();
    }
    $list = array();
    foreach ($this->values as $value) {
      $list[] = array('embed' => $value->embed_id, 'file' => ($value->files ? $value->files[0]->file_id : null) );
    }
    return $list;
  }

}

/**
 * Location field
 */
class PodioLocationItemField extends PodioItemField {

  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a string
   */
  public function __get($name) {
    $attribute = parent::__get($name);
    if ($name == 'values' && is_array($attribute)) {
      $list = array();
      foreach ($attribute as $value) {
        $list[] = $value['value'];
      }
      return $list;
    }
    return $attribute;
  }

  public function api_friendly_values() {
    return $this->values ? $this->values : null;
  }

  public function set_value($values) {
    if ($values) {
      if (is_array($values)) {
        $formatted_values = array_map(function($value){
          return array('value' => $value);
        }, $values);
        parent::__set('values', $formatted_values);
      }
      else {
        parent::__set('values', array(array('value' => $values)));
      }
    }
  }

  public function add_value($value) {
    if (!$this->values) {
      $this->set_value($value);
    } else {
      $values = $this->values;
      $values[] = $value;
      $this->set_value($values);
    }
  }

  public function humanized_value() {
    if (!$this->values) {
      return '';
    }

    return join(';', $this->values);
  }
}

/**
 * Date field
 */
class PodioDateItemField extends PodioItemField {

  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    elseif ($name == 'start') {
      if ($value === null) {
        return parent::__set('values', null);
      }
      return $this->set_value(array('start' => $value, 'end' => $this->end));
    }
    elseif ($name == 'end') {
      return $this->set_value(array('start' => $this->start, 'end' => $value));
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a string
   */
  public function __get($name) {
    $attribute = parent::__get($name);

    // When reading always provide UTC DateTime
    if ($name == 'values' && is_array($attribute)) {

      // We have to pick a timezone since PHP can't create a DateTime without one
      // Never rely on it for anything as the datetimes from the API come in the user's
      // local timezone.
      $tz = new DateTimeZone('UTC');
      $start = DateTime::createFromFormat('Y-m-d H:i:s', $attribute[0]['start_date'].' '.($attribute[0]['start_time'] ? $attribute[0]['start_time'] : '00:00:00'), $tz);
      if ($attribute[0]['start_date'] == $attribute[0]['end_date'] && !$attribute[0]['end_time']) {
        $end = null;
      }
      else {
        $end = DateTime::createFromFormat('Y-m-d H:i:s', $attribute[0]['end_date'].' '.($attribute[0]['end_time'] ? $attribute[0]['end_time'] : '00:00:00'), $tz);
      }

      return array('start' => $start, 'end' => $end);
    }
    elseif ($name == 'start') {
      return $this->values ? $this->values['start'] : null;
    }
    elseif ($name == 'end') {
      return $this->values ? $this->values['end'] : null;
    }
    return $attribute;
  }

  /**
   * True if start and end are on the same day.
   */
  public function same_day() {
    if (!$this->values || ($this->start && !$this->end)) {
      return true;
    }

    if ($this->start->format('Y-m-d') == $this->end->format('Y-m-d')) {
      return true;
    }
    return false;
  }

  /**
   * True if this is an allday event (has no time component on both start and end)
   */
  public function all_day() {
    if (!$this->values) {
      return false;
    }
    if (($this->start->format('H:i:s') == '00:00:00' && (!$this->end || ($this->end && $this->end->format('H:i:s') == '00:00:00')))) {
      return true;
    }
    return false;
  }

  public function set_value($values) {

    // If a start date isn't provided all we can do it null the value
    if (!$values['start']) {
      return parent::__set('values', null);
    }

    $formatted_values = array(
      'start_date' => null,
      'start_time' => null,
      'end_date' => null,
      'end_time' => null
    );

    if (is_string($values['start'])) {
      $values['start'] = $this->datetime_from_string($values['start']);
    }

    $formatted_values['start_date'] = $values['start']->format('Y-m-d');
    $formatted_values['start_time'] = $values['start']->format('H:i') == '00:00' ? null : $values['start']->format('H:i:s');

    if (!empty($values['end']) && is_string($values['end'])) {
      $values['end'] = $this->datetime_from_string($values['end']);
    }

    if (empty($values['end'])) {
      $formatted_values['end_date'] = $values['start']->format('Y-m-d');
      $formatted_values['end_time'] = null;
    }
    else {
      $formatted_values['end_date'] = $values['end']->format('Y-m-d');
      $formatted_values['end_time'] = $values['end']->format('H:i') == '00:00' ? null : $values['end']->format('H:i:s');
    }

    parent::__set('values', array($formatted_values));
  }

  public function datetime_from_string($string) {
    $tz = new DateTimeZone('UTC');

    $split = explode(' ', $string);
    if (count($split) == 1) {
      $split[] = '00:00:00';
    }
    return DateTime::createFromFormat('Y-m-d H:i:s', $split[0].' '.$split[1], $tz);
  }

  public function humanized_value() {
    $start = $this->start;
    $end = $this->end;

    if (!$start) {
      return '';
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

    if ($this->same_day()) {
      if (!$end) {
        return $start->format('H:i') == '00:00' ? $start->format('Y-m-d') : $start->format('Y-m-d H:i');
      }
      else {
        return $start->format('Y-m-d H:i').' - '.$end->format('H:i');
      }
    }
    else {
      if ($end->format('H:i') != '00:00') {
        return $start->format('Y-m-d H:i').' - '.$end->format('Y-m-d H:i');
      }
      elseif ($start->format('H:i') != '00:00' && $end->format('H:i') == '00:00') {
        return $start->format('Y-m-d H:i').' - '.$end->format('Y-m-d');
      }
      else {
        return $start->format('Y-m-d').' - '.$end->format('Y-m-d');
      }
    }
  }

  public function api_friendly_values() {
    if (!$this->start) {
      return array();
    }
    if ($this->end) {
      return array('start' => $this->start->format('Y-m-d H:i:s'), 'end' => $this->end->format('Y-m-d H:i:s'));
    }
    return array('start' => $this->start->format('Y-m-d H:i:s'));
  }

}

/**
 * Contact field
 */
class PodioContactItemField extends PodioItemField {
  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a PodioCollection of PodioEmbed objects
   */
  public function __get($name) {
    $attribute = parent::__get($name);
    if ($name == 'values' && $attribute) {
      // Create PodioCollection from raw values
      $collection = new PodioCollection();
      foreach ($attribute as $value) {
        $collection[] = new PodioContact($value['value']);
      }
      return $collection;
    }
    return $attribute;
  }

  public function humanized_value() {
    if (!$this->values) {
      return '';
    }

    $values = array();
    foreach ($this->values as $value) {
      $values[] = $value->name;
    }
    return join(';', $values);
  }

  public function set_value($values) {
    if ($values) {
      // Ensure that we have an array of values
      if (is_a($values, 'PodioCollection')) {
        $values = $values->_get_items();
      }
      if (is_object($values) || (is_array($values) && !empty($values['profile_id']))) {
        $values = array($values);
      }

      $values = array_map(function($value) {
        if (is_object($value)) {
          return array('value' => $value->as_json(false));
        }
        return array('value' => $value);
      }, $values);

      parent::__set('values', $values);
    }
  }

  public function api_friendly_values() {
    if (!$this->values) {
      return array();
    }
    $list = array();
    foreach ($this->values as $value) {
      $list[] = $value->profile_id;
    }
    return $list;
  }
}

/**
 * App reference field
 */
class PodioAppItemField extends PodioItemField {
  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a PodioCollection of PodioEmbed objects
   */
  public function __get($name) {
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

  public function humanized_value() {
    if (!$this->values) {
      return '';
    }

    $values = array();
    foreach ($this->values as $value) {
      $values[] = $value->title;
    }
    return join(';', $values);
  }

  public function set_value($values) {
    if ($values) {
      // Ensure that we have an array of values
      if (is_a($values, 'PodioCollection')) {
        $values = $values->_get_items();
      }
      if (is_object($values) || (is_array($values) && !empty($values['item_id']))) {
        $values = array($values);
      }

      $values = array_map(function($value) {
        if (is_object($value)) {
          return array('value' => $value->as_json(false));
        }
        return array('value' => $value);
      }, $values);

      parent::__set('values', $values);
    }
  }

  public function api_friendly_values() {
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

/**
 * Category field
 */
class PodioCategoryItemField extends PodioItemField {
  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a string
   */
  public function __get($name) {
    $attribute = parent::__get($name);
    if ($name == 'values' && is_array($attribute)) {
      $list = array();
      foreach ($attribute as $value) {
        $list[] = $value['value'];
      }
      return $list;
    }
    return $attribute;
  }

  public function api_friendly_values() {
    if (!$this->values) {
      return array();
    }
    $list = array();
    foreach ($this->values as $value) {
      $list[] = $value['id'];
    }
    return $list;
  }

  public function set_value($values) {
    if ($values) {
      if (is_array($values)) {
        $formatted_values = array_map(function($value){
          if (is_array($value)) {
            return array('value' => $value);
          }
          else {
            return array('value' => array('id' => $value));
          }
        }, $values);
        parent::__set('values', $formatted_values);
      }
      else {
        parent::__set('values', array(array('value' => array('id' => $values))));
      }
    }
  }

  public function add_value($value) {
    if (!$this->values) {
      $this->set_value($value);
    } else {
      $values = $this->values;
      $values[] = $value;
      $this->set_value($values);
    }
  }

  public function humanized_value() {
    if (!$this->values) {
      return '';
    }
    $list = array();
    foreach ($this->values as $value) {
      $list[] = isset($value['text']) ? $value['text'] : $value['id'];
    }

    return join(';', $list);
  }
}

/**
 * Question field
 */
class PodioQuestionItemField extends PodioCategoryItemField {}

/**
 * Asset field, super class for Image/Video/File fields
 */
class PodioAssetItemField extends PodioItemField {
  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a PodioCollection of PodioEmbed objects
   */
  public function __get($name) {
    $attribute = parent::__get($name);
    if ($name == 'values' && $attribute) {
      // Create PodioCollection from raw values
      $collection = new PodioCollection();
      foreach ($attribute as $value) {
        $collection[] = new PodioFile($value['value']);
      }
      return $collection;
    }
    return $attribute;
  }

  public function humanized_value() {
    if (!$this->values) {
      return '';
    }

    $values = array();
    foreach ($this->values as $value) {
      $values[] = $value->name;
    }
    return join(';', $values);
  }

  public function set_value($values) {
    $this->values = array();
    if ($values) {
      // Ensure that we have an array of values
      if (is_a($values, 'PodioCollection')) {
        $values = $values->_get_items();
      }
      if (is_object($values) || (is_array($values) && !empty($values['file_id']))) {
        $values = array($values);
      }

      $values = array_map(function($value) {
        if (is_object($value)) {
          return array('value' => $value->as_json(false));
        }
        return array('value' => $value);
      }, $values);

      parent::__set('values', $values);
    }
  }

  public function api_friendly_values() {
    if (!$this->values) {
      return array();
    }
    $list = array();
    foreach ($this->values as $value) {
      $list[] = $value->file_id;
    }
    return $list;
  }
}

/**
 * Image field
 */
class PodioImageItemField extends PodioAssetItemField {}

/**
 * Video field
 */
class PodioVideoItemField extends PodioAssetItemField {}

/**
 * File field
 */
class PodioFileItemField extends PodioAssetItemField {}

/**
 * Number field
 */
class PodioNumberItemField extends PodioItemField {

  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a string
   */
  public function __get($name) {
    $attribute = parent::__get($name);
    if ($name == 'values' && $attribute) {
      return $attribute[0]['value'];
    }
    return $attribute;
  }

  public function set_value($values) {
    parent::__set('values', $values ? array(array('value' => $values)) : array());
  }

  public function humanized_value() {
    if ($this->values === null) {
      return '';
    }
    return rtrim(rtrim(number_format($this->values, 4, '.', ''), '0'), '.');
  }

  public function api_friendly_values() {
    return $this->values !== null ? $this->values : null;
  }

}

/**
 * Progress field
 */
class PodioProgressItemField extends PodioItemField {
  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a string
   */
  public function __get($name) {
    $attribute = parent::__get($name);
    if ($name == 'values' && $attribute) {
      return $attribute[0]['value'];
    }
    return $attribute;
  }

  public function set_value($values) {
    parent::__set('values', $values ? array(array('value' => (int)$values)) : array());
  }

  public function humanized_value() {
    if ($this->values === null) {
      return '';
    }
    return $this->values.'%';
  }

  public function api_friendly_values() {
    return $this->values !== null ? $this->values : null;
  }
}

/**
 * State field
 */
class PodioStateItemField extends PodioItemField {

  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a string
   */
  public function __get($name) {
    $attribute = parent::__get($name);
    if ($name == 'values' && $attribute) {
      return $attribute[0]['value'];
    }
    return $attribute;
  }

  public function set_value($values) {
    parent::__set('values', $values ? array(array('value' => $values)) : array());
  }

  public function humanized_value() {
    return $this->values;
  }

  public function api_friendly_values() {
    return $this->values ? $this->values : null;
  }
}

/**
 * Duration field
 */
class PodioDurationItemField extends PodioItemField {

  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as an integer
   */
  public function __get($name) {
    $attribute = parent::__get($name);
    if ($name == 'values' && $attribute) {
      return $attribute[0]['value'];
    }
    elseif ($name == 'hours') {
      return floor($this->values/3600);
    }
    elseif ($name == 'minutes') {
      return (($this->values/60)%60);
    }
    elseif ($name == 'seconds') {
      return ($this->values%60);
    }
    return $attribute;
  }

  public function set_value($values) {
    parent::__set('values', $values ? array(array('value' => (int)$values)) : array());
  }

  public function humanized_value() {
    $list = array(str_pad($this->hours, 2, '0', STR_PAD_LEFT), str_pad($this->minutes, 2, '0', STR_PAD_LEFT), str_pad($this->seconds, 2, '0', STR_PAD_LEFT));
    return join(':', $list);
  }

  public function api_friendly_values() {
    return $this->values ? $this->values : null;
  }
}

/**
 * Calculation field
 */
class PodioCalculationItemField extends PodioItemField {

  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values') {
      return true;
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as a string
   */
  public function __get($name) {
    $attribute = parent::__get($name);
    if ($name == 'values' && $attribute) {
      return $attribute[0]['value'];
    }
    return $attribute;
  }

  public function set_value($values) {
    return true;
  }

  public function humanized_value() {
    if ($this->values === null) {
      return '';
    }
    return rtrim(rtrim(number_format($this->values, 4, '.', ''), '0'), '.');
  }

  public function api_friendly_values() {
    return $this->values !== null ? $this->values : null;
  }
}

/**
 * Money field
 */
class PodioMoneyItemField extends PodioItemField {

  /**
   * Override __set to use field specific method for setting values property
   */
  public function __set($name, $value) {
    if ($name == 'values' && $value !== null) {
      return $this->set_value($value);
    }
    elseif ($name == 'amount') {
      if ($value === null) {
        return parent::__set('values', null);
      }
      $currency = !empty($this->values['currency']) ? $this->values['currency'] : '';
      return $this->set_value(array('currency' => $currency, 'value' => $value));
    }
    elseif ($name == 'currency') {
      if ($value === null) {
        return parent::__set('values', null);
      }
      $amount = !empty($this->values['value']) ? $this->values['value'] : '0';
      return $this->set_value(array('currency' => $value, 'value' => $amount));
    }
    return parent::__set($name, $value);
  }

  /**
   * Override __get to provide values as an integer
   */
  public function __get($name) {
    $attribute = parent::__get($name);
    if ($name == 'values' && $attribute) {
      return $attribute[0];
    }
    elseif ($name == 'amount') {
      return $this->values ? $this->values['value'] : null;
    }
    elseif ($name == 'currency') {
      return $this->values ? $this->values['currency'] : null;
    }
    return $attribute;
  }

  public function set_value($values) {
    parent::__set('values', $values ? array($values) : array());
  }

  public function humanized_value() {
    if (!$this->values) {
      return '';
    }

    $amount = number_format($this->values['value'], 2, '.', '');
    switch ($this->values['currency']) {
      case 'USD':
        $currency = '$';
        break;
      case 'EUR':
        $currency = '€';
        break;
      case 'GBP':
        $currency = '£';
        break;
      default:
        $currency = $this->values['currency'].' ';
        break;
    }
    return $currency.$amount;
  }

  public function api_friendly_values() {
    return $this->values ? $this->values : null;
  }

}
