<?php

/**
 * Actions are activities performed by users that does not pertain to 
 * one of the core objects in Podio, items, tasks and statuses. Instead 
 * an action is a point-in-time activity performed by a user. 
 */
class PodioAction {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Returns the action with the given id
   */
  public function get($action_id) {
    if ($response = $this->podio->get('/action/'.$action_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
