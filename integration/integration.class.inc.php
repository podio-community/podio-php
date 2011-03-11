<?php

/**
 * This API controls the integration supported by Podio "out-of-the-box".
 */
class PodioIntegrationAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Returns the integration with the given id.
   *
   * @param $app_id The app to get integration for
   */
  public function get($app_id) {
    if ($response = $this->podio->request('/integration/'.$app_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Refreshes the integration. This will update all items in the background.
   *
   * @param $app_id The app to get integration for
   */
  public function refresh($app_id) {
    if ($response = $this->podio->request('/integration/'.$app_id.'/refresh', array(), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

