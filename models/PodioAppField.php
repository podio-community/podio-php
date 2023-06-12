<?php
/**
 * @see https://developers.podio.com/doc/applications
 */
class PodioAppField extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
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
    public static function create($app_id, $attributes = array(), PodioClient $podio_client)
    {
        $body = $podio_client->post("/app/{$app_id}/field/", $attributes)->json_body();
        return $body['field_id'];
    }

    /**
     * @see https://developers.podio.com/doc/applications/get-app-field-22353
     */
    public static function get($app_id, $field_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/app/{$app_id}/field/{$field_id}"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/applications/update-an-app-field-22356
     */
    public static function update($app_id, $field_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->put("/app/{$app_id}/field/{$field_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/applications/delete-app-field-22355
     */
    public static function delete($app_id, $field_id, $attributes = array(), PodioClient $podio_client)
    {
        $body = $podio_client->delete("/app/{$app_id}/field/{$field_id}", $attributes)->json_body();
        return $body['revision'];
    }
}
