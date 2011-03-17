<?php

/**
 * A space is a work area. Apps with their items, status updates and other 
 * things are done on a space. A user can be a member of a space with a 
 * certain role, which dictates his rights.
 */
class PodioSpaceAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Returns the top most active members of the space.
   *
   * @param $space_id The space to get members for
   *
   * @return Array of users
   */
  public function getMembersTop($space_id) {
    if ($response = $this->podio->request('/space/'.$space_id.'/member/top')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the top spaces for the user
   *
   * @param $limit The number of spaces to get. Defaults to 6
   *
   * @return Array of spaces
   */
  public function getTop($limit = 6) {
    if ($response = $this->podio->request('/space/top/', array('limit' => $limit))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Add a new space to an organization.
   *
   * @param $org_id The id of the organization the space is in
   * @param $name The name of the space
   * @param $post_on_new_app True if new apps should be announced with 
   *                         a status update, false otherwise
   * @param $post_on_new_member True if new members should be announced 
   *                            with a status update, false otherwise
   *
   * @return Array with the new space id and URL
   */
  public function create($org_id, $name, $post_on_new_app = TRUE, $post_on_new_member = TRUE) {
    $data = array(
      'org_id' => $org_id,
      'name' => $name,
      'post_on_new_app' => $post_on_new_app,
      'post_on_new_member' => $post_on_new_member,
    );
    
    if ($response = $this->podio->request('/space/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the space with the given id
   * 
   * @param $space_id The id of the space to act on
   * @param $name The name of the space
   * @param $post_on_new_app True if new apps should be announced with a 
   *                         status update, false otherwise
   * @param $post_on_new_member True if new members should be announced 
   *                            with a status update, false otherwise
   */
  public function update($space_id, $name, $post_on_new_app, $post_on_new_member) {
    $data = array(
      'name' => $name,
      'post_on_new_app' => $post_on_new_app ? TRUE : FALSE,
      'post_on_new_member' => $post_on_new_member ? TRUE : FALSE,
    );
    if ($response = $this->podio->request('/space/'.$space_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the space with the given id. This will also end all memberships 
   * of the space and cancel any space invites still outstanding.
   *
   * @param $space_id The id of the space to delete
   */
  public function delete($space_id) {
    if ($response = $this->podio->request('/space/'.$space_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Get the space with the given id
   *
   * @param $space_id The id of the space to get
   *
   * @return Space object
   */
  public function get($space_id) {
    if (!$space_id) {
      return FALSE;
    }
    
    if ($response = $this->podio->request('/space/'.$space_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the space and organization with the given full URL.
   *
   * @param $url The URL of the space to retrieve
   * @param $info Set to "1" to return the informationals on the 
   *              space, "0" otherwise. Defaults to "0".
   *
   * @return Space object
   */
  public function getByURL($url, $info = 0) {
    if ($response = $this->podio->request('/space/url', array('url' => $url, 'info' => $info))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Ends the users membership on the space, can also be called for members 
   * in state invited.
   * 
   * @param $space_id The id of the space to end membership on
   * @param $user_id The id of the user to end membership for
   */
  public function deleteMember($space_id, $user_id) {
    if ($space_id > 0 && $user_id > 0 && $response = $this->podio->request('/space/'.$space_id.'/member/'.$user_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Updates a space membership with another role
   *
   * @param $space_id The id of the space to update membership on
   * @param $user_id The user to act on
   * @param $role The new role that the user should have on the space
   */
  public function updateMember($space_id, $user_id, $role) {
    if ($response = $this->podio->request('/space/'.$space_id.'/member/'.$user_id, array('role' => $role), HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Used to get the details of an active users membership of a space.
   *
   * @param $space_id The id of the space to get membership for
   * @param $user_id The user id of the user to get membership for
   *
   * @return A user object with membership status
   */
  public function getMember($space_id, $user_id) {
    if (!$space_id) {
      return FALSE;
    }
    
    if ($response = $this->podio->request('/space/'.$space_id.'/member/'.$user_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the active members of the given space.
   *
   * @param $space_id The id of the space to get members for
   *
   * @return Array of users
   */
  public function getMembers($space_id) {
    if ($response = $this->podio->request('/space/'.$space_id.'/member/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns a list of the members that have been removed from the space.
   *
   * @param $space_id The id of the space to get members for
   *
   * @return Array of users
   */
  public function getMembersEnded($space_id) {
    if ($response = $this->podio->request('/space/'.$space_id.'/member/ended')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the members that was invited to the space, but has not yet 
   * accepted or declined.
   *
   * @return Array of users
   */
  public function getInvites($space_id) {
    if ($response = $this->podio->request('/space/'.$space_id.'/member/invited')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the status for a space invitation. Used to present the proper 
   * screen to the user when attempting to join a space.
   *
   * @param $invite_code The invitation code sent in the space invite mail
   *
   * @return Invite object with status, space info etc.
   */
  public function getInviteByToken($invite_code) {
    if ($response = $this->podio->request('/space/invite/status', array('invite_code' => $invite_code))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Invites a list of users (either through user_id or email) to the space.
   *
   * @param $space_id The id of the space to invite to
   * @param $role The role of the new users
   * @param $subject The subject to put in the invitation mail to the users
   * @param $message The personalized message to put in the invitation
   * @param $resend True if the invitation should be resend every week, 
   *                false otherwise
   * @param $notify True if the inviter should be notified when the user 
   *                accepts or declines the invitation
   * @param $users Array of user ids to invite
   * @param $mails Array of e-mails to invite
   */
  public function invite($space_id, $role, $subject, $message, $resend, $notify, $users, $mails) {
    $data = array(
      'role' => $role,
      'subject' => $subject,
      'message' => $message,
      'resend' => $resend,
      'notify' => $notify,
      'users' => $users,
      'mails' => $mails,
    );
    if ($response = $this->podio->request('/space/'.$space_id.'/invite', $data, HTTP_Request2::METHOD_POST)) {
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Used to accept an invite to a space
   *
   * @param $invite_code The invite code that was sent as part of the invitation mail
   */
  public function inviteAccept($invite_code) {
    if ($response = $this->podio->request('/space/invite/accept', array('invite_code' => $invite_code), HTTP_Request2::METHOD_POST)) {
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Used to decline an invite to a space for the active user
   *
   * @param $invite_code The invite code that was sent as part of the invitation mail
   */
  public function inviteDecline($invite_code) {
    if ($response = $this->podio->request('/space/invite/decline', array('invite_code' => $invite_code), HTTP_Request2::METHOD_POST)) {
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Resends the space invite with a new subject and message.
   *
   * @param $space_id The id of the space to act on
   * @param $user_id Id of the user to resend invite to
   * @param $subject The subject to put in the invitation mail
   * @param $message The personalized message to put in the invitation
   */
  public function inviteResend($space_id, $user_id, $subject, $message) {
    $data = array(
      'subject' => $subject,
      'message' => $message,
    );
    if ($response = $this->podio->request('/space/'.$space_id.'/member/'.$user_id.'/resend_invite', $data, HTTP_Request2::METHOD_POST)) {
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Returns the statistics for the space with the number of members, statuses,
   * items and comments.
   *
   * @param $space_id The id of the space to get statistics for
   *
   * @return Array with statistics
   */
  public function getStatistics($space_id) {
    if ($response = $this->podio->request('/space/'.$space_id.'/statistics')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
}

