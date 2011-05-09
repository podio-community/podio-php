<?php

/**
 * Bulletins are small updates sent from the Podio team. It can contain 
 * information about new releases, upcoming events and notifications about 
 * system maintenance. Bulletins are received by all users of Hoist, and 
 * can only be sent by Hoist employees.
 */
class PodioBulletinAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Returns all the bulletins
   *
   * @return An array of bulletin objects
   */
  public function getAll() {
    if ($response = $this->podio->request('/bulletin/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the bulletin with the given id
   *
   * @param $bulletin_id The id of the bulletin to retrieve
   *
   * @return A single bulletin object
   */
  public function get($bulletin_id) {
    if ($response = $this->podio->request('/bulletin/'.$bulletin_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
