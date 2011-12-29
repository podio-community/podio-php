<?php

/**
 * This API controls the integration supported by Podio "out-of-the-box".
 */
class PodioIntegration {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Returns the integration with the given id.
   */
  public function get($app_id) {
    if ($response = $this->podio->get('/integration/'.$app_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Refreshes the integration. This will update all items in the background.
   */
  public function refresh($app_id) {
    if ($response = $this->podio->post('/integration/'.$app_id.'/refresh')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
