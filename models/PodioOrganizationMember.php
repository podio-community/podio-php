<?php
/**
 * @see https://developers.podio.com/doc/organizations
 */
class PodioOrganizationMember extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('admin', 'boolean');
        $this->property('employee', 'boolean');
        $this->property('space_memberships', 'integer');

        $this->has_one('profile', 'Contact');
        $this->has_one('user', 'User');
        $this->has_many('spaces', 'Space');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/organizations/get-organization-member-50908
     */
    public static function get($org_id, $user_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/org/{$org_id}/member/{$user_id}"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/organizations/get-organization-members-50661
     */
    public static function get_for_org($org_id, $attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/org/{$org_id}/member/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/organizations/end-organization-membership-50689
     */
    public static function delete($org_id, $user_id, PodioClient $podio_client)
    {
        return $podio_client->delete("/org/{$org_id}/member/{$user_id}");
    }
}
