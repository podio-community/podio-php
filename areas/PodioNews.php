<?php

/**
 * The news API will supply the news items, which are small updates about Podio.
 */
class PodioNews {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Returns relevant stream entries for the logged in user
   */
  public function getStreamEntry() {
    if ($response = $this->podio->get('/news/stream')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
