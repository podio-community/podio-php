<?php
class PodioObject {
  private $__attributes = array();
  private $__belongs_to;
  private $__properties = array();
  private $__relationships = array();
  protected $__id_column;

  public function init($default_attributes = array()) {
    if (is_int($default_attributes)) {
      $default_attributes = array('id' => $default_attributes);
    }
    if (is_string($default_attributes)) {
      $default_attributes = array('external_id' => $default_attributes);
    }
    if (!is_array($default_attributes)) {
      $default_attributes = array();
    }
    // Create object instance from attributes
    foreach ($this->__properties as $name => $property) {
      if (isset($property['options']['id'])) {
        $this->__id_column = $name;
        if (array_key_exists('id', $default_attributes)) {
          $this->id = $default_attributes['id'];
        }
      }
      if (array_key_exists($name, $default_attributes)) {
        $this->$name = $default_attributes[$name];
      }
    }
    if ($this->__relationships) {
      foreach ($this->__relationships as $name => $type) {
        if (array_key_exists($name, $default_attributes)) {
          $property = $this->__properties[$name];
          $class_name = 'Podio'.$property['type'];

          if ($type == 'has_one') {
            $child = is_object($default_attributes[$name]) ? $default_attributes[$name] : new $class_name($default_attributes[$name]);
            $child->add_relationship($this, $name);
            $this->set_attribute($name, $child);
          }
          elseif ($type == 'has_many' && is_array($default_attributes[$name])) {

            // Special handling for ItemField and AppField.
            // We need to create collection of the right type
            if ($class_name == 'PodioItemField') {
              $collection_class = 'PodioItemFieldCollection';
              $values = $default_attributes[$name];
            }
            elseif ($class_name == 'PodioAppField') {
              $collection_class = 'PodioAppFieldCollection';
              $values = $default_attributes[$name];
            }
            else {
              $collection_class = 'PodioCollection';
              $values = array();
              foreach ($default_attributes[$name] as $value) {
                $child = is_object($value) ? $value : new $class_name($value);
                $values[] = $child;
              }
            }
            $collection = new $collection_class($values);
            $collection->add_relationship($this, $name);
            $this->set_attribute($name, $collection);
          }
        }
      }
    }
  }
  public function __set($name, $value) {
    if ($name == 'id' && !empty($this->__id_column)) {
      return $this->set_attribute($this->__id_column, $value);
    }
    return $this->set_attribute($name, $value);
  }
  public function __get($name) {
    if ($name == 'id' && !empty($this->__id_column)) {
      return empty($this->__attributes[$this->__id_column]) ? null : $this->__attributes[$this->__id_column];
    }
    if ($this->has_attribute($name)) {
      // Create DateTime object if necessary
      if ($this->has_property($name) && ($this->__properties[$name]['type'] == 'datetime' || $this->__properties[$name]['type'] == 'date')) {
        $tz = new DateTimeZone('UTC');
        return DateTime::createFromFormat($this->date_format_for_property($name), $this->__attributes[$name], $tz);
      }

      return $this->__attributes[$name];
    }
  }
  public function __isset($name) {
    return isset($this->__attributes[$name]);
  }
  public function __unset($name) {
    unset($this->__attributes[$name]);
  }
  public function __toString() {
    return print_r($this->as_json(false), true);
  }

  public function date_format_for_property($name) {
    if ($this->has_property($name)) {
      if ($this->__properties[$name]['type'] == 'datetime') {
        return 'Y-m-d H:i:s';
      }
      elseif ($this->__properties[$name]['type'] == 'date') {
        return 'Y-m-d';
      }
    }
  }

  public function relationship() {
    return $this->__belongs_to;
  }

  public function add_relationship($instance, $property = 'fields') {
    if (!$this->has_property($property)) {
      throw new PodioDataIntegrityError('Cannot add relationship. Property does not exist.');
    }
    $this->__belongs_to = array('property' => $property, 'instance' => $instance);
  }

  protected function set_attribute($name, $value) {
    if ($this->has_property($name)) {

      $property = $this->__properties[$name];
      switch($property['type']) {
        case 'integer':
          $this->__attributes[$name] = $value ? (int)$value : null;
          break;
        case 'boolean':
          $this->__attributes[$name] = null;
          if ($value === true || $value === false) {
            $this->__attributes[$name] = $value;
          }
          elseif ($value) {
            $this->__attributes[$name] = in_array(trim(strtolower($value)), array('true', 1, 'yes'));
          }
          break;
        case 'datetime':
        case 'date':
          if (is_a($value, 'DateTime')) {
            $this->__attributes[$name] = $value->format($this->date_format_for_property($name));
          }
          else {
            $this->__attributes[$name] = $value;
          }
          break;
        case 'string':
          $this->__attributes[$name] = $value ? (string)$value : null;
          break;
        case 'array':
        case 'hash':
          $this->__attributes[$name] = $value ? (array)$value : array();
          break;
        default:
          $this->__attributes[$name] = $value;
      }
      return true;
    }
    throw new PodioDataIntegrityError("Attribute cannot be assigned. Property '{$name}' doesn't exist.");
  }

