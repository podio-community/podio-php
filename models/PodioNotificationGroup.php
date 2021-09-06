<?php
/**
 * @see https://developers.podio.com/doc/notifications
 */
class PodioNotificationGroup extends PodioObject
{
    public function __construct($attributes = array())
    {
        $this->has_one('context', 'NotificationContext');
        $this->has_many('notifications', 'Notification');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/notifications/get-notification-v2-2973737
     */
    public static function get($notification_id)
    {
        return self::member(Podio::get("/notification/{$notification_id}/v2"));
    }

    /**
     * @see https://developers.podio.com/doc/notifications/get-notifications-290777
     */
    public static function get_all($attributes = array())
    {
        return self::listing(Podio::get("/notification/", $attributes));
    }
}
