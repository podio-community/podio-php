<?php
/**
 * @see https://developers.podio.com/doc/calendar
 */
class PodioCalendarMute extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('id', 'integer');
        $this->property('type', 'string');
        $this->property('title', 'string');
        $this->property('data', 'hash');
        $this->property('item', 'boolean');
        $this->property('status', 'boolean');
        $this->property('task', 'boolean');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-mutes-in-global-calendar-62730
     */
    public static function get_all(PodioClient $podio_client)
    {
        return self::listing($podio_client, $podio_client->get("/calendar/mute/"));
    }
}
