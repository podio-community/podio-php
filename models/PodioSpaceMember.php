<?php
/**
 * @see https://developers.podio.com/doc/space-members
 */
class PodioSpaceMember extends PodioObject {
  public function __construct($attributes = array()) {
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
  public static function get($space_id, $user_id) {
    return self::member(Podio::get("/space/{$space_id}/member/{$user_id}"));
  }

  /**
   * @see https://developers.podio.com/doc/space-members/get-space-members-by-role-68043
   */
  public static function get_by_role($space_id, $role) {
    return self::listing(Podio::get("/space/{$space_id}/member/{$role}/"));
  }

  /**
   * @see https://developers.podio.com/doc/space-members/end-space-memberships-22399
   */
  public static function delete($space_id, $user_ids) {
    return Podio::delete("/space/{$space_id}/member/{$user_ids}");
  }

  /**
   * @see https://developers.podio.com/doc/space-members/update-space-memberships-22398
   */
  public static function update($space_id, $user_ids, $attributes = array()) {
    return Podio::put("/space/{$space_id}/member/{$user_ids}");
  }

  /**
  * @see https://developers.podio.com/doc/space-members/join-space-1927286
  */
  public static function join($space_id) {
    return Podio::post("/space/{$space_id}/join");
  }

}
