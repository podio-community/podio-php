<?php

/**
 * Duration field
 */
class PodioDurationItemField extends PodioItemField
{

  /**
   * Override __set to use field specific method for setting values property
   */
    public function __set($name, $value)
    {
        if ($name == 'values' && $value !== null) {
            return $this->set_value($value);
        }
        return parent::__set($name, $value);
    }

    /**
     * Override __get to provide values as an integer
     */
    public function __get($name)
    {
        $attribute = parent::__get($name);
        if ($name == 'values' && $attribute) {
            return $attribute[0]['value'];
        } elseif ($name == 'hours') {
            return floor($this->values / 3600);
        } elseif ($name == 'minutes') {
            return (($this->values / 60) % 60);
        } elseif ($name == 'seconds') {
            return ($this->values % 60);
        }
        return $attribute;
    }

    public function set_value($values)
    {
        parent::__set('values', $values ? array(array('value' => (int)$values)) : array());
    }

    public function humanized_value()
    {
        $list = array(str_pad($this->hours, 2, '0', STR_PAD_LEFT), str_pad($this->minutes, 2, '0', STR_PAD_LEFT), str_pad($this->seconds, 2, '0', STR_PAD_LEFT));
        return join(':', $list);
    }

    public function api_friendly_values()
    {
        return $this->values ? $this->values : null;
    }
}
