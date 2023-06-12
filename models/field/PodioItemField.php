<?php
/**
 * @see https://developers.podio.com/doc/items
 */
class PodioItemField extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array(), $force_type = null)
    {
        parent::__construct($podio_client);
        $this->property('field_id', 'integer', array('id' => true));
        $this->property('type', 'string');
        $this->property('external_id', 'string');
        $this->property('label', 'string');
        $this->property('values', 'array');
        $this->property('config', 'hash');
        $this->property('status', 'string');

        $this->init($attributes);

        $this->set_type_from_class_name();
    }

    /**
     * Saves the value of the field
     */
    public function save($options = array())
    {
        $relationship = $this->relationship();
        if (!$relationship) {
            throw new PodioMissingRelationshipError('{"error_description":"Field is missing relationship to item", "request": {}}', null, null);
        }
        if (!$this->id && !$this->external_id) {
            throw new PodioDataIntegrityError('Field must have id or external_id set.');
        }
        $attributes = $this->as_json(false);
        return self::update($relationship['instance']->id, $this->id ? $this->id : $this->external_id, $attributes, $options, $this->podio_client);
    }

    /**
     * Calling parent so we get all field attributes printed instead of only api_friendly_values
     */
    public function __toString()
    {
        return print_r(parent::as_json(false), true);
    }

    /**
     * Overwrites normal as_json to use api_friendly_values
     */
    public function as_json($encoded = true)
    {
        $result = $this->api_friendly_values();
        return $encoded ? json_encode($result) : $result;
    }

    /**
     * @see https://developers.podio.com/doc/items/update-item-field-values-22367
     */
    public static function update($item_id, $field_id, $attributes = array(), $options = array(), PodioClient $podio_client)
    {
        $url = $podio_client->url_with_options("/item/{$item_id}/value/{$field_id}", $options);
        return $podio_client->put($url, $attributes)->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-item-field-calendar-as-ical-10195681
     */
    public static function ical($item_id, $field_id, PodioClient $podio_client)
    {
        return $podio_client->get("/calendar/item/{$item_id}/field/{$field_id}/ics/")->body;
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-item-field-calendar-as-ical-10195681
     */
    public static function ical_field($item_id, $field_id, PodioClient $podio_client)
    {
        return $podio_client->get("/calendar/item/{$item_id}/field/{$field_id}/ics/")->body;
    }

    public function set_type_from_class_name()
    {
        switch (get_class($this)) {
      case 'PodioTextItemField':
        $this->type = 'text';
        break;
      case 'PodioEmbedItemField':
        $this->type = 'embed';
        break;
      case 'PodioLocationItemField':
        $this->type = 'location';
        break;
      case 'PodioDateItemField':
        $this->type = 'date';
        break;
      case 'PodioContactItemField':
        $this->type = 'contact';
        break;
      case 'PodioAppItemField':
        $this->type = 'app';
        break;
      case 'PodioCategoryItemField':
        $this->type = 'category';
        break;
      case 'PodioImageItemField':
        $this->type = 'image';
        break;
      case 'PodioFileItemField':
        $this->type = 'file';
        break;
      case 'PodioNumberItemField':
        $this->type = 'number';
        break;
      case 'PodioProgressItemField':
        $this->type = 'progress';
        break;
      case 'PodioDurationItemField':
        $this->type = 'duration';
        break;
      case 'PodioCalculationItemField':
        $this->type = 'calculation';
        break;
      case 'PodioMoneyItemField':
        $this->type = 'money';
        break;
      case 'PodioPhoneItemField':
        $this->type = 'phone';
        break;
      case 'PodioEmailItemField':
        $this->type = 'email';
        break;
      case 'PodioTagItemField':
        $this->type = 'tag';
        break;
      default:
        break;
    }
    }
}
