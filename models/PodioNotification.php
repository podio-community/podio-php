<?php
/**
 * @see https://developers.podio.com/doc/notifications
 */
class PodioNotification extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('notification_id', 'integer', array('id' => true));
        $this->property('type', 'string');
        $this->property('data', 'hash');
        $this->property('icon', 'string');
        $this->property('text', 'string');
        $this->property('viewed_on', 'datetime');
        $this->property('subscription_id', 'integer');
        $this->property('created_on', 'datetime');
        $this->property('starred', 'boolean');

        $this->has_one('created_by', 'ByLine');
        $this->has_one('created_via', 'Via');
        $this->has_one('user', 'User');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/notifications/get-inbox-new-count-84610
     */
    public static function get_new_count(PodioClient $podio_client)
    {
        $body = $podio_client->get("/notification/inbox/new/count")->json_body();
        return $body['new'];
    }

    /**
     * @see https://developers.podio.com/doc/notifications/mark-all-notifications-as-viewed-58099
     */
    public static function mark_all_as_viewed(PodioClient $podio_client)
    {
        return $podio_client->post("/notification/viewed");
    }

    /**
     * @see https://developers.podio.com/doc/notifications/mark-notification-as-viewed-22436
     */
    public static function mark_as_viewed($notification_id, PodioClient $podio_client)
    {
        return $podio_client->post("/notification/{$notification_id}/viewed");
    }

    /**
     * @see https://developers.podio.com/doc/notifications/mark-notifications-as-viewed-by-ref-553653
     */
    public static function mark_as_viewed_for_ref($ref_type, $ref_id, PodioClient $podio_client)
    {
        return $podio_client->post("/notification/{$ref_type}/{$ref_id}/viewed");
    }

    /**
     * @see https://developers.podio.com/doc/notifications/star-notification-295910
     */
    public static function star($notification_id, PodioClient $podio_client)
    {
        return $podio_client->post("/notification/{$notification_id}/star");
    }

    /**
     * @see https://developers.podio.com/doc/notifications/un-star-notification-295911
     */
    public static function unstar($notification_id, PodioClient $podio_client)
    {
        return $podio_client->delete("/notification/{$notification_id}/star");
    }
}
