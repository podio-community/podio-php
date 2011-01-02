<?php

/**
 * This area is used to manage application definitions. An application 
 * definition, commonly called just an app, is the setup of an 
 * application. It consists primarily of a list of fields and secondly 
 * of various settings.
 */
class PodioAppAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  public function create($data) {
    if ($response = $this->podio->request('/app/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function update($app_id, $data = array(), $user_id = 0) {
    if ($response = $this->podio->request('/app/'.$app_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Activates a deactivated app. This puts the app back in the app navigator
   * and allows insertion of new items.
   *
   * @param $app_id The id of the app to activate
   */
  public function activate($app_id) {
    if ($response = $this->podio->request('/app/'.$app_id.'/activate', array(), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deactivates the app with the given id. This removes the app from the app
   * navigator, and disables insertion of new items.
   *
   * @param $app_id The id of the app to deactivate
   */
  public function deactivate($app_id) {
    if ($response = $this->podio->request('/app/'.$app_id.'/deactivate', array(), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the app with the given id. This will delete all items, widgets, 
   * filters and shares on the app. This operating is not reversible.
   *
   * @param $app_id The id of the app to delete
   */
  public function delete($app_id) {
    if ($response = $this->podio->request('/app/'.$app_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
    }
  }
  
  /**
   * Returns a single field from an app.
   *
   * @param $app_id The id of the app to retrieve field from
   * @param $field_id The id of the field to retrieve
   */
  public function getField($app_id, $field_id) {
    if ($response = $this->podio->request('/app/'.$app_id.'/field/'.$field_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Gets the definition of an app and can include configuration and fields. 
   * This method will always return the latest revision of the app definition.
   *
   * @param $app_id The id of the app to retrieve
   */
  public function get($app_id) {
    static $list;
    $key = $app_id;
    
    if (!isset($list[$key])) {
      if ($response = $this->podio->request('/app/'.$app_id)) {
        $app = json_decode($response->getBody(), TRUE);
      }
      $list[$key] = $app;
    }
    return $list[$key];
  }
  
  /**
   * Returns the apps that the given app depends on.
   *
   * @param $app_id The id of the app to get dependencies for
   *
   * @return Array with two lists
   * - apps: List of apps
   * - dependencies: Graph of dependencies
   */
  public function getDependencies($app_id) {
    if ($response = $this->podio->request('/app/'.$app_id.'/dependencies/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the active apps on the space along with their dependencies. 
   * The dependencies are only one level deep.
   *
   * @param $space_id The id of the space to get apps for
   *
   * @return Array with two lists
   * - apps: List of apps
   * - dependencies: Graph of dependencies
   */
  public function getSpaceAppDependencies($space_id) {
    if ($response = $this->podio->request('/app/space/'.$space_id.'/dependencies/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the features that the given apps and optionally space includes. 
   * The current list of features are:
   * 
   * - widgets
   * - tasks
   * - filters
   *
   * @param $app_ids
   *        A comma-separated list of app ids from which the features should be extracted
   * @param $include_space 
   *        1 if features from the containing space should be included, 0 otherwise
   *
   * @return Array of features
   */
  public function getFeatures($app_ids, $include_space) {
    if ($response = $this->podio->request('/app/features/', array('app_ids' => implode(',', $app_ids), 'include_space' => $include_space))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the apps on the space that are visible. The apps are sorted 
   * by any custom ordering and else by name.
   *
   * @param $space_id The id of the space to get apps for
   *
   * @return Array of apps
   */
  public function getSpaceApps($space_id) {
    static $list;
    if (!isset($list[$space_id])) {
      if ($response = $this->podio->request('/app/space/'.$space_id.'/')) {
        $list[$space_id] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$space_id];
  }
  
  /**
   * Returns the apps available for install on the given space. This includes 
   * all the apps that are visible and allows insert on all the others space 
   * the user is a member of.
   *
   * @param $space_id The id of the space to get apps for
   *
   * @return Array of apps
   */
  public function getAvailableApps($space_id) {
    static $list;
    if (!isset($list[$space_id])) {
      if ($response = $this->podio->request('/app/space/'.$space_id.'/available/')) {
        $list[$space_id] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$space_id];
  }
  
  
  public function getApps($data, $type = NULL) {
    static $list;
    
    if ($type == 'full' || $type == 'simple') {
      $data['type'] = $type;
    }
    
    if ($data['space_ids'] == '0') {
      return FALSE;
    }

    $key = serialize($data);
    if (!isset($list[$key])) {
      if ($response = $this->podio->request('/app/', $data)) {
        $apps = json_decode($response->getBody(), TRUE);
        $list[$key] = $apps;
      }
    }
    return $list[$key];
  }
  public function createField($app_id, $data, $user_id) {
    if ($response = $this->podio->request('/app/'.$app_id.'/field/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function updateField($app_id, $field_id, $data, $user_id) {
    if ($response = $this->podio->request('/app/'.$app_id.'/field/'.$field_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the order of the apps on the space. It should post all the apps 
   * from the space in the order required.
   *
   * @param $space_id The space to order apps on
   * @param $list An array of app ids in the order needed
   */
  public function reorder($space_id, $list) {
    if ($response = $this->podio->request('/app/space/'.$space_id.'/order', $list, HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Installs the app with the given id on the space.
   *
   * @param $app_id The id of the app to install
   * @param $space_id The id of the space where the app should be installed
   *
   * @return Array with the app_id of the new app
   */
  public function install($app_id, $space_id) {
    if ($response = $this->podio->request('/app/'.$app_id.'/install', array('space_id' => $space_id), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
}

