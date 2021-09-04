<?php

/**
 * Location field
 */
class PodioLocationItemField extends PodioItemField
{

  /**
   * Override __set to use field specific method for setting values property
   */
    public function __set($name, $value)
    {
        if ($name == 'values' && $value !== null) {
            return $this->set_value($value);
        } elseif ($name == 'text') {
            if ($value === null) {
                return parent::__set('values', null);
            }
            $current_values = $this->values ? $this->values : array();
            $current_values['value'] = $value;
            return $this->set_value($current_values);
        }
        return parent::__set($name, $value);
    }

    /**
     * Override __get to provide values as a string
     */
    public function __get($name)
    {
        $attribute = parent::__get($name);
        if ($name == 'values' && is_array($attribute) && !empty($attribute)) {
            return $attribute[0];
        } elseif ($name == 'text') {
            return $this->values ? $this->values['value'] : null;
        }
        return $attribute;
    }

    public function api_friendly_values()
    {
        return $this->values ? $this->values : null;
    }

    public function set_value($values)
    {
        parent::__set('values', $values ? array($values) : array());
    }

    public function humanized_value()
    {
        if (!$this->text) {
            return '';
        }
        return $this->text;
    }
}
