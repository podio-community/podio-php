<?php

/**
 * Methods for interacting with mobile devices
 */
class PodioMobile {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Pushes the app to the user's mobile phone(s)
   */
  public function install_app($app_id) {
    if ($response = $this->podio->post('/mobile/install_app/'.$app_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

