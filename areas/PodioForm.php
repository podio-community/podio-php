<?php

/**
 * Forms can be used to enable direct submission into apps from websites. 
 * Forms can be configured through the API, but can only be used through 
 * the JS script.
 */
class PodioForm {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Creates a new form on an app.
   */
  public function create($app_id, $attributes = array()) {
    if ($response = $this->podio->post('/form/app/'.$app_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the form with new settings, domains, fields, etc.
   */
  public function update($form_id, $attributes = array()) {
    if ($response = $this->podio->put('/form/'.$form_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the form with the given id.
   */
  public function get($form_id) {
    if ($response = $this->podio->get('/form/'.$form_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the active forms on the given app.
   */
  public function getForms($app_id) {
    if ($response = $this->podio->get('/form/app/'.$app_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the form with the given id.
   */
  public function delete($form_id) {
    if ($response = $this->podio->delete('/form/'.$form_id)) {
      return TRUE;
    }
  }
}
