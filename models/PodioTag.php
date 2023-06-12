<?php
/**
 * @see https://developers.podio.com/doc/tags
 */
class PodioTag extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('count', 'integer');
        $this->property('text', 'string');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/tags/create-tags-22464
     */
    public static function create($ref_type, $ref_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->post("/tag/{$ref_type}/{$ref_id}/", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/tags/update-tags-39859
     */
    public static function update($ref_type, $ref_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->put("/tag/{$ref_type}/{$ref_id}/", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/tags/remove-tag-22465
     */
    public static function delete($ref_type, $ref_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->delete("/tag/{$ref_type}/{$ref_id}/", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/tags/get-tags-on-app-22467
     */
    public static function get_for_app($app_id, $attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/tag/app/{$app_id}/", $attributes), $podio_client);
    }
}
