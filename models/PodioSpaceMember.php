<?php
/**
 * @see https://developers.podio.com/doc/space-members
 */
class PodioSpaceMember extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('role', 'string');
        $this->property('invited_on', 'datetime');
        $this->property('started_on', 'datetime');
        $this->property('ended_on', 'datetime');

        $this->has_one('user', 'User');
        $this->has_one('profile', 'Contact');
        $this->has_one('space', 'Space');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/space-members/get-space-membership-22397
     */
    public static function get(PodioClient $podio_client, $space_id, $user_id)
    {
        return self::member($podio_client, $podio_client->get("/space/{$space_id}/member/{$user_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/space-members/get-active-members-of-space-22395
     */
    public static function get_all(PodioClient $podio_client, $space_id)
    {
        return self::listing($podio_client, $podio_client->get("/space/{$space_id}/member/"));
    }

    /**
     * @see https://developers.podio.com/doc/space-members/get-space-members-by-role-68043
     */
    public static function get_by_role(PodioClient $podio_client, $space_id, $role)
    {
        return self::listing($podio_client, $podio_client->get("/space/{$space_id}/member/{$role}/"));
    }

    /**
     * @see https://developers.podio.com/doc/space-members/end-space-memberships-22399
     */
    public static function delete(PodioClient $podio_client, $space_id, $user_ids)
    {
        return $podio_client->delete("/space/{$space_id}/member/{$user_ids}");
    }

    /**
     * @see https://developers.podio.com/doc/space-members/update-space-memberships-22398
     */
    public static function update(PodioClient $podio_client, $space_id, $user_ids, $attributes = array())
    {
        return $podio_client->put("/space/{$space_id}/member/{$user_ids}", $attributes);
    }

    /**
    * @see https://developers.podio.com/doc/space-members/join-space-1927286
    */
    public static function join(PodioClient $podio_client, $space_id)
    {
        return $podio_client->post("/space/{$space_id}/join");
    }

    /**
    * @see https://developers.podio.com/doc/space-members/add-member-to-space-1066259
    */
    public static function add(PodioClient $podio_client, $space_id, $attributes = array())
    {
        return $podio_client->post("/space/{$space_id}/member/", $attributes);
    }

    /**
  * @see https://developers.podio.com/doc/space-members/request-space-membership-6146231
  */

    public static function request(PodioClient $podio_client, $space_id)
    {
        return $podio_client->post("/space/{$space_id}/member_request/");
    }
}
