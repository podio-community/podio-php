<?php

/**
 * Date field
 */
class PodioDateItemField extends PodioItemField
{
  /**
   * Override __set to use field specific method for setting values property
   */
    public function __set($name, $value)
    {
        if ($name == 'values' && $value !== null) {
            return $this->set_value($value);
        } elseif ($name == 'start_date') {
            return $this->set_value(array(
        'start_date_utc' => $value,
        'start_time_utc' => $this->start_time,
        'end_date_utc' => $this->end_date,
        'end_time_utc' => $this->end_time
      ));
        } elseif ($name == 'start_time') {
            return $this->set_value(array(
        'start_date_utc' => $this->start_date,
        'start_time_utc' => $value,
        'end_date_utc' => $this->end_date,
        'end_time_utc' => $this->end_time
      ));
        } elseif ($name == 'end_date') {
            return $this->set_value(array(
        'start_date_utc' => $this->start_date,
        'start_time_utc' => $this->start_time,
        'end_date_utc' => $value,
        'end_time_utc' => $this->end_time
      ));
        } elseif ($name == 'end_time') {
            return $this->set_value(array(
        'start_date_utc' => $this->start_date,
        'start_time_utc' => $this->start_time,
        'end_date_utc' => $this->end_date,
        'end_time_utc' => $value
      ));
        } elseif ($name == 'start') {
            if ($value === null) {
                return parent::__set('values', null);
            }

            return $this->set_value(array(
        'start_date_utc' => is_string($value) ? $this->datetime_from_string($value) : $value,
        'start_time_utc' => is_string($value) ? $this->datetime_from_string($value) : $value,
        'end_date_utc' => $this->end_date,
        'end_time_utc' => $this->end_time
      ));
        } elseif ($name == 'end') {
            if ($value && is_string($value)) {
                $end = $this->datetime_from_string($value);
            } else {
                $end = $value;
            }

            return $this->set_value(array(
        'start_date_utc' => $this->start_date,
        'start_time_utc' => $this->start_time,
        'end_date_utc' => $end,
        'end_time_utc' => $end,
      ));
        }
        return parent::__set($name, $value);
    }

    /**
     * Override __get to provide values as a string
     */
    public function __get($name)
    {
        // We only work on UTC values
        $tz = new DateTimeZone('UTC');
        $values = parent::__get('values');

        if ($name == 'values' && is_array($values) && !empty($values)) {
            $start = DateTime::createFromFormat('Y-m-d H:i:s', $values[0]['start_date_utc'] . ' ' . (!empty($values[0]['start_time_utc']) ? $values[0]['start_time_utc'] : '00:00:00'), $tz);
            if (!isset($values[0]['end_date_utc']) || ($values[0]['start_date_utc'] == $values[0]['end_date_utc'] && empty($values[0]['end_time_utc']))) {
                $end = null;
            } else {
                $end = DateTime::createFromFormat('Y-m-d H:i:s', $values[0]['end_date_utc'] . ' ' . (!empty($values[0]['end_time_utc']) ? $values[0]['end_time_utc'] : '00:00:00'), $tz);
            }

            return array('start' => $start, 'end' => $end);
        } elseif ($name == 'start_time') {
            return is_array($values) && $values[0]['start_date_utc'] && $values[0]['start_time_utc'] ? $this->values['start'] : null;
        } elseif ($name == 'end_time') {
            return is_array($values) && $values[0]['end_date_utc'] && $values[0]['end_time_utc'] ? $this->values['end'] : null;
        } elseif ($name == 'start' || $name == 'start_date') {
            return $this->values ? $this->values['start'] : null;
        } elseif ($name == 'end' || $name == 'end_date') {
            return $this->values ? $this->values['end'] : null;
        }
        return parent::__get($name);
    }

    /**
     * True if start and end are on the same day.
     */
    public function same_day()
    {
        if (!$this->values || ($this->start && !$this->end)) {
            return true;
        }

        if ($this->start->format('Y-m-d') == $this->end->format('Y-m-d')) {
            return true;
        }
        return false;
    }

    /**
     * True if this is an allday event (has no time component on both start and end)
     */
    public function all_day()
    {
        if (!$this->values) {
            return false;
        }
        if (($this->start->format('H:i:s') == '00:00:00' && (!$this->end || ($this->end && $this->end->format('H:i:s') == '00:00:00')))) {
            return true;
        }
        return false;
    }

