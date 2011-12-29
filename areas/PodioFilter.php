<?php

/**
 * Filtering can be used to limit the items returned when viewing an app.
 *
 * There are two types of filters:
 * - Saved filters which are filters a user have saved for future reuse 
 *   by the user or other members of the space.
 * - Last used filters, which stores the filter which was last used by 
 *   the user on the given app.
 */
class PodioFilter {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Creates a new filter on the given app.
   */
  public function create($app_id, $attributes = array()) {
    if ($response = $this->podio->post('/filter/app/'.$app_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the definition for the given filter.
   */
  public function get($filter_id) {
    if ($response = $this->podio->get('/filter/'.$filter_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the last filter used by the active user on the given app. 
   * If there was no last filter, the default filter is returned.
   */
  public function getLast($app_id) {
    if ($response = $this->podio->get('/filter/app/'.$app_id.'/last')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the filters on the given app.
   */
  public function getForApp($app_id) {
    if ($response = $this->podio->get('/filter/app/'.$app_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the given filter
   */
  public function delete($filter_id) {
    if ($response = $this->podio->delete('/filter/'.$filter_id)) {
      return TRUE;
    }
  }
}

