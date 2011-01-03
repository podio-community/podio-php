<?php

/**
 * This area holds organizations. An organization is the container for spaces.
 */
class PodioOrgAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Creates a new organization
   *
   * @param $name The name of the new organization
   * @param $logo The file id of the logo of the organization, 
   *              leave blank for no logo
   *
   * @return Array with the new organization id and URL
   */
  public function create($name, $logo = NULL) {
    if ($response = $this->podio->request('/org/', array('name' => $name, 'logo' => $logo), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates an organization with new name and logo. Note that the URL of the 
   * organization will not change even though the name changes.
   *
   * @param $org_id The id of the organization to update
   * @param $name The new name
   * @param $logo The file id of the logo
   */
  public function update($org_id, $name, $logo = NULL) {
    $data = array('name' => $name);
    if ($logo) {
      $data['logo'] = $logo;
    }
    if ($response = $this->podio->request('/org/'.$org_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the organization with the given id. This will also delete all 
   * spaces under the organization
   *
   * @param $org_id The organization id to delete
   */
  public function delete($org_id) {
    if ($response = $this->podio->request('/org/'.$org_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Gets the organization with the given id.
   *
   * @param $org_id The organization id to retrieve
   *
   * @return An organization object
   */
  public function get($org_id) {
    if ($response = $this->podio->request('/org/'.$org_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the organization with the given full URL. The URL does not have 
   * to be truncated to the root, it can be to any resource on the URL.
   *
   * @param $url The URL to search for
   *
   * @return An organization object
   */
  public function getByUrl($url) {
    if ($response = $this->podio->request('/org/url', array('url' => $url))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the spaces for the organization.
   *
   * @param $org_id The id of the organization to get spaces for
   *
   * @return Array of space objects
   */
  public function getOrgSpaces($org_id) {
    if ($response = $this->podio->request('/org/'.$org_id.'/space')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns a list of all the organizations and spaces the user is member of.
   *
   * @return Array of organization objects
   */
  public function getOrgs() {
    if ($response = $this->podio->request('/org/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the organizations and spaces that the logged in user shares with 
   * the specified user. The organizations and spaces will be returned sorted 
   * by name.
   *
   * @param $user_id The user to compare current user to
   *
   * @return Array of organizations with spaces
   */
  public function getShared($user_id) {
    if ($response = $this->podio->request('/org/shared/'.$user_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
}

