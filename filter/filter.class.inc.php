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
class PodioFilterAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Creates a new filter on the given app.
   *
   * @param $app_id App id to create filter on
   * @param $name The name of the new filter
   * @param $sort_by How the sorting should be
   * @param $sort_desc True if sorting should be descending, false otherwise
   * @param $filters Array of filters to apply
   *
   * @return Array with the new filter id
   */
  public function create($app_id, $name, $sort_by, $sort_desc = FALSE, $filters = array()) {
    $data = array('name' => $name, 'sort_by' => $sort_by, 'sort_desc' => $sort_desc, 'filters' => $filters);
    if ($response = $this->podio->request('/filter/app/'.$app_id.'/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the definition for the given filter.
   *
   * @param $filter_id The filter id to retrieve
   *
   * @return A filter object
   */
  public function get($filter_id) {
    if ($response = $this->podio->request('/filter/'.$filter_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the last filter used by the active user on the given app. 
   * If there was no last filter, the default filter is returned.
   *
   * @param $app_id The app to get filter for
   *
   * @return A filter object
   */
  public function getLast($app_id) {
    if ($response = $this->podio->request('/filter/app/'.$app_id.'/last')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the filters on the given app.
   *
   * @param $app_id The app to get filters for
   *
   * @return Array of filter objects
   */
  public function getForApp($app_id) {
    if ($response = $this->podio->request('/filter/app/'.$app_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the given filter
   *
   * @param $filter_id Id of filter to delete
   */
  public function delete($filter_id) {
    if ($response = $this->podio->request('/filter/'.$filter_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
}

