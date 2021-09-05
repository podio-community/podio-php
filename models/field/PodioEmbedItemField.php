<?php

/**
 * Embed field
 */
class PodioEmbedItemField extends PodioItemField
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
            $embeds = new PodioCollection();
            foreach ($attribute as $value) {
                $embed = new PodioEmbed($value['embed']);
                if (!empty($value['file'])) {
                    $embed->files = new PodioCollection(array(new PodioFile($value['file'])));
                }
                $embeds[] = $embed;
            }
            return $embeds;
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
            $values[] = $value->original_url;
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
            if (is_object($values) || (is_array($values) && !empty($values['embed']))) {
                $values = array($values);
            }

            $values = array_map(function ($value) {
                if (is_object($value)) {
                    $file = $value->files ? $value->files[0] : null;
                    unset($value->files);

                    return array('embed' => $value->as_json(false), 'file' => $file ? $file->as_json(false) : null);
                }
                return $value;
            }, $values);

            parent::__set('values', $values);
        }
    }

    public function api_friendly_values()
    {
        if (!$this->values) {
            return array();
        }
        $list = array();
        foreach ($this->values as $value) {
            $list[] = array('embed' => $value->embed_id, 'file' => ($value->files ? $value->files[0]->file_id : null));
        }
        return $list;
    }
}
