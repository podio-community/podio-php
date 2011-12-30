<?php

/**
 * Embeds are links shared by users in statuses, conversations and comments.
 */
class PodioEmbed {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Grabs metadata and returns metadata for the given url such as title, 
   * description and thumbnails.
   */
  public function create($attributes = array()) {
    if ($response = $this->podio->post('/embed/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
