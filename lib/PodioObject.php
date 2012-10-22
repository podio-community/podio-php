<?php
class PodioObject {
  public $attributes = array();
  public $belongs_to;
  public $properties = array();
  public $relationships = array();
  protected $podio;
  protected $id_column;

  public function init($default_attributes = array()) {
    if (!is_array($default_attributes)) {
      $default_attributes = array();
    }
    // Create object instance from attributes
    foreach ($this->properties as $name => $property) {
      if (isset($property['options']['id'])) {
        $this->id_column = $name;
      }
      if (array_key_exists($name, $default_attributes)) {
        $this->set_attribute($name, $default_attributes[$name]);
      }
    }
    if ($this->relationships) {
      foreach ($this->relationships as $name => $type) {
        if (array_key_exists($name, $default_attributes)) {
          $property = $this->properties[$name];
          $class_name = 'Podio'.$property['type'];

          if ($type == 'has_one') {
            $child = new $class_name($default_attributes[$name]);
            $child->belongs_to = array('property' => $name, 'instance' => $this);
            $this->set_attribute($name, $child);
          }
          elseif ($type == 'has_many' && is_array($default_attributes[$name])) {
            $values = array();
            foreach ($default_attributes[$name] as $value) {

              // ItemField has special handling since we want to create objects of the sub-types.
              if ($class_name == 'PodioItemField') {
                $class_name_alternate = 'Podio'.ucfirst($value['type']).'ItemField';
                if (class_exists($class_name_alternate)) {
                  $class_name = $class_name_alternate;
                }
              }

              $child = new $class_name($value);
              $child->belongs_to = array('property' => $name, 'instance' => $this);
              $values[] = $child;

              $class_name = 'PodioItemField';
            }
            $this->set_attribute($name, $values);
          }
        }
      }
    }
  }
  public function __set($name, $value) {
    if ($name == 'id' && !empty($this->id_column)) {
      return $this->set_attribute($this->id_column, $value);
    }
    return $this->set_attribute($name, $value);
  }
  public function __get($name) {
    if ($name == 'id' && !empty($this->id_column)) {
      return $this->attributes[$this->id_column];
    }
    if ($this->has_attribute($name)) {
      // Create DateTime object if necessary
      if ($this->has_property($name) && ($this->properties[$name]['type'] == 'datetime' || $this->properties[$name]['type'] == 'date')) {
        $tz = new DateTimeZone('UTC');
        return DateTime::createFromFormat($this->date_format_for_property($name), $this->attributes[$name], $tz);
      }

      return $this->attributes[$name];
    }
  }
  public function __isset($name) {
    return isset($this->attributes[$name]);
  }
  public function __unset($name) {
    unset($this->attributes[$name]);
  }
  public function __toString() {
    return print_r($this->attributes, true);
  }

  public function date_format_for_property($name) {
    if ($this->has_property($name)) {
      if ($this->properties[$name]['type'] == 'datetime') {
        return 'Y-m-d H:i:s';
      }
      elseif ($this->properties[$name]['type'] == 'date') {
        return 'Y-m-d';
      }
    }
  }

  protected function set_attribute($name, $value) {
    if ($this->has_property($name)) {

      $property = $this->properties[$name];
      switch($property['type']) {
        case 'integer':
          $this->attributes[$name] = $value ? (int)$value : null;
          break;
        case 'boolean':
          $this->attributes[$name] = null;
          if ($value === true || $value === false) {
            $this->attributes[$name] = $value;
          }
          elseif ($value) {
            $this->attributes[$name] = in_array(trim(strtolower($value)), array('true', 1, 'yes'));
          }
          break;
        case 'datetime':
        case 'date':
          if (is_a($value, 'DateTime')) {
            $this->attributes[$name] = $value->format($this->date_format_for_property($name));
          }
          else {
            $this->attributes[$name] = $value;
          }
          break;
        default:
          $this->attributes[$name] = $value;
      }
      return true;
    }
    throw new Exception("Attribute cannot be assigned. Property '{$name}' doesn't exist.");
  }

  public static function listing($response) {
    if ($response) {
      $body = $response->json_body();
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
    return array_key_exists($name, $this->attributes);
  }

  public function has_property($name) {
    return array_key_exists($name, $this->properties);
  }

  public function has_relationship($name) {
    return array_key_exists($name, $this->relationships);
  }

  // Define a property on this object
  public function property($name, $type, $options = array()) {
    if (!$this->has_property($name)) {
      $this->properties[$name] = array('type' => $type, 'options' => $options);
    }
  }

  public function has_one($name, $class_name, $options = array()) {
    $this->property($name, $class_name, $options);
    if (!$this->has_relationship($name)) {
      $this->relationships[$name] = 'has_one';
    }
  }

  public function has_many($name, $class_name, $options = array()) {
    $this->property($name, $class_name, $options);
    if (!$this->has_relationship($name)) {
      $this->relationships[$name] = 'has_many';
    }
  }

  public function as_json($encoded = true) {
    $result = array();
    foreach ($this->properties as $name => $property) {
      if (!$this->has_relationship($name) && $this->has_attribute($name) && !is_null($this->attributes[$name])) {
        $result[$name] = $this->attributes[$name];
      }
    }
    foreach ($this->relationships as $name => $type) {
      if ($type == 'has_one') {
        $target_name = $name;
        if (!empty($this->properties[$name]['options']['json_target'])) {
          $target_name = $this->properties[$name]['options']['json_target'];
        }

        if ($this->has_attribute($name)) {
          if (!empty($this->properties[$name]['options']['json_value'])) {
            $result[$target_name] = $this->attributes[$name]->{$this->properties[$name]['options']['json_value']};
          }
          elseif (is_object($this->attributes[$name]) && get_class($this->attributes[$name]) == 'PodioReference') {
            $result['ref_type'] = $this->attributes[$name]->type;
            $result['ref_id'] = $this->attributes[$name]->id;
          }
          else {
            $child = $this->attributes[$name]->as_json(false);
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
          if ($this->properties[$name]['type'] == 'ItemField') {
            foreach ($this->attributes[$name] as $item) {
              $key = $item->external_id ? $item->external_id : $item->id;
              $list[$key] = $item->as_json(false);
            }
            $result[$name] = $list;
          }
          else {
            foreach ($this->attributes[$name] as $item) {
              if (!empty($this->properties[$name]['options']['json_value'])) {
                $list[] = $item->{$this->properties[$name]['options']['json_value']};
              }
              else {
                $list[] = $item->as_json(false);
              }
            }
            if ($list) {
              if (!empty($this->properties[$name]['options']['json_target'])) {
                $result[$this->properties[$name]['options']['json_target']] = join(',', $list);
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
