<?php
/**
 * @see https://developers.podio.com/doc/items
 */
class PodioItemField extends PodioObject
{
    public function __construct($attributes = array(), $force_type = null)
    {
        parent::__construct();
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
    public static function save(PodioClient $podio_client, PodioItemField $field, $options = array())
    {
        $relationship = $field->relationship();
        if (!$relationship) {
            throw new PodioMissingRelationshipError('{"error_description":"Field is missing relationship to item", "request": {}}', null, null);
        }
        if (!$field->id && !$field->external_id) {
            throw new PodioDataIntegrityError('Field must have id or external_id set.');
        }
        $attributes = $field->as_json(false);
        return self::update($podio_client, $relationship['instance']->id, $field->id ?: $field->external_id, $attributes, $options);
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
    public static function update(PodioClient $podio_client, $item_id, $field_id, $attributes = array(), $options = array())
    {
        $url = $podio_client->url_with_options("/item/{$item_id}/value/{$field_id}", $options);
        return $podio_client->put($url, $attributes)->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-item-field-calendar-as-ical-10195681
     */
    public static function ical(PodioClient $podio_client, $item_id, $field_id)
    {
        return $podio_client->get("/calendar/item/{$item_id}/field/{$field_id}/ics/")->body;
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-item-field-calendar-as-ical-10195681
     */
    public static function ical_field(PodioClient $podio_client, $item_id, $field_id)
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
