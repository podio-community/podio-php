<?php
/**
 * @see https://developers.podio.com/doc/reference
 */
class PodioReference extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('type', 'string');
        $this->property('id', 'integer');
        $this->property('title', 'string');
        $this->property('link', 'string');
        $this->property('data', 'hash');
        $this->property('created_on', 'datetime');

        $this->has_one('created_by', 'ByLine');
        $this->has_one('created_via', 'Via');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/reference/get-reference-10661022
     */
    public static function get_for(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array())
    {
        return self::member($podio_client, $podio_client->get("/reference/{$ref_type}/{$ref_id}", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/reference/search-references-13312595
     */
    public static function search(PodioClient $podio_client, $attributes = array())
    {
        return $podio_client->post("/reference/search", $attributes)->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/reference/resolve-url-66839423
     */
    public static function resolve(PodioClient $podio_client, $attributes = array())
    {
        return self::member($podio_client, $podio_client->get("/reference/resolve", $attributes));
    }
}
