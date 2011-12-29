<?php

/**
 * Status messages are small texts that the users wishes to share with the 
 * other users in a space. It can be anything from a note that the user will 
 * be in later today over links to interesting resources and information 
 * about what the user is working on a the moment.
 */
class PodioStatus {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Retrieves a status message by its id. The id of the status message is 
   * usually gotten from the stream.
   */
  public function get($status_id) {
    if ($response = $this->podio->get('/status/'.$status_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Retrieves the latest status message on a space from a user.
   */
  public function getLatest($user_id, $space_id) {
    if ($response = $this->podio->get('/status/user/'.$user_id.'/space/'.$space_id.'/latest/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Creates a new status message for a user on a specific space. A status 
   * update is simply a short text message that the user wishes to share with 
   * the rest of the space.
   */
  public function create($space_id, $attributes = array()) {
    if ($response = $this->podio->post('/status/space/'.$space_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * This will update an existing status message. This will normally only be 
   * used to correct spelling and grammatical mistakes.
   */
  public function update($status_id, $attributes = array()) {
    if ($response = $this->podio->put('/status/'.$status_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * This is used to delete a status message. This is normally only done if 
   * the user regrets his status update. After deletion the status message 
   * will no longer be viewable by anyone.
   */
  public function delete($status_id) {
    if ($response = $this->podio->delete('/status/'.$status_id)) {
      return TRUE;
    }
  }
}

