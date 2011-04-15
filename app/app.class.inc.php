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
  
  /**
   * Creates a new app on a space. This creates an empty app. Fields must be 
   * created manually afterwards.
   *
   * @param $space_id The id of the space on which the app is placed
   * @param $config Array with the current configuration of the app. Options:
   * - "name": The name of the app
   * - "item_name": The name of each item in an app
   * - "description": The description of the app
   * - "usage": Description of how the app should be used
   * - "external_id": The external id of the app. This can be used to store an 
   *                  id from an external system on the app
   * - "icon": The name of the icon used to represent the app
   * - "allow_edit": True if other members are allowed to edit items from the 
   *                 app, false otherwise
   * - "default_view": The default view of the app items on the app main page
   * - "allow_attachments": True if attachment of files to an item is allowed, 
   *                        false otherwise
   * - "allow_comments": True if members can make comments on an item, 
   *                     false otherwise
   * - "fivestar": True if fivestar rating is enabled on an item, 
   *               false otherwise
   * - "fivestar_label": If fivestar rating is enabled, this is the label that 
   *                     will be presented to the users
   * - "approved": True if an item can be approved, false otherwise
   * - "thumbs": True if an item can have a thumbs up or thumbs down, 
   *             false otherwise
   * - "thumbs_label": If thumbs ratings are enabled, this is the label that 
   *                   will be presented to the users
   * - "rsvp": True if RSVP is enabled, false otherwise
   * - "rsvp_label": If RSVP is enabled, this is the label that will be 
   *                 presented to the users
   * - "yesno": True if yes/no rating is enabled, false otherwise
   * - "yesno_label": If yes/no is enabled, this is the label that will be 
   *                  presented to the users
   * - "tasks": A comma separated list of the tasks that will automatically be 
   *            created when a new item is added
   * @param $notify True if at the space members should be notified about 
   *                this new app, false otherwise
   *
   * @return Array with new app id
   */
  public function create($space_id, $config, $notify, $subscribe) {
    $data = array('space_id' => $space_id, 'config' => $config, 'notify' => $notify);
    if ($response = $this->podio->request('/app/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the app config. Fields must be updates separately.
   *
   * @param $app_id The id of the app to update
   * @param $config New config array. For options see the 'create' method
   * @param $resubscribe True if all space members should be resubscribed to 
   *                     this app, false otherwise
   */
  public function update($app_id, $config) {
    $data = array('config' => $config);
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
    if ($response = $this->podio->request('/app/'.$app_id)) {
      return json_decode($response->getBody(), TRUE);
    }
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
    if ($response = $this->podio->request('/app/space/'.$space_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
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
    if ($response = $this->podio->request('/app/space/'.$space_id.'/available/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Gets a list of apps by certain criteria. The apps are sorted by the 
   * order they were created in, unless apps have been moved manually 
   * by a space administrator.
   *
   * @param $type How the results should be returned. "full" will return both 
   *              config and fields, while "short" will return only the config
   * @param $space_ids A comma-separated list of space ids to which the apps 
   *                   should belong
   * @param $status The status of the app, "active", "inactive" or "deleted". 
   *                Defaults to "active"
   * @param $owner_id The id of the owner of the app
   * @param $external_id The external id of the app. Can be used to get apps 
   *                     based on an external id from another system
   *
   * @return Array of apps.
   */
  public function getApps($type, $space_ids, $status = 'active', $owner_id = NULL, $external_id = NULL) {
    $data = array('type' => $type, 'space_ids' => $space_ids, 'status' => $status);
    if ($owner_id) {
      $data['owner_id'] = $owner_id;
    }
    if ($external_id) {
      $data['external_id'] = $external_id;
    }
    
    if ($data['space_ids'] == '0') {
      return FALSE;
    }

    if ($response = $this->podio->request('/app/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the top apps for the active user. This is the apps that the user 
   * have interacted with the most.
   * 
   * @param $limit The maximum number of apps to return, defaults to 4.
   *
   * @return Array of apps
   */
  public function getTop($limit = 4) {
    if ($response = $this->podio->request('/app/top/', array('limit' => $limit))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Adds a new field to an app
   *
   * @param $app_id The id of the app to add field on
   * @param $type The type of field. See API documentation for possible values
   * @param $config Array with a config object for the field. Options are:
   * - "label": The label of the field, which is what the users will see
   * - "description": The description of the field, shown to the user when 
   *                  inserting and editing
   * - "delta": An integer indicating the order of the field compared to other fields
   * - "settings": The settings of the field which depends on the type of the field
   * - "required": True if the field is required when creating and editing items, false otherwise
   * - "visible": True if the field is visible, false otherwise (might be removed in a future version)
   *
   * @return Array with the new field id
   */
  public function createField($app_id, $type, $config) {
    $data = array('type' => $type, 'config' => $config);
    if ($response = $this->podio->request('/app/'.$app_id.'/field/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the configuration of an app field. The type of the field cannot 
   * be updated, only the configuration.
   * 
   * @param $app_id The id of the app to edit field on
   * @param $field_id The id of the field to update
   * @param $config Array with a config object for the field. Options are:
   * - "label": The label of the field, which is what the users will see
   * - "description": The description of the field, shown to the user when 
   *                  inserting and editing
   * - "delta": An integer indicating the order of the field compared to other fields
   * - "settings": The settings of the field which depends on the type of the field
   * - "required": True if the field is required when creating and editing items, false otherwise
   * - "visible": True if the field is visible, false otherwise (might be removed in a future version)
   */
  public function updateField($app_id, $field_id, $config) {
    if ($response = $this->podio->request('/app/'.$app_id.'/field/'.$field_id, $config, HTTP_Request2::METHOD_PUT)) {
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

