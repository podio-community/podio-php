<?php

/**
 * Manages connections to outside services.
 */
class PodioConnection {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Returns the connection with the given id.
   */
  public function get($connection_id) {
    if ($response = $this->podio->get('/connection/'.$connection_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the connections that the user have.
   */
  public function getConnections($attributes = array()) {
    if ($response = $this->podio->get('/connection/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Creates a new connection to an external service. The data for the 
   * connection varies based on which type of connection is being added
   */
  public function create($attributes = array()) {
    if ($response = $this->podio->post('/connection/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Deletes the connection with the given id. This also deletes any imported 
   * contacts from the given connection.
   */
  public function delete($connection_id) {
    if ($response = $this->podio->delete('/connection/'.$connection_id)) {
      return TRUE;
    }
  }

  /**
   * Loads contacts from the given connection.
   */
  public function load($connection_id) {
    if ($response = $this->podio->post('/connection/'.$connection_id.'/load')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
