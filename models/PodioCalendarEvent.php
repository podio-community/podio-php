<?php
/**
 * @see https://developers.podio.com/doc/calendar
 */
class PodioCalendarEvent extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('id', 'integer');
        $this->property('type', 'string');
        $this->property('group', 'string');
        $this->property('title', 'string');
        $this->property('description', 'string');
        $this->property('location', 'string');
        $this->property('status', 'string');
        $this->property('link', 'string');
        $this->property('start', 'datetime');
        $this->property('end', 'datetime');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/actions/get-action-1701120
     */
    public static function get($attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/calendar/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-space-calendar-22459
     */
    public static function get_for_space($space_id, $attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/calendar/space/{$space_id}/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-app-calendar-22460
     */
    public static function get_for_app($app_id, $attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/calendar/app/{$app_id}/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-global-calendar-as-ical-22513
     */
    public static function ical($user_id, $token, PodioClient $podio_client)
    {
        return $podio_client->get("/calendar/ics/{$user_id}/{$token}/")->body;
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-space-calendar-as-ical-22514
     */
    public static function ical_for_space($space_id, $user_id, $token, PodioClient $podio_client)
    {
        return $podio_client->get("/calendar/space/{$space_id}/ics/{$user_id}/{$token}/")->body;
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-app-calendar-as-ical-22515
     */
    public static function ical_for_app($app_id, $user_id, $token, PodioClient $podio_client)
    {
        return $podio_client->get("/calendar/app/{$app_id}/ics/{$user_id}/{$token}/")->body;
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-calendar-summary-1609256
     */
    public static function get_summary($attributes = array(), PodioClient $podio_client)
    {
        $result = $podio_client->get("/calendar/summary", $attributes)->json_body();
        $result['today']['events'] = self::listing($result['today']['events'], $podio_client);
        $result['upcoming']['events'] = self::listing($result['upcoming']['events'], $podio_client);
        return $result;
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-calendar-summary-for-personal-1657903
     */
    public static function get_summary_personal($attributes = array(), PodioClient $podio_client)
    {
        $result = $podio_client->get("/calendar/personal/summary", $attributes)->json_body();
        $result['today']['events'] = self::listing($result['today']['events'], $podio_client);
        $result['upcoming']['events'] = self::listing($result['upcoming']['events'], $podio_client);
        return $result;
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-calendar-summary-for-space-1609328
     */
    public static function get_summary_for_space($space_id, $attributes = array(), PodioClient $podio_client)
    {
        $result = $podio_client->get("/calendar/space/{$space_id}/summary", $attributes)->json_body();
        $result['today']['events'] = self::listing($result['today']['events'], $podio_client);
        $result['upcoming']['events'] = self::listing($result['upcoming']['events'], $podio_client);
        return $result;
    }
}
