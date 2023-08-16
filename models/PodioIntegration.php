<?php
/**
 * @see https://developers.podio.com/doc/integrations
 */
class PodioIntegration extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
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
    public static function create(PodioClient $podio_client, $app_id, $attributes = array())
    {
        $body = $podio_client->post("/integration/{$app_id}", $attributes)->json_body();
        return $body['integration_id'];
    }

    /**
     * @see https://developers.podio.com/doc/integrations/get-integration-86821
     */
    public static function get(PodioClient $podio_client, $app_id)
    {
        return self::member($podio_client->get("/integration/{$app_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/integrations/refresh-integration-86987
     */
    public static function refresh(PodioClient $podio_client, $app_id)
    {
        return $podio_client->post("/integration/{$app_id}/refresh");
    }

    /**
     * @see https://developers.podio.com/doc/integrations/update-integration-86843
     */
    public static function update(PodioClient $podio_client, $app_id, $attributes = array())
    {
        return $podio_client->put("/integration/{$app_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/integrations/delete-integration-86876
     */
    public static function delete(PodioClient $podio_client, $app_id)
    {
        return $podio_client->delete("/integration/{$app_id}");
    }
}
