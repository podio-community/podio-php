<?php
/**
 * @see https://developers.podio.com/doc/stream
 */
class PodioStreamObject extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('id', 'integer');
        $this->property('type', 'string');
        $this->property('last_update_on', 'datetime');
        $this->property('title', 'string');
        $this->property('link', 'string');
        $this->property('rights', 'array');
        $this->property('data', 'hash');
        $this->property('comments_allowed', 'boolean');
        $this->property('user_ratings', 'hash');
        $this->property('created_on', 'datetime');

        $this->has_one('created_by', 'ByLine');
        $this->has_one('created_via', 'Via');
        $this->has_one('app', 'App');
        $this->has_one('space', 'Space');
        $this->has_one('organization', 'Organization');

        $this->has_many('comments', 'Comment');
        $this->has_many('files', 'File');
        $this->has_many('activity', 'Activity');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/stream/get-global-stream-80012
     */
    public static function get($attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/stream/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/stream/get-organization-stream-80038
     */
    public static function get_for_org($org_id, $attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/stream/org/{$org_id}/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/stream/get-space-stream-80039
     */
    public static function get_for_space($space_id, $attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/stream/space/{$space_id}/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/stream/get-app-stream-264673
     */
    public static function get_for_app($app_id, $attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/stream/app/{$app_id}/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/stream/get-user-stream-1289318
     */
    public static function get_for_user($user_id, $attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/stream/user/{$user_id}/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/stream/get-app-stream-264673
     */
    public static function personal($attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/stream/personal/", $attributes), $podio_client);
    }
}
