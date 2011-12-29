<?php

/**
 * This area holds organizations. An organization is the container for spaces.
 */
class PodioOrganization {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Creates a new organization
   */
  public function create($attributes = array()) {
    if ($response = $this->podio->post('/org/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates an organization with new name and logo. Note that the URL of the 
   * organization will not change even though the name changes.
   */
  public function update($org_id, $attributes = array()) {
    if ($response = $this->podio->put('/org/'.$org_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the organization with the given id. This will also delete all 
   * spaces under the organization
   */
  public function delete($org_id) {
    if ($response = $this->podio->delete('/org/'.$org_id)) {
      return TRUE;
    }
  }
  
  /**
   * Gets the organization with the given id.
   */
  public function get($org_id) {
    if ($response = $this->podio->get('/org/'.$org_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the organization with the given full URL. The URL does not have 
   * to be truncated to the root, it can be to any resource on the URL.
   */
  public function getByUrl($attributes = array()) {
    if ($response = $this->podio->get('/org/url', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the spaces for the organization.
   */
  public function getOrgSpaces($org_id) {
    if ($response = $this->podio->get('/org/'.$org_id.'/space')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns a list of all the organizations and spaces the user is member of.
   */
  public function getOrgs() {
    if ($response = $this->podio->get('/org/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the organizations and spaces that the logged in user shares with 
   * the specified user. The organizations and spaces will be returned sorted 
   * by name.
   */
  public function getShared($user_id) {
    if ($response = $this->podio->get('/org/shared/'.$user_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns interesting statistics for this organization. Only org creator 
   * is allowed to see this.
   */
  public function getStatistics($org_id) {
    if ($response = $this->podio->get('/org/'.$org_id.'/statistics')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
