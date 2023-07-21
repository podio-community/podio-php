<?php
/**
 * @see https://developers.podio.com/doc/recurrence
 */
class PodioRecurrence extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('recurrence_id', 'integer', array('id' => true));
        $this->property('name', 'string');
        $this->property('config', 'hash');
        $this->property('step', 'integer');
        $this->property('until', 'date');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/recurrence/get-recurrence-3415545
     */
    public static function get_for(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return self::member($podio_client, $podio_client->get("/recurrence/{$ref_type}/{$ref_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/recurrence/create-or-update-recurrence-3349957
     */
    public static function create(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array())
    {
        return $podio_client->put("/recurrence/{$ref_type}/{$ref_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/recurrence/create-or-update-recurrence-3349957
     */
    public static function update(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array())
    {
        return $podio_client->put("/recurrence/{$ref_type}/{$ref_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/recurrence/delete-recurrence-3349970
     */
    public static function delete(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return $podio_client->delete("/recurrence/{$ref_type}/{$ref_id}");
    }
}
