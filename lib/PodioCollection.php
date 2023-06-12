<?php

/**
 * Provides a very simple iterator and array access interface to a collection
 * of PodioObject models.
 */
class PodioCollection implements IteratorAggregate, ArrayAccess, Countable
{
    private $__items = array();
    private $__idToItems = array();
    private $__extToItems = array();
    private $__belongs_to;

    /**
     * Constructor. Pass in an array of PodioObject objects.
     * @param PodioClient $podio_client not used, but required for compatibility with other Podio collections.
     */
    public function __construct(PodioClient $podio_client, $items = array())
    {
        foreach ($items as $item) {
            $this->offsetSet(null, $item);
        }
    }

    /**
     * Convert collection to string
     */
    public function __toString()
    {
        $items = array();
        foreach ($this->__items as $item) {
            $items[] = $item->as_json(false);
        }
        return print_r($items, true);
    }

    /**
     * Implements Countable
     */
    public function count(): int
    {
        return count($this->__items);
    }

    /**
     * Implements IteratorAggregate
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->__items);
    }

    /**
     * Array access. Set item by offset, automatically adding relationship.
     */
    public function offsetSet($offset, $value): void
    {
        if (!is_a($value, 'PodioObject')) {
            throw new PodioDataIntegrityError("Objects in PodioCollection must be of class PodioObject");
        }

        // If the collection has a relationship with a parent, add it to the item as well.
        $relationship = $this->relationship();
        if ($relationship) {
            $value->add_relationship($relationship['instance'], $relationship['property']);
        }

        if (is_null($offset)) {
            $this->__items[] = $value;
        } else {
            if (isset($this->__items[$offset])) {
                $oldItem = $this->__items[$offset];
                $this->cleanUpItem($oldItem);
            }
            $this->__items[$offset] = $value;
        }
        if ($value->id) {
            $this->__idToItems[strval($value->id)] = $value;
        }
        if ($value->external_id) {
            $this->__extToItems[$value->external_id] = $value;
        }
    }

    /**
     * Array access. Check for existence.
     */
    public function offsetExists($offset): bool
    {
        return isset($this->__items[$offset]);
    }

    /**
     * Array access. Unset.
     */
    public function offsetUnset($offset): void
    {
        if (isset($this->__items[$offset])) {
            $item = $this->__items[$offset];
            $this->cleanUpItem($item);
        }
        unset($this->__items[$offset]);
    }

    /**
     * Array access. Get.
     */
    #[ReturnTypeWillChange] // We cannot use :mixed due to incompatibility with php 7.3, 7.4
    public function offsetGet($offset)
    {
        return isset($this->__items[$offset]) ? $this->__items[$offset] : null;
    }

    /**
     * Return the raw array of objects. Internal use only.
     */
    public function _get_items()
    {
        return $this->__items;
    }

    /**
     * Set the raw array of objects. Internal use only.
     */
    public function _set_items($items)
    {
        $this->__items = $items;
    }

    /**
     * Return any relationship to a parent object.
     */
    public function relationship()
    {
        return $this->__belongs_to;
    }

    /**
     * Add a new relationship to a parent object. Will also add relationship
     * to all individual objects in the collection.
     */
    public function add_relationship($instance, $property = 'fields')
    {
        $this->__belongs_to = array('property' => $property, 'instance' => $instance);

        // Add relationship to all individual fields as well.
        foreach ($this as $item) {
            if ($item->has_property($property)) {
                $item->add_relationship($instance, $property);
            }
        }
    }

    /**
     * Get object in the collection by id or external_id.
     */
    public function get($id_or_external_id)
    {
        if (is_int($id_or_external_id)) {
            return isset($this->__idToItems[strval($id_or_external_id)]) ? $this->__idToItems[strval($id_or_external_id)] : null;
        } else {
            return isset($this->__extToItems[$id_or_external_id]) ? $this->__extToItems[$id_or_external_id] : null;
        }
    }

    /**
     * Remove object from collection by id or external_id.
     */
    public function remove($id_or_external_id)
    {
        if (count($this) === 0) {
            return true;
        }
        $removedObject = null;
        if (is_int($id_or_external_id)) {
            if (isset($this->__idToItems[strval($id_or_external_id)])) {
                $removedObject = $this->__idToItems[strval($id_or_external_id)];
            }
        } else {
            if (isset($this->__extToItems[$id_or_external_id])) {
                $removedObject = $this->__extToItems[$id_or_external_id];
            }
        }

        // this operation is expensive, hence only do it if necessary:
        if ($removedObject) {
            $this->_set_items(array_filter($this->_get_items(), function ($item) use ($id_or_external_id) {
                return !($item->id == $id_or_external_id || $item->external_id == $id_or_external_id);
            }));
            $this->cleanUpItem($removedObject);
        }
    }

    private function cleanUpItem($removedObject)
    {
        if ($removedObject) {
            if ($removedObject->external_id && isset($this->__extToItems[$removedObject->external_id])) {
                unset($this->__extToItems[$removedObject->external_id]);
            }
            if ($removedObject->id && isset($this->__idToItems[strval($removedObject->id)])) {
                unset($this->__idToItems[strval($removedObject->id)]);
            }
        }
    }
}
