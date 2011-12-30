<?php

/**
 * This is merely an example.
 */
class PodioExample {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Example get request.
   */
  public function getSomething($attributes = array()) {
    if ($response = $this->podio->get('/example/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
