<?php
class PodioObject {
  protected $attributes;
  protected $properties;
  protected $relationships;
  protected $podio;
  protected $id_column;

  public function init($attributes = array()) {
    // Create object instance from attributes
    foreach ($this->properties as $name => $property) {
      if (array_key_exists($name, $attributes)) {
        $this->set_attribute($name, $attributes[$name]);
        if (isset($property['options']['id'])) {
          $this->id_column = $name;
        }
      }
    }
    if ($this->relationships) {
      foreach ($this->relationships as $name => $type) {
        if (array_key_exists($name, $attributes)) {
          // TODO: instance should have a 'belongs_to' property pointing to $this
          $property = $this->properties[$name];
          $class_name = 'Podio'.$property['type'];

          if ($type == 'single') {
            $this->set_attribute($name, new $class_name($attributes[$name]));
          }
          elseif ($type == 'multiple' && is_array($attributes[$name])) {
            $values = array();
            foreach ($attributes[$name] as $value) {
              $values[] = new $class_name($attributes[$name]);
            }
            $this->set_attribute($name, $values);
          }
        }
      }
    }
  }
  public function __set($name, $value) {
    return $this->set_attribute($name, $value);
  }
  public function __get($name) {
    if ($name == 'id' && !empty($this->id_column)) {
      return $this->attributes[$this->id_column];
    }
    if (array_key_exists($name, $this->attributes)) {
      return $this->attributes[$name];
    }
  }
  public function __isset($name) {
    return isset($this->attributes[$name]);
  }
  public function __unset($name) {
    unset($this->attributes[$name]);
  }

  protected function set_attribute($name, $value) {
    if (array_key_exists($name, $this->properties)) {

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
          $this->attributes[$name] = null;
          if (is_a($value, 'DateTime')) {
            $this->attributes[$name] = $value;
          }
          elseif ($value) {
            // Handle task and item date fields where datetime is supplied
            // in the users local timezone
            $tz = date_default_timezone_get();
            date_default_timezone_set('UTC');
            $timestamp = strtotime($value);
            $this->attributes[$name] = new \DateTime("@{$timestamp}");
            date_default_timezone_set($tz);
          }
          break;
        // case 'date':

        //   break;
        // case 'time':

        //   break;
        // case 'array':

        //   break;
        default:
          $this->attributes[$name] = $value;
      }
      return true;
    }
    throw new Exception("Attribute cannot be assigned. Property doesn't exist.");
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

  // Define a property on this object
  public function property($name, $type, $options = array()) {
    if (!$this->properties) {
      $this->properties = array();
    }
    if (!array_key_exists($name, $this->properties)) {
      $this->properties[$name] = array('type' => $type, 'options' => $options);
    }
  }

  public function has_one($name, $class_name) {
    $this->property($name, $class_name);
    if (!$this->relationships) {
      $this->relationships = array();
    }
    if (!array_key_exists($name, $this->relationships)) {
      $this->relationships[$name] = 'single';
    }
  }

  public function has_many($name, $class_name) {
    $this->property($name, $class_name);
    if (!$this->relationships) {
      $this->relationships = array();
    }
    if (!array_key_exists($name, $this->relationships)) {
      $this->relationships[$name] = 'multiple';
    }
  }

  // TODO: collection, date/time properties, alias so e.g. hook_id can be accessed as just 'id'


}
