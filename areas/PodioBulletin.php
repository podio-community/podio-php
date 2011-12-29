<?php

/**
 * Bulletins are small updates sent from the Podio team. It can contain 
 * information about new releases, upcoming events and notifications about 
 * system maintenance. Bulletins are received by all users of Hoist, and 
 * can only be sent by Hoist employees.
 */
class PodioBulletin {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Returns all the bulletins
   */
  public function getAll() {
    if ($response = $this->podio->get('/bulletin/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the bulletin with the given id
   */
  public function get($bulletin_id) {
    if ($response = $this->podio->get('/bulletin/'.$bulletin_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
