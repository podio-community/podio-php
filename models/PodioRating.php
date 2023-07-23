<?php
/**
 * @see https://developers.podio.com/doc/ratings
 */
class PodioRating extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('rating_id', 'integer', array('id' => true));
        $this->property('type', 'string');
        $this->property('value', 'string');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/ratings/get-rating-22407
     */
    public static function get_for_type_and_user(PodioClient $podio_client, $ref_type, $ref_id, $rating_type, $user_id)
    {
        return self::member($podio_client, $podio_client->get("/rating/{$ref_type}/{$ref_id}/{$rating_type}/{$user_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/ratings/get-ratings-22375
     */
    public static function get_for_type(PodioClient $podio_client, $ref_type, $ref_id, $rating_type)
    {
        return $podio_client->get("/rating/{$ref_type}/{$ref_id}/{$rating_type}")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/ratings/get-all-ratings-22376
     */
    public static function get_for(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return $podio_client->get("/rating/{$ref_type}/{$ref_id}")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/ratings/get-rating-own-84128
     */
    public static function get_own_for_type(PodioClient $podio_client, $ref_type, $ref_id, $rating_type)
    {
        return self::member($podio_client, $podio_client->get("/rating/{$ref_type}/{$ref_id}/{$rating_type}/self"));
    }

    /**
     * @see https://developers.podio.com/doc/ratings/add-rating-22377
     */
    public static function create(PodioClient $podio_client, $ref_type, $ref_id, $rating_type, $attributes = array())
    {
        return $podio_client->post("/rating/{$ref_type}/{$ref_id}/{$rating_type}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/ratings/remove-rating-22342
     */
    public static function delete(PodioClient $podio_client, $ref_type, $ref_id, $rating_type)
    {
        return $podio_client->delete("/rating/{$ref_type}/{$ref_id}/{$rating_type}");
    }
}
