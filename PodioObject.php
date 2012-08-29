<?php

class PodioObject {
  private $attributes;
  private $properties;
  protected $podio;

  public function init($attributes = array()) {
    print "Parent constructor called\n";

    // Create object instance from attributes
    foreach ($this->properties as $name => $type) {
      if (array_key_exists($name, $attributes)) {
        $this->$name = $attributes[$name];
      }
    }
  }
  public function __set($name, $value) {
    // Make sure that $name is a property
    // TODO: Make sure type is correct
    if (array_key_exists($name, $this->properties)) {

      switch($this->properties[$name]) {
        case 'integer':
          $this->attributes[$name] = $value ? (int)$value : null;
          break;
        case 'boolean':
          if ($value === true || $value === false) {
            $this->attributes[$name] = $value;
          }
          elseif ($value) {
            $this->attributes[$name] = in_array(trim(strtolower($value)), array('true', 1, 'yes'));
          }
          $this->attributes[$name] = null;
          break;
        // case 'datetime':

        //   break;
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
  public function __get($name) {
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

  public static function podio() {
    return Podio::instance();
  }

  public static function listing($body) {
    $list = array();
    foreach ($body as $attributes) {
      $class = get_called_class();
      $list[] = new $class($attributes);
    }
    return $list;
  }

  // Define a property on this object
  public function property($name, $type) {
    $this->properties[$name] = $type;
  }
}
