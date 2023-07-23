<?php

/**
 * Asset field, super class for Image/File fields
 */
class PodioAssetItemField extends PodioItemField
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
     * Override __get to provide values as a PodioCollection of PodioEmbed objects
     */
    public function __get($name)
    {
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

    public function humanized_value()
    {
        if (!$this->values) {
            return '';
        }

        $values = array();
        foreach ($this->values as $value) {
            $values[] = $value->name;
        }
        return join(';', $values);
    }

    public function set_value($values)
    {
        if ($values) {
            // Ensure that we have an array of values
            if (is_a($values, 'PodioCollection')) {
                $values = $values->_get_items();
            }
            if (is_object($values) || (is_array($values) && !empty($values['file_id']))) {
                $values = array($values);
            }

            $values = array_map(function ($value) {
                if (is_object($value)) {
                    return array('value' => $value->as_json(false));
                }
                return array('value' => $value);
            }, $values);

            parent::__set('values', $values);
        } else {
            parent::__set('values', array());
        }
    }

    public function api_friendly_values()
    {
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
