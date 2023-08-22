<?php
/**
 * @see https://developers.podio.com/doc/applications
 */
class PodioApp extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('app_id', 'integer', array('id' => true));
        $this->property('original', 'integer');
        $this->property('original_revision', 'integer');
        $this->property('status', 'string');
        $this->property('icon', 'string');
        $this->property('icon_id', 'integer');
        $this->property('space_id', 'integer');
        $this->property('owner_id', 'integer');
        $this->property('owner', 'hash'); // TODO: User class?
        $this->property('config', 'hash');
        $this->property('subscribed', 'boolean');
        $this->property('rights', 'array');
        $this->property('link', 'string');
        $this->property('url_add', 'string');
        $this->property('token', 'string');
        $this->property('url_label', 'string');
        $this->property('mailbox', 'string');

        $this->has_one('integration', 'Integration');
        $this->has_many('fields', 'AppField');

        // When app is returned as part of large collection (e.g. for stream), some config properties is moved to the main object
        $this->property('name', 'string');
        $this->property('item_name', 'string');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/applications/get-app-22349
     */
    public static function get(PodioClient $podio_client, $app_id, $attributes = array())
    {
        return self::member($podio_client->get("/app/{$app_id}", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/applications/get-all-user-apps-5902728
     */
    public static function get_all(PodioClient $podio_client, $attributes = array())
    {
        return self::listing($podio_client->get("/app/v2/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/applications/get-top-apps-22476
     */
    public static function get_top(PodioClient $podio_client, $attributes = array())
    {
        return self::listing($podio_client->get("/app/top/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/applications/get-top-apps-for-organization-1671395
     */
    public static function get_top_for_org(PodioClient $podio_client, $org_id, $attributes = array())
    {
        return self::listing($podio_client->get("/app/org/{$org_id}/top/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/applications/get-app-on-space-by-url-label-477105
     */
    public static function get_for_url(PodioClient $podio_client, $space_id, $url_label, $attributes = array())
    {
        return self::member($podio_client->get("/app/space/{$space_id}/{$url_label}", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/applications/get-apps-by-space-22478
     */
    public static function get_for_space(PodioClient $podio_client, $space_id, $attributes = array())
    {
        return self::listing($podio_client->get("/app/space/{$space_id}/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/applications/add-new-app-22351
     */
    public static function create(PodioClient $podio_client, $attributes = array(), $silent = false)
    {
        return self::member($podio_client->post($podio_client->url_with_options("/app/", array('silent' => $silent)), $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/applications/update-app-22352
     */
    public static function update(PodioClient $podio_client, $app_id, $attributes = array(), $silent = false)
    {
        return $podio_client->put($podio_client->url_with_options("/app/{$app_id}", array('silent' => $silent)), $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/applications/delete-app-43693
     */
    public static function delete(PodioClient $podio_client, $app_id, $silent = false)
    {
        return $podio_client->delete($podio_client->url_with_options("/app/{$app_id}", array('silent' => $silent)));
    }

    /**
     * @see https://developers.podio.com/doc/applications/install-app-22506
     */
    public static function install(PodioClient $podio_client, $app_id, $attributes = array())
    {
        $body = $podio_client->post("/app/{$app_id}/install", $attributes)->json_body();
        return $body['app_id'];
    }

    /**
     * @see https://developers.podio.com/doc/applications/update-app-order-22463
     */
    public static function update_org(PodioClient $podio_client, $space_id, $attributes = array())
    {
        return $podio_client->put("/app/space/{$space_id}/order", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/applications/activate-app-43822
     */
    public static function activate(PodioClient $podio_client, $app_id)
    {
        return $podio_client->post("/app/{$app_id}/activate");
    }

    /**
     * @see https://developers.podio.com/doc/applications/deactivate-app-43821
     */
    public static function deactivate(PodioClient $podio_client, $app_id)
    {
        return $podio_client->post("/app/{$app_id}/deactivate");
    }

    /**
     * @see https://developers.podio.com/doc/applications/get-calculations-for-app-773005
     */
    public static function calculations(PodioClient $podio_client, $app_id)
    {
        return self::listing($podio_client->get("/app/{$app_id}/calculation/"));
    }

    /**
     * @see https://developers.podio.com/doc/applications/get-features-43648
     */
    public static function features(PodioClient $podio_client, $attributes = array())
    {
        return $podio_client->get("/app/features/")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/applications/get-app-dependencies-39159
     */
    public static function dependencies(PodioClient $podio_client, $app_id)
    {
        $result = $podio_client->get("/app/{$app_id}/dependencies/")->json_body();
        $result['apps'] = self::listing($result['apps']);
        return $result;
    }

    /**
     * @see https://developers.podio.com/doc/applications/get-space-app-dependencies-45779
     */
    public static function dependencies_space(PodioClient $podio_client, $space_id)
    {
        $result = $podio_client->get("/space/{$space_id}/dependencies/")->json_body();
        $result['apps'] = self::listing($result['apps']);
        return $result;
    }

    /**
     * Activate app in space. Only applicable to Platform
     */
    public static function activate_for_space(PodioClient $podio_client, $app_id, $space_id, $attributes = array())
    {
        return $podio_client->put("/app/{$app_id}/activate/{$space_id}", $attributes);
    }
}