    public function set_value($values)
    {
        if (!$values) {
            return parent::__set('values', null);
        }

        $formatted_values = array(
      'start_date_utc' => null,
      'start_time_utc' => null,
      'end_date_utc' => null,
      'end_time_utc' => null
    );

        // Ensure DateTime objects for start values
        if (isset($values['start'])) {
            $values['start_date_utc'] = $values['start'];
            $values['start_time_utc'] = $values['start'];
            if (is_string($values['start'])) {
                $components = explode(' ', $values['start']);
                $values['start_time_utc'] = count($components) === 1 ? null : $this->datetime_from_timestring($components[1]);
            }
        }

        if (isset($values['end'])) {
            $values['end_date_utc'] = $values['end'];
            $values['end_time_utc'] = $values['end'];
            if (is_string($values['end'])) {
                $components = explode(' ', $values['end']);
                $values['end_time_utc'] = count($components) === 1 ? null : $this->datetime_from_timestring($components[1]);
            }
        }

        if (!empty($values['start_date_utc']) && is_string($values['start_date_utc'])) {
            $values['start_date_utc'] = $this->datetime_from_string($values['start_date_utc']);
        }

        if (!empty($values['start_time_utc']) && is_string($values['start_time_utc'])) {
            $values['start_time_utc'] = $this->datetime_from_timestring($values['start_time_utc']);
        }

        // Ensure we're saving UTC values
        if ($values['start_date_utc']) {
            $values['start_date_utc']->setTimeZone(new DateTimeZone('UTC'));
        }
        if ($values['start_time_utc']) {
            $values['start_time_utc']->setTimeZone(new DateTimeZone('UTC'));
        }

        // Set values
        $formatted_values['start_date_utc'] = $values['start_date_utc'] ? $values['start_date_utc']->format('Y-m-d') : null;
        $formatted_values['start_time_utc'] = $values['start_time_utc'] ? $values['start_time_utc']->format('H:i:s') : null;

        // Ensure DateTime objects for end values
        if (!empty($values['end_date_utc']) && is_string($values['end_date_utc'])) {
            $values['end_date_utc'] = $this->datetime_from_string($values['end_date_utc']);
        }

        if (!empty($values['end_time_utc']) && is_string($values['end_time_utc'])) {
            $values['end_time_utc'] = $this->datetime_from_timestring($values['end_time_utc']);
        }

        // Ensure we're saving UTC values
        if (!empty($values['end_date_utc'])) {
            $values['end_date_utc']->setTimeZone(new DateTimeZone('UTC'));
        }
        if (!empty($values['end_time_utc'])) {
            $values['end_time_utc']->setTimeZone(new DateTimeZone('UTC'));
        }

        // Set values
        if (empty($values['end_date_utc'])) {
            $formatted_values['end_date_utc'] = $values['start_date_utc'] ? $values['start_date_utc']->format('Y-m-d') : null;
        } else {
            $formatted_values['end_date_utc'] = $values['end_date_utc'] ? $values['end_date_utc']->format('Y-m-d') : null;
        }
        if (isset($values['end_time_utc'])) {
            $formatted_values['end_time_utc'] = $values['end_time_utc'] ? $values['end_time_utc']->format('H:i:s') : null;
        }

        parent::__set('values', array($formatted_values));
    }

    public function datetime_from_string($string)
    {
        $tz = new DateTimeZone('UTC');

        $split = explode(' ', $string);
        if (count($split) == 1) {
            $split[] = '00:00:00';
        }
        return DateTime::createFromFormat('Y-m-d H:i:s', $split[0] . ' ' . $split[1], $tz);
    }

    public function datetime_from_timestring($string)
    {
        $tz = new DateTimeZone('UTC');
        return DateTime::createFromFormat('H:i:s', $string, $tz);
    }

    public function humanized_value()
    {
        $start = $this->start;
        $end = $this->end;

        if (!$start) {
            return '';
        }

        // Variants:

        // Same date
        // 2012-12-12
        // 2012-12-12 14:00
        // 2012-12-12 14:00 - 15:00

        // Different dates
        // 2012-12-12 - 2012-12-14
        // 2012-12-12 14:00 - 2012-12-14
        // 2012-12-12 14:00 - 2012-12-12 15:00

        if ($this->same_day()) {
            if (!$end) {
                return $start->format('H:i') == '00:00' ? $start->format('Y-m-d') : $start->format('Y-m-d H:i');
            } else {
                return $start->format('Y-m-d H:i') . ' - ' . $end->format('H:i');
            }
        } else {
            if ($end->format('H:i') != '00:00') {
                return $start->format('Y-m-d H:i') . ' - ' . $end->format('Y-m-d H:i');
            } elseif ($start->format('H:i') != '00:00' && $end->format('H:i') == '00:00') {
                return $start->format('Y-m-d H:i') . ' - ' . $end->format('Y-m-d');
            } else {
                return $start->format('Y-m-d') . ' - ' . $end->format('Y-m-d');
            }
        }
    }

    public function api_friendly_values()
    {
        if (!$this->start) {
            return array();
        }

        $result = array();
        if ($this->start_date && $this->start_time) {
            $result['start_utc'] = $this->start_date->format('Y-m-d') . ' ' . $this->start_time->format('H:i:s');
        } else {
            $result['start_date'] = $this->start_date ? $this->start_date->format('Y-m-d') : null;
        }

        if ($this->end_date && $this->end_time) {
            $result['end_utc'] = $this->end_date->format('Y-m-d') . ' ' . $this->end_time->format('H:i:s');
        } else {
            $result['end_date'] = $this->end_date ? $this->end_date->format('Y-m-d') : null;
        }

        return $result;
    }
}
