<?php

/**
 * Methods for interacting with mobile devices
 */
class PodioMobileAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Pushes the app to the user's mobile phone(s)
   *
   * @param $app_id The ID of the app to add
   */
  public function install_app($app_id) {
    if ($response = $this->podio->request('/mobile/install_app/'.$app_id, array(), HTTP_Request2::METHOD_POST)) {
      podio_log($response);
      return json_decode($response->getBody(), TRUE);
    }
  }
}

