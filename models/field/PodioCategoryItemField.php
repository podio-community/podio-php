<?php

/**
 * Category field
 */
class PodioCategoryItemField extends PodioItemField
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
     * Override __get to provide values as a string
     */
    public function __get($name)
    {
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

    public function api_friendly_values()
    {
        if (!$this->values) {
            return array();
        }
        $list = array();
        foreach ($this->values as $value) {
            $list[] = $value['id'];
        }
        return $list;
    }

    public function set_value($values)
    {
        if (is_array($values)) {
            $formatted_values = array_map(function ($value) {
                if (is_array($value)) {
                    return array('value' => $value);
                } else {
                    return array('value' => array('id' => $value));
                }
            }, $values);
            parent::__set('values', $formatted_values);
        } else {
            parent::__set('values', array(array('value' => array('id' => $values))));
        }
    }

    public function add_value($value)
    {
        if (!$this->values) {
            $this->set_value($value);
        } else {
            $values = $this->values;
            $values[] = $value;
            $this->set_value($values);
        }
    }

    public function humanized_value()
    {
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
