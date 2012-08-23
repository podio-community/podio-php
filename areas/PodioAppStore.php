<?php

/**
 * Apps can be shared to the app store so it can be installed and used by
 * anyone. From the app store the apps can be browsed by top apps,
 * category and author. From here it can also be installed into a space
 * the user is administrator on.
 */
class PodioAppStore {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Returns the apps in the app store in the given category and language.
   */
  public function getByCategory($category_id, $attributes = array()) {
    if ($response = $this->podio->get('/app_store/category/'.$category_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the categories available in the system.
   */
  public function getCategories($attributes = array()) {
    if ($response = $this->podio->get('/app_store/category/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Installs the share with the given id on the space.
   */
  public function install($share_id, $attributes = array()) {
    if ($response = $this->podio->post('/app_store/'.$share_id.'/install/v2', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

}
