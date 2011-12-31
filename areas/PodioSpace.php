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
   * Returns the top spaces for the user
   */
  public function getTop($attributes = array()) {
    if ($response = $this->podio->get('/space/top/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the available spaces for the given space. This is spaces 
   * that are open and available for the user to join.
   */
  public function available($org_id) {
    if ($response = $this->podio->get('/space/org/'.$org_id.'/available/')) {
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
   * Returns the space and organization with the given full URL.
   */
  public function getByOrgAndURL($org_id, $url_label) {
    if ($response = $this->podio->get('/space/org/'.$org_id.'/'.$url_label)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Validates that the URL label is valid and not in use for the given org.
   */
  public function validate($org_id) {
    if ($response = $this->podio->post('/space/org/'.$org_id.'/url/validate')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
