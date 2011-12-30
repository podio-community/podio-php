<?php

/**
 * This API makes it possible to search across Podio. For now the API is very 
 * limited, but will be expanded greatly in the future.
 */
class PodioSearch {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Searches globally in all items, tasks and statuses.
   */
  public function search($words = array()) {
    if ($response = $this->podio->post('/search/', $words)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Searches in an organization.
   */
  public function searchOrganization($org_id, $words = array()) {
    if ($response = $this->podio->post('/search/org/'.$org_id.'/', $words)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Searches in a space.
   */
  public function searchSpace($space_id, $words = array()) {
    if ($response = $this->podio->post('/search/space/'.$space_id.'/', $words)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
