<?php
/**
 * @see https://developers.podio.com/doc/integrations
 */
class PodioIntegration extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('integration_id', 'integer', array('id' => true));
        $this->property('app_id', 'integer');
        $this->property('status', 'string');
        $this->property('type', 'string');
        $this->property('silent', 'boolean');
        $this->property('config', 'hash');
        $this->property('mapping', 'hash');
        $this->property('updating', 'boolean');
        $this->property('last_updated_on', 'datetime');
        $this->property('created_on', 'datetime');

        $this->has_one('created_by', 'ByLine');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/integrations/create-integration-86839
     */
    public static function create($app_id, $attributes = array(), PodioClient $podio_client)
    {
        $body = $podio_client->post("/integration/{$app_id}", $attributes)->json_body();
        return $body['integration_id'];
    }

    /**
     * @see https://developers.podio.com/doc/integrations/get-integration-86821
     */
    public static function get($app_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/integration/{$app_id}"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/integrations/refresh-integration-86987
     */
    public static function refresh($app_id, PodioClient $podio_client)
    {
        return $podio_client->post("/integration/{$app_id}/refresh");
    }

    /**
     * @see https://developers.podio.com/doc/integrations/update-integration-86843
     */
    public static function update($app_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->put("/integration/{$app_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/integrations/delete-integration-86876
     */
    public static function delete($app_id, PodioClient $podio_client)
    {
        return $podio_client->delete("/integration/{$app_id}");
    }
}
