<?php
/**
 * @see https://developers.podio.com/doc/applications
 */
class PodioAppField extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('field_id', 'integer', array('id' => true));
        $this->property('type', 'string');
        $this->property('external_id', 'string');
        $this->property('config', 'hash');
        $this->property('status', 'string');
        $this->property('label', 'string');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/applications/add-new-app-field-22354
     */
    public static function create(PodioClient $podio_client, $app_id, $attributes = array())
    {
        $body = $podio_client->post("/app/{$app_id}/field/", $attributes)->json_body();
        return $body['field_id'];
    }

    /**
     * @see https://developers.podio.com/doc/applications/get-app-field-22353
     */
    public static function get(PodioClient $podio_client, $app_id, $field_id)
    {
        return self::member($podio_client->get("/app/{$app_id}/field/{$field_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/applications/update-an-app-field-22356
     */
    public static function update(PodioClient $podio_client, $app_id, $field_id, $attributes = array())
    {
        return $podio_client->put("/app/{$app_id}/field/{$field_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/applications/delete-app-field-22355
     */
    public static function delete(PodioClient $podio_client, $app_id, $field_id, $attributes = array())
    {
        $body = $podio_client->delete("/app/{$app_id}/field/{$field_id}", $attributes)->json_body();
        return $body['revision'];
    }
}
