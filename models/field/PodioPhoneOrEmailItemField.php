<?php

/**
 * phone ore email field
 */
abstract class PodioPhoneOrEmailItemField extends PodioItemField
{
    public function humanized_value()
    {
        if (!$this->values) {
            return '';
        }

        $values = array();
        foreach ($this->values as $value) {
            $values[] = $value['type'] . ': ' . $value['value'];
        }
        return join(';', $values);
    }

    public function api_friendly_values()
    {
        return $this->values ? $this->values : array();
    }
}