  public static function listing($response_or_attributes) {
    if ($response_or_attributes) {
      if (is_object($response_or_attributes) && get_class($response_or_attributes) == 'PodioResponse') {
        $body = $response_or_attributes->json_body();
      }
      else {
        $body = $response_or_attributes;
      }
      $list = array();
      foreach ($body as $attributes) {
        $class_name = get_called_class();
        $list[] = new $class_name($attributes);
      }
      return $list;
    }
  }

  public static function member($response) {
    if ($response) {
      $class_name = get_called_class();
      return new $class_name($response->json_body());
    }
  }

  public static function collection($response, $collection_type = "PodioCollection") {
    if ($response) {
      $body = $response->json_body();
      $list = array();
      foreach ($body['items'] as $attributes) {
        $class_name = get_called_class();
        $list[] = new $class_name($attributes);
      }
      return new $collection_type($list, $body['filtered'], $body['total']);
    }
  }

  public function can($right) {
    if ($this->has_property('rights')) {
      return $this->has_attribute('rights') && in_array($right, $this->rights);
    }
    return false;
  }

  public function has_attribute($name) {
    return array_key_exists($name, $this->__attributes);
  }

  public function has_property($name) {
    return array_key_exists($name, $this->__properties);
  }

  public function has_relationship($name) {
    return array_key_exists($name, $this->__relationships);
  }

  // Define a property on this object
  public function property($name, $type, $options = array()) {
    if (!$this->has_property($name)) {
      $this->__properties[$name] = array('type' => $type, 'options' => $options);
    }
  }

  public function has_one($name, $class_name, $options = array()) {
    $this->property($name, $class_name, $options);
    if (!$this->has_relationship($name)) {
      $this->__relationships[$name] = 'has_one';
    }
  }

  public function has_many($name, $class_name, $options = array()) {
    $this->property($name, $class_name, $options);
    if (!$this->has_relationship($name)) {
      $this->__relationships[$name] = 'has_many';
    }
  }

  public function as_json($encoded = true) {
    $result = array();
    foreach ($this->__properties as $name => $property) {
      if (!$this->has_relationship($name) && $this->has_attribute($name) && !is_null($this->__attributes[$name])) {
        $result[$name] = $this->__attributes[$name];
      }
    }
    foreach ($this->__relationships as $name => $type) {
      if ($type == 'has_one') {
        $target_name = $name;
        if (!empty($this->__properties[$name]['options']['json_target'])) {
          $target_name = $this->__properties[$name]['options']['json_target'];
        }

        if ($this->has_attribute($name)) {
          if (!empty($this->__properties[$name]['options']['json_value'])) {
            $result[$target_name] = $this->__attributes[$name]->{$this->__properties[$name]['options']['json_value']};
          }
          elseif (is_a($this->__attributes[$name], 'PodioFieldCollection')) {
            foreach ($this->__attributes[$name] as $field) {
              // Only use external_id for item fields
              $key = $field->external_id && is_a($this->__attributes[$name], 'PodioItemFieldCollection') ? $field->external_id : $field->id;
              $list[$key] = $field->as_json(false);
            }
            $result[$name] = $list;
          }
          elseif (is_object($this->__attributes[$name]) && get_class($this->__attributes[$name]) == 'PodioReference') {
            $result['ref_type'] = $this->__attributes[$name]->type;
            $result['ref_id'] = $this->__attributes[$name]->id;
          }
          else {
            $child = $this->__attributes[$name]->as_json(false);
            if ($child) {
              $result[$target_name] = $child;
            }
          }
        }
      }
      elseif ($type == 'has_many') {
        if ($this->has_attribute($name)) {
          $list = array();
          foreach ($this->__attributes[$name] as $item) {
            if (!empty($this->__properties[$name]['options']['json_value'])) {
              $list[] = $item->{$this->__properties[$name]['options']['json_value']};
            }
            else {
              $list[] = $item->as_json(false);
            }
          }
          if ($list) {
            if (!empty($this->__properties[$name]['options']['json_target'])) {
              $result[$this->__properties[$name]['options']['json_target']] = $list;
            }
            else {
              $result[$name] = $list;
            }
          }
        }
      }
    }

    if ($result) {
      return $encoded ? json_encode($result) : $result;
    }
    return null;
  }

}
