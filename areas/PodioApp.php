<?php

/**
 * This area is used to manage application definitions. An application 
 * definition, commonly called just an app, is the setup of an 
 * application. It consists primarily of a list of fields and secondly 
 * of various settings.
 */
class PodioApp {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Creates a new app on a space.
   */
  public function create($attributes = array()) {
    if ($response = $this->podio->post('/app/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates an app.
   */
  public function update($app_id, $attributes = array()) {
    if ($response = $this->podio->put('/app/'.$app_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Activates a deactivated app. This puts the app back in the app navigator
   * and allows insertion of new items.
   */
  public function activate($app_id) {
    if ($response = $this->podio->post('/app/'.$app_id.'/activate')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deactivates the app with the given id. This removes the app from the app
   * navigator, and disables insertion of new items.
   */
  public function deactivate($app_id) {
    if ($response = $this->podio->post('/app/'.$app_id.'/deactivate')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the app with the given id. This will delete all items, widgets, 
   * filters and shares on the app. This operating is not reversible.
   */
  public function delete($app_id) {
    if ($response = $this->podio->delete('/app/'.$app_id)) {
      return TRUE;
    }
  }
  
  /**
   * Returns a single field from an app.
   */
  public function getField($app_id, $field_id) {
    if ($response = $this->podio->get('/app/'.$app_id.'/field/'.$field_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Gets the definition of an app and can include configuration and fields. 
   * This method will always return the latest revision of the app definition.
   */
  public function get($app_id, $attributes = array()) {
    if ($response = $this->podio->get('/app/'.$app_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the app on the given space with the given URL label
   */
  public function getByLabel($space_id, $url_label, $attributes = array()) {
    if ($response = $this->podio->get('/app/space/'.$space_id.'/'.$url_label, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the apps that the given app depends on.
   */
  public function getDependencies($app_id) {
    if ($response = $this->podio->get('/app/'.$app_id.'/dependencies/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the active apps on the space along with their dependencies. 
   * The dependencies are only one level deep.
   */
  public function getSpaceAppDependencies($space_id) {
    if ($response = $this->podio->get('/app/space/'.$space_id.'/dependencies/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the features that the given apps and optionally space includes. 
   */
  public function getFeatures($attributes = array()) {
    if ($response = $this->podio->get('/app/features/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the apps on the space that are visible. The apps are sorted 
   * by any custom ordering and else by name.
   */
  public function getSpaceApps($space_id) {
    if ($response = $this->podio->get('/app/space/'.$space_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the apps available for install on the given space. This includes 
   * all the apps that are visible and allows insert on all the others space 
   * the user is a member of.
   */
  public function getAvailableApps($space_id) {
    if ($response = $this->podio->get('/app/space/'.$space_id.'/available/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Gets a list of apps by certain criteria. The apps are sorted by the 
   * order they were created in, unless apps have been moved manually 
   * by a space administrator.
   */
  public function getApps($attributes = array()) {
    if ($response = $this->podio->get('/app/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the top apps for the active user. This is the apps that the user 
   * have interacted with the most.
   */
  public function getTop($attributes = array()) {
    if ($response = $this->podio->get('/app/top/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Adds a new field to an app
   */
  public function createField($app_id, $attributes = array()) {
    if ($response = $this->podio->post('/app/'.$app_id.'/field/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the configuration of an app field. The type of the field cannot 
   * be updated, only the configuration.
   */
  public function updateField($app_id, $field_id, $attributes = array()) {
    if ($response = $this->podio->put('/app/'.$app_id.'/field/'.$field_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the app field with the given id. This operating is not reversible.
   */
  public function deleteField($app_id, $field_id) {
    if ($response = $this->podio->delete('/app/'.$app_id.'/field/'.$field_id)) {
      return TRUE;
    }
  }
  
  /**
   * Updates the order of the apps on the space. It should post all the apps 
   * from the space in the order required.
   */
  public function reorder($space_id, $attributes = array()) {
    if ($response = $this->podio->put('/app/space/'.$space_id.'/order', $attributes)) {
      return TRUE;
    }
  }
  
  /**
   * Installs the app with the given id on the space.
   */
  public function install($app_id, $attributes = array()) {
    if ($response = $this->podio->post('/app/'.$app_id.'/install', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the list of possible calculations that can be done on related apps
   */
  public function calculation($app_id) {
    if ($response = $this->podio->get('/app/'.$app_id.'/calculation/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
