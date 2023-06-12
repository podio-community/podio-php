<?php
/**
 * @see https://developers.podio.com/doc/notifications
 */
class PodioNotificationGroup extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->has_one('context', 'NotificationContext');
        $this->has_many('notifications', 'Notification');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/notifications/get-notification-v2-2973737
     */
    public static function get($notification_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/notification/{$notification_id}/v2"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/notifications/get-notifications-290777
     */
    public static function get_all($attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/notification/", $attributes), $podio_client);
    }
}
