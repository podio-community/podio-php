<?php
/**
 * @see https://developers.podio.com/doc/spaces
 */
class PodioSpace extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('space_id', 'integer', array('id' => true));
        $this->property('name', 'string');
        $this->property('url', 'string');
        $this->property('url_label', 'string');
        $this->property('org_id', 'integer');
        $this->property('contact_count', 'integer');
        $this->property('members', 'integer');
        $this->property('role', 'string');
        $this->property('rights', 'array');
        $this->property('post_on_new_app', 'boolean');
        $this->property('post_on_new_member', 'boolean');
        $this->property('subscribed', 'boolean');
        $this->property('privacy', 'string');
        $this->property('auto_join', 'boolean');
        $this->property('type', 'string');
        $this->property('premium', 'boolean');
        $this->property('description', 'string');

        $this->property('created_on', 'datetime');
        $this->property('last_activity_on', 'datetime');

        $this->has_one('created_by', 'ByLine');
        $this->has_one('org', 'Organization');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/spaces/get-space-22389
     */
    public static function get($space_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/space/{$space_id}"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/organizations/get-spaces-on-organization-22387
     */
    public static function get_for_org($org_id, PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/org/{$org_id}/space/"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/spaces/get-space-by-url-22481
     */
    public static function get_for_url($attributes = array(), PodioClient $podio_client)
    {
        return self::member($podio_client->get("/space/url", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/spaces/get-available-spaces-1911961
     */
    public static function get_available($org_id, PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/space/org/{$org_id}/available/"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/spaces/get-top-spaces-22477
     */
    public static function get_top($attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/space/top/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/spaces/create-space-22390
     */
    public static function create($attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->post("/space/", $attributes)->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/spaces/update-space-22391
     */
    public static function update($space_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->put("/space/{$space_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/spaces/delete-space-22417
     */
    public static function delete($space_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->delete("/space/{$space_id}");
    }
}
