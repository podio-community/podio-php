<?php

/**
 * Collection for managing a list of PodioItemField objects.
 */
class PodioItemFieldCollection extends PodioFieldCollection
{
    /**
     * Constructor. Pass in either decoded JSON from an API request
     * or an array of PodioItemField objects.
     */
    public function __construct(PodioClient $podio_client, $attributes = array(), $__api_values = false)
    {

    // Make default array into array of proper objects
        $fields = array();
        $class_name = 'PodioItemField';

        foreach ($attributes as $field_attributes) {
            $old_class_name = $class_name;

            if (!is_object($field_attributes)) {
                $class_name_alternate = 'Podio'.ucfirst($field_attributes['type']).'ItemField';
                if (class_exists($class_name_alternate)) {
                    $old_class_name = 'PodioItemField';
                    $class_name = $class_name_alternate;
                }
            }

            $field = is_object($field_attributes) ? $field_attributes : new $class_name($podio_client, array_merge($field_attributes, array('__api_values' => $__api_values)));
            $fields[] = $field;

            $class_name = $old_class_name;
        }

        // Add to internal storage
        parent::__construct($podio_client, $fields);
    }

    /**
     * Array access. Add field to collection.
     */
    public function offsetSet($offset, $field): void
    {
        if (!is_a($field, 'PodioItemField')) {
            throw new PodioDataIntegrityError("Objects in PodioItemFieldCollection must be of class PodioItemField");
        }

        parent::offsetSet($offset, $field);
    }
}
