<?php
/**
 * @see https://developers.podio.com/doc/contacts
 */
class PodioContact extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('profile_id', 'integer');
        $this->property('user_id', 'integer');
        $this->property('name', 'string');
        $this->property('avatar', 'integer');
        $this->property('birthdate', 'date');
        $this->property('department', 'string');
        $this->property('vatin', 'string');
        $this->property('skype', 'string');
        $this->property('about', 'string');
        $this->property('address', 'array');
        $this->property('zip', 'string');
        $this->property('city', 'string');
        $this->property('country', 'string');
        $this->property('state', 'string');
        $this->property('im', 'array');
        $this->property('location', 'array');
        $this->property('mail', 'array');
        $this->property('phone', 'array');
        $this->property('title', 'array');
        $this->property('url', 'array');
        $this->property('skill', 'array');
        $this->property('linkedin', 'string');
        $this->property('twitter', 'string');
        $this->property('organization', 'string');
        $this->property('type', 'string');
        $this->property('space_id', 'integer');
        $this->property('link', 'string');
        $this->property('rights', 'array');

        $this->property('app_store_about', 'string');
        $this->property('app_store_organization', 'string');
        $this->property('app_store_location', 'string');
        $this->property('app_store_title', 'string');
        $this->property('app_store_url', 'string');

        $this->property('last_seen_on', 'datetime');
        $this->property('is_employee', 'boolean');

        // Only available for space contacts
        $this->property('role', 'integer');
        $this->property('removable', 'boolean');

        $this->has_one('image', 'File');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/contacts/create-space-contact-65590
     */
    public static function create(PodioClient $podio_client, $space_id, $attributes = array())
    {
        $body = $podio_client->post("/contact/space/{$space_id}/", $attributes)->json_body();
        return $body['profile_id'];
    }

    /**
     * @see https://developers.podio.com/doc/contacts/delete-contact-s-60560
     */
    public static function delete(PodioClient $podio_client, $profile_ids)
    {
        return $podio_client->delete("/contact/{$profile_ids}");
    }

    /**
     * @see https://developers.podio.com/doc/contacts/get-user-contact-field-22403
     */
    public static function get_field_for_user(PodioClient $podio_client, $user_id, $key)
    {
        return $podio_client->get("/contact/user/{$user_id}/{$key}")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/contacts/get-contact-totals-60467
     */
    public static function get_totals(PodioClient $podio_client)
    {
        return $podio_client->get("/contact/totals/")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/contacts/get-contact-totals-v3-34629208
     */
    public static function get_totals_v3(PodioClient $podio_client)
    {
        return $podio_client->get("/contact/totals/v3/")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/contacts/get-space-contact-totals-67508
     */
    public static function get_totals_for_space(PodioClient $podio_client, $space_id)
    {
        return $podio_client->get("/contact/space/{$space_id}/totals/")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/contacts/get-contact-s-22335
     */
    public static function get(PodioClient $podio_client, $profile_ids, $attributes = array())
    {
        $result = $podio_client->get("/contact/{$profile_ids}/v2", $attributes);
        if (is_array($result->json_body())) {
            return self::listing($podio_client, $result);
        }
        return self::member($podio_client, $result);
    }

    /**
     * @see https://developers.podio.com/doc/contacts/get-vcard-213496
     */
    public static function vcard(PodioClient $podio_client, $profile_id)
    {
        return $podio_client->get("/contact/{$profile_id}/vcard")->body;
    }

    /**
     * @see https://developers.podio.com/doc/contacts/get-user-contact-60514
     */
    public static function get_for_user(PodioClient $podio_client, $user_id)
    {
        return self::member($podio_client, $podio_client->get("/contact/user/{$user_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/contacts/get-contacts-22400
     */
    public static function get_all(PodioClient $podio_client, $attributes = array())
    {
        return self::listing($podio_client, $podio_client->get("/contact/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/contacts/get-organization-contacts-22401
     */
    public static function get_for_org(PodioClient $podio_client, $org_id, $attributes = array())
    {
        return self::listing($podio_client, $podio_client->get("/contact/org/{$org_id}", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/contacts/get-space-contacts-22414
     */
    public static function get_for_space(PodioClient $podio_client, $space_id, $attributes = array())
    {
        return self::listing($podio_client, $podio_client->get("/contact/space/{$space_id}/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/contacts/get-space-contacts-on-app-79475279
     */
    public static function get_for_app(PodioClient $podio_client, $app_id, $attributes = array())
    {
        return self::listing($podio_client, $podio_client->get("/contact/app/{$app_id}/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/contacts/get-skills-1346872
     */
    public static function get_skills(PodioClient $podio_client, $attributes = array())
    {
        return $podio_client->get("/contact/skill/", $attributes)->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/contacts/update-contact-60556
     */
    public static function update(PodioClient $podio_client, $profile_id, $attributes = array())
    {
        return $podio_client->put("/contact/{$profile_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/contacts/update-contact-field-60558
     */
    public static function update_field(PodioClient $podio_client, $profile_id, $key, $attributes = array())
    {
        return $podio_client->put("/contact/{$profile_id}/{$key}", $attributes);
    }
}
