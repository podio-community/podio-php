<?php

/**
 * The news API will supply the news items, which are small updates about Podio.
 */
class PodioNewsAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Returns relevant stream entries for the logged in user
   *
   * @return News object
   */
  public function getStreamEntry() {
    $data = array();
    if ($response = $this->podio->request('/news/stream', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

}

