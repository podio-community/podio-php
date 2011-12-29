<?php

/**
 * A space is a work area. Apps with their items, status updates and other 
 * things are done on a space. A user can be a member of a space with a 
 * certain role, which dictates his rights.
 */
class PodioSpace {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Returns the top most active members of the space.
   */
  public function getMembersTop($space_id) {
    if ($response = $this->podio->get('/space/'.$space_id.'/member/top')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the top spaces for the user
   */
  public function getTop($attributes = array()) {
    if ($response = $this->podio->get('/space/top/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Add a new space to an organization.
   */
  public function create($attributes = array()) {
    if ($response = $this->podio->post('/space/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the space with the given id
   */
  public function update($space_id, $attributes = array()) {
    if ($response = $this->podio->put('/space/'.$space_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the space with the given id. This will also end all memberships 
   * of the space and cancel any space invites still outstanding.
   */
  public function delete($space_id) {
    if ($response = $this->podio->delete('/space/'.$space_id)) {
      return TRUE;
    }
  }
  
  /**
   * Get the space with the given id
   */
  public function get($space_id) {
    if ($response = $this->podio->get('/space/'.$space_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the space and organization with the given full URL.
   */
  public function getByURL($attributes = array()) {
    if ($response = $this->podio->get('/space/url', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Ends the users membership on the space, can also be called for members 
   * in state invited.
   */
  public function deleteMember($space_id, $user_id) {
    if ($response = $this->podio->delete('/space/'.$space_id.'/member/'.$user_id)) {
      return TRUE;
    }
  }
  
  /**
   * Updates a space membership with another role
   */
  public function updateMember($space_id, $user_id, $attributes = array()) {
    if ($response = $this->podio->put('/space/'.$space_id.'/member/'.$user_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Used to get the details of an active users membership of a space.
   */
  public function getMember($space_id, $user_id) {
    if ($response = $this->podio->get('/space/'.$space_id.'/member/'.$user_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the active members of the given space.
   */
  public function getMembers($space_id) {
    if ($response = $this->podio->get('/space/'.$space_id.'/member/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns a list of the members that have been removed from the space.
   */
  public function getMembersEnded($space_id) {
    if ($response = $this->podio->get('/space/'.$space_id.'/member/ended')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the members that was invited to the space, but has not yet 
   * accepted or declined.
   */
  public function getInvites($space_id) {
    if ($response = $this->podio->get('/space/'.$space_id.'/member/invited')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the status for a space invitation. Used to present the proper 
   * screen to the user when attempting to join a space.
   */
  public function getInviteByToken($attributes = array()) {
    if ($response = $this->podio->get('/space/invite/status', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Invites a list of users (either through user_id or email) to the space.
   */
  public function invite($space_id, $attributes = array()) {
    if ($response = $this->podio->post('/space/'.$space_id.'/invite', $attributes)) {
      return TRUE;
    }
  }
  
  /**
   * Used to accept an invite to a space
   */
  public function inviteAccept($attributes = array()) {
    if ($response = $this->podio->post('/space/invite/accept', $attributes)) {
      return TRUE;
    }
  }
  
  /**
   * Used to decline an invite to a space for the active user
   */
  public function inviteDecline($attributes = array()) {
    if ($response = $this->podio->post('/space/invite/decline', $attributes)) {
      return TRUE;
    }
  }
  
  /**
   * Resends the space invite with a new subject and message.
   */
  public function inviteResend($space_id, $user_id, $attributes = array()) {
    if ($response = $this->podio->post('/space/'.$space_id.'/member/'.$user_id.'/resend_invite', $attributes)) {
      return TRUE;
    }
  }
  
  /**
   * Returns the statistics for the space with the number of members, statuses,
   * items and comments.
   */
  public function getStatistics($space_id) {
    if ($response = $this->podio->get('/space/'.$space_id.'/statistics')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
