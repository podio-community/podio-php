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
    public static function get($space_id, $user_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/space/{$space_id}/member/{$user_id}"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/space-members/get-active-members-of-space-22395
     */
    public static function get_all($space_id, PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/space/{$space_id}/member/"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/space-members/get-space-members-by-role-68043
     */
    public static function get_by_role($space_id, $role, PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/space/{$space_id}/member/{$role}/"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/space-members/end-space-memberships-22399
     */
    public static function delete($space_id, $user_ids, PodioClient $podio_client)
    {
        return $podio_client->delete("/space/{$space_id}/member/{$user_ids}");
    }

    /**
     * @see https://developers.podio.com/doc/space-members/update-space-memberships-22398
     */
    public static function update($space_id, $user_ids, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->put("/space/{$space_id}/member/{$user_ids}", $attributes);
    }

    /**
    * @see https://developers.podio.com/doc/space-members/join-space-1927286
    */
    public static function join($space_id, PodioClient $podio_client)
    {
        return $podio_client->post("/space/{$space_id}/join");
    }

    /**
    * @see https://developers.podio.com/doc/space-members/add-member-to-space-1066259
    */
    public static function add($space_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->post("/space/{$space_id}/member/", $attributes);
    }

    /**
  * @see https://developers.podio.com/doc/space-members/request-space-membership-6146231
  */

    public static function request($space_id, PodioClient $podio_client)
    {
        return $podio_client->post("/space/{$space_id}/member_request/");
    }
}
