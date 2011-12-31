<?php

/**
 * Manages membership of spaces.
 */
class PodioSpaceMember {
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
  public function getTop($space_id) {
    if ($response = $this->podio->get('/space/'.$space_id.'/member/top')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Ends the users membership on the space, can also be called for members 
   * in state invited.
   */
  public function delete($space_id, $user_id) {
    if ($response = $this->podio->delete('/space/'.$space_id.'/member/'.$user_id)) {
      return TRUE;
    }
  }
  
  /**
   * Updates a space membership with another role
   */
  public function update($space_id, $user_id) {
    if ($response = $this->podio->put('/space/'.$space_id.'/member/'.$user_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Joins the open space with the given id
   */
  public function join($space_id) {
    if ($response = $this->podio->put('/space/'.$space_id.'/join')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Used to get the details of an active users membership of a space.
   */
  public function get($space_id, $user_id) {
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
   * Returns the space members with the specified role.
   */
  public function getByRole($space_id, $role) {
    if ($response = $this->podio->get('/space/'.$space_id.'/member/'.$role.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns a list of the members that have been removed from the space.
   */
  public function getEnded($space_id) {
    if ($response = $this->podio->get('/space/'.$space_id.'/member/ended')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
