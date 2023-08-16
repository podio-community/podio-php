<?php
/**
 * @see https://developers.podio.com/doc/organizations
 */
class PodioOrganizationMember extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
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
    public static function get(PodioClient $podio_client, $org_id, $user_id)
    {
        return self::member($podio_client->get("/org/{$org_id}/member/{$user_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/organizations/get-organization-members-50661
     */
    public static function get_for_org(PodioClient $podio_client, $org_id, $attributes = array())
    {
        return self::listing($podio_client->get("/org/{$org_id}/member/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/organizations/end-organization-membership-50689
     */
    public static function delete(PodioClient $podio_client, $org_id, $user_id)
    {
        return $podio_client->delete("/org/{$org_id}/member/{$user_id}");
    }
}
