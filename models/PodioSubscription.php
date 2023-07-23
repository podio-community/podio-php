<?php
/**
 * @see https://developers.podio.com/doc/subscriptions
 */
class PodioSubscription extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('started_on', 'datetime');
        $this->property('notifications', 'integer');

        $this->has_one('ref', 'Reference');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/subscriptions/get-subscription-by-id-22446
     */
    public static function get(PodioClient $podio_client, $subscription_id)
    {
        return self::member($podio_client, $podio_client->get("/subscription/{$subscription_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/subscriptions/get-subscription-by-reference-22408
     */
    public static function get_for(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return self::member($podio_client, $podio_client->get("/subscription/{$ref_type}/{$ref_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/subscriptions/subscribe-22409
     */
    public static function create(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return $podio_client->post("/subscription/{$ref_type}/{$ref_id}")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/subscriptions/unsubscribe-by-id-22445
     */
    public static function delete(PodioClient $podio_client, $subscription_id)
    {
        return $podio_client->delete("/subscription/{$subscription_id}");
    }

    /**
     * @see https://developers.podio.com/doc/subscriptions/unsubscribe-by-reference-22410
     */
    public static function delete_for(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return $podio_client->delete("/subscription/{$ref_type}/{$ref_id}");
    }
}
