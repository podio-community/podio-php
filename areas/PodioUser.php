<?php

/**
 * This area holds all the users which just includes basic operations.
 */
class PodioUser {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Gets the active user
   */
  public function get() {
    if ($response = $this->podio->get('/user/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the profile of the active user
   */
  public function getOwnProfile() {
    if ($response = $this->podio->get('/user/profile/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the current status for the user. This includes the user data, 
   * profile data and notification data.
   */
  public function getStatus() {
    if ($response = $this->podio->get('/user/status')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the value (true or false) of the property for the active user with the given name.
   * The property is specific to the auth client used.
   */
  public function getProperty($name) {
    if ($response = $this->podio->get('/user/property/'.$name)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Sets the value of the property for the active user with the given name. 
   * The property is specific to the auth client used.
   */
  public function setProperty($name, $attributes) {
    if ($response = $this->podio->put('/user/property/'.$name, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the property for the active user with the given name.
   * The property is specific to the auth client used.
   */
  public function deleteProperty($name) {
    if ($response = $this->podio->delete('/user/property/'.$name, array())) {
      return TRUE;
    }
  }
  
  /**
   * Updates the fields of an existing profile. Fields not specified will 
   * not be updated. To delete a field set the value of the field to null.
   */
  public function updateProfile($attributes = array()) {
    if ($response = $this->podio->put('/user/profile/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the specific field on the user.
   */
  public function updateProfileField($field, $attributes) {
    if ($response = $this->podio->put('/user/profile/'.$field, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
}

