<?php
/**
 * @see https://developers.podio.com/doc/organizations
 */
class PodioOrganization extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('org_id', 'integer', array('id' => true));
        $this->property('name', 'string');
        $this->property('type', 'string');
        $this->property('logo', 'integer');
        $this->property('url', 'string');
        $this->property('user_limit', 'integer');
        $this->property('url_label', 'string');
        $this->property('premium', 'boolean');
        $this->property('role', 'string');
        $this->property('status', 'string');
        $this->property('sales_agent_id', 'integer');
        $this->property('created_on', 'datetime');
        $this->property('domains', 'array');
        $this->property('rights', 'array');
        $this->property('rank', 'integer');

        $this->has_one('created_by', 'ByLine');
        $this->has_one('image', 'File');
        $this->has_many('spaces', 'Space');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/organizations/get-organization-22383
     */
    public static function get(PodioClient $podio_client, $org_id)
    {
        return self::member($podio_client, $podio_client->get("/org/{$org_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/organizations/get-organization-by-url-22384
     */
    public static function get_for_url(PodioClient $podio_client, $attributes = array())
    {
        return self::member($podio_client, $podio_client->get("/org/url", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/organizations/get-organizations-22344
     */
    public static function get_all(PodioClient $podio_client)
    {
        return self::listing($podio_client, $podio_client->get("/org/"));
    }

    /**
     * @see https://developers.podio.com/doc/organizations/add-new-organization-22385
     */
    public static function create(PodioClient $podio_client, $attributes = array())
    {
        return self::member($podio_client, $podio_client->post("/org/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/organizations/add-organization-admin-50854
     */
    public static function create_admin(PodioClient $podio_client, $org_id, $attributes = array())
    {
        return $podio_client->post("/org/{$org_id}/admin/", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/organizations/get-organization-admins-81542
     */
    public static function get_all_admins(PodioClient $podio_client, $org_id)
    {
        return PodioUser::listing($podio_client, $podio_client->get("/org/{$org_id}/admin/"));
    }

    /**
     * @see https://developers.podio.com/doc/organizations/get-organization-login-report-51730
     */
    public static function get_login_report(PodioClient $podio_client, $org_id, $attributes = array())
    {
        return $podio_client->get("/org/{$org_id}/report/login", $attributes)->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/organizations/get-organization-statistics-28734
     */
    public static function get_statistics(PodioClient $podio_client, $org_id, $attributes = array())
    {
        return $podio_client->get("/org/{$org_id}/statistics", $attributes)->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/organizations/update-organization-22386
     */
    public static function update(PodioClient $podio_client, $org_id, $attributes = array())
    {
        return $podio_client->put("/org/{$org_id}", $attributes);
    }

    /**
     * Bootstrap organization. Only applicable on Podio Platform
     */
    public static function bootstrap(PodioClient $podio_client, $attributes = array())
    {
        return $podio_client->post("/org/bootstrap", $attributes);
    }
}
