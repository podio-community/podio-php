<?php

/**
 * Calculation field
 */
class PodioCalculationItemField extends PodioItemField
{
    /**
     * Override __set to use field specific method for setting values property
     */
    public function __set($name, $value)
    {
        if ($name == 'values') {
            return true;
        }
        return parent::__set($name, $value);
    }

    /**
     * Override __get to provide values as a string
     */
    public function __get($name)
    {
        $attribute = parent::__get($name);
        if ($name == 'values' && $attribute) {
            return (isset($attribute[0]['value'])) ? $attribute[0]['value'] : $attribute[0];
        }
        return $attribute;
    }

    public function set_value($values)
    {
        return true;
    }

    public function humanized_value()
    {
        if ($this->values === null) {
            return '';
        }
        return rtrim(rtrim(number_format($this->values, 4, '.', ''), '0'), '.');
    }

    public function api_friendly_values()
    {
        return $this->values !== null ? $this->values : null;
    }
}
