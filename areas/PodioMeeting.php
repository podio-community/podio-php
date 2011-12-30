<?php

/**
 * Meetings are used to schedule appointments, events and, yes, meetings 
 * with other Podio users or space contacts.
 */
class PodioMeeting {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Returns the meeting with the given id.
   */
  public function get($meeting_id) {
    if ($response = $this->podio->get('/meeting/'.$meeting_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns a list of all meetings for this user.
   */
  public function getMeetings($attributes = array()) {
    if ($response = $this->podio->get('/meeting/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns a list of meetings on the given reference.
   */
  public function getByRef($ref_type, $ref_id) {
    if ($response = $this->podio->get('/meeting/'.$ref_type.'/'.$ref_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Creates a new meeting with no reference to other objects.
   */
  public function create($attributes = array()) {
    if ($response = $this->podio->post('/meeting/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Creates a new meeting with a reference to the given object. The valid 
   * types of objects are "item", "status", "app", "space" and "conversation".
   */
  public function createByRef($ref_type, $ref_id, $attributes = array()) {
    if ($response = $this->podio->post('/meeting/'.$ref_type.'/'.$ref_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the meeting with the given information.
   */
  public function update($meeting_id, $attributes = array()) {
    if ($response = $this->podio->put('/meeting/'.$meeting_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Starts a meeting and notifies participants that it is time to join the meeting.
   */
  public function start($meeting_id) {
    if ($response = $this->podio->post('/meeting/'.$meeting_id.'/start')) {
      return TRUE;
    }
  }

  /**
   * Ends the meeting, making any post-meeting actions appropriate for the meeting.
   */
  public function stop($meeting_id) {
    if ($response = $this->podio->post('/meeting/'.$meeting_id.'/stop')) {
      return TRUE;
    }
  }

  /**
   * Cancels the meeting, notifying participants of this.
   */
  public function cancel($meeting_id) {
    if ($response = $this->podio->post('/meeting/'.$meeting_id.'/cancel')) {
      return TRUE;
    }
  }

  /**
   * Deletes the meeting with the given id
   */
  public function delete($meeting_id) {
    if ($response = $this->podio->delete('/meeting/'.$meeting_id)) {
      return TRUE;
    }
  }
}
