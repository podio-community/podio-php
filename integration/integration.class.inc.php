<?php

/**
 * This API makes it possible to search across Podio. For now the API is very 
 * limited, but will be expanded greatly in the future.
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
   */
  public function get($app_id) {
    if ($response = $this->podio->request('/integration/'.$app_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

