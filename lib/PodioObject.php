<?php
class PodioObject {
  public $__attributes = array();
  public $__belongs_to;
  public $__properties = array();
  public $__relationships = array();
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
            $child->__belongs_to = array('property' => $name, 'instance' => $this);
            $this->set_attribute($name, $child);
          }
          elseif ($type == 'has_many' && is_array($default_attributes[$name])) {
            $values = array();
            foreach ($default_attributes[$name] as $value) {
              $old_class_name = $class_name;

              // ItemField has special handling since we want to create objects of the sub-types.
              if ($class_name == 'PodioItemField' && !is_object($value)) {
                $class_name_alternate = 'Podio'.ucfirst($value['type']).'ItemField';
                if (class_exists($class_name_alternate)) {
                  $old_class_name = 'PodioItemField';
                  $class_name = $class_name_alternate;
                }
              }

              $child = is_object($value) ? $value : new $class_name($value);
              $child->__belongs_to = array('property' => $name, 'instance' => $this);
              $values[] = $child;

              $class_name = $old_class_name;
            }
            $this->set_attribute($name, $values);
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
    return print_r($this->__attributes, true);
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
        default:
          $this->__attributes[$name] = $value;
      }
      return true;
    }
    throw new Exception("Attribute cannot be assigned. Property '{$name}' doesn't exist.");
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

  public static function collection($response) {
    if ($response) {
      $body = $response->json_body();
      $list = array();
      foreach ($body['items'] as $attributes) {
        $class_name = get_called_class();
        $list[] = new $class_name($attributes);
      }
      $collection = array(
        'items' => $list,
        'filtered' => $body['filtered'],
        'total' => $body['total'],
      );
      return $collection;
    }
  }

  public function can($right) {
    if ($this->has_property('rights')) {
      return $this->has_attribute('rights') && in_array($right, $this->rights);
    }
    return null;
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

          // ItemField is a special case.
          if ($this->__properties[$name]['type'] == 'ItemField') {
            foreach ($this->__attributes[$name] as $item) {
              $key = $item->external_id ? $item->external_id : $item->id;
              $list[$key] = $item->as_json(false);
            }
            $result[$name] = $list;
          }
          else {
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
    }

    if ($result) {
      return $encoded ? json_encode($result) : $result;
    }
    return null;
  }

}
