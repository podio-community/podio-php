<?php
/**
 * @see https://developers.podio.com/doc/users
 */
class PodioUser extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('user_id', 'integer', array('id' => true));
        $this->property('profile_id', 'integer');
        $this->property('name', 'string');
        $this->property('link', 'string');
        $this->property('avatar', 'integer');
        $this->property('mail', 'string');
        $this->property('status', 'string');
        $this->property('locale', 'string');
        $this->property('timezone', 'string');
        $this->property('flags', 'array');
        $this->property('type', 'string');
        $this->property('created_on', 'datetime');

        $this->has_one('profile', 'Contact');
        $this->has_many('mails', 'UserMail');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/users/get-user-22378
     */
    public static function get(PodioClient $podio_client)
    {
        return self::member($podio_client, $podio_client->get("/user"));
    }

    /**
     * @see https://developers.podio.com/doc/users/get-user-property-29798
     */
    public static function get_property(PodioClient $podio_client, $name)
    {
        return $podio_client->get("/user/property/{$name}")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/users/set-user-property-29799
     */
    public static function set_property(PodioClient $podio_client, $name, $value)
    {
        return $podio_client->put("/user/property/{$name}", $value);
    }

    /**
     * @see https://developers.podio.com/doc/users/set-user-properties-9052829
     */
    public static function set_properties(PodioClient $podio_client, $attributes)
    {
        return $podio_client->put("/user/property/", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/users/delete-user-property-29800
     */
    public static function delete_property(PodioClient $podio_client, $name)
    {
        return $podio_client->delete("/user/property/{$name}");
    }

    /**
     * @see https://developers.podio.com/doc/users/update-profile-22402
     */
    public static function update_profile(PodioClient $podio_client, $attributes)
    {
        return $podio_client->put("/user/profile/", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/users/get-profile-field-22380
     */
    public static function get_profile_field(PodioClient $podio_client, $field)
    {
        return $podio_client->get("/user/profile/{$field}")->json_body();
    }
}
