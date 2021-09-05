<?php

/**
 * Money field
 */
class PodioMoneyItemField extends PodioItemField
{
  /**
   * Override __set to use field specific method for setting values property
   */
    public function __set($name, $value)
    {
        if ($name == 'values' && $value !== null) {
            return $this->set_value($value);
        } elseif ($name == 'amount') {
            if ($value === null) {
                return parent::__set('values', null);
            }
            $currency = !empty($this->values['currency']) ? $this->values['currency'] : '';
            return $this->set_value(array('currency' => $currency, 'value' => $value));
        } elseif ($name == 'currency') {
            if ($value === null) {
                return parent::__set('values', null);
            }
            $amount = !empty($this->values['value']) ? $this->values['value'] : '0';
            return $this->set_value(array('currency' => $value, 'value' => $amount));
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
            return $attribute[0];
        } elseif ($name == 'amount') {
            return $this->values ? $this->values['value'] : null;
        } elseif ($name == 'currency') {
            return $this->values ? $this->values['currency'] : null;
        }
        return $attribute;
    }

    public function set_value($values)
    {
        parent::__set('values', $values ? array($values) : array());
    }

    public function humanized_value()
    {
        if (!$this->values) {
            return '';
        }

        $amount = number_format($this->values['value'], 2, '.', '');
        switch ($this->values['currency']) {
      case 'USD':
        $currency = '$';
        break;
      case 'EUR':
        $currency = '€';
        break;
      case 'GBP':
        $currency = '£';
        break;
      default:
        $currency = $this->values['currency'] . ' ';
        break;
    }
        return $currency . $amount;
    }

    public function api_friendly_values()
    {
        return $this->values ? $this->values : null;
    }
}
