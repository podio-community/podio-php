<?php

/**
 * The stream API will supply the different streams. Currently supported is 
 * the global stream, the organization stream and the space stream. 
 */
class PodioStream {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Returns the global stream. The types of objects in the stream can 
   * be either "item", "status" or "task". See API documentation 
   * for details.
   */
  public function get($attributes = array()) {
    if ($response = $this->podio->get('/stream/v2/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns an organisation stream. The types of objects in the stream can 
   * be either "item", "status" or "task". See API documentation 
   * for details.
   */
  public function getOrg($org_id, $attributes = array()) {
    if ($response = $this->podio->get('/stream/org/'.$org_id.'/v2/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns a space stream. The types of objects in the stream can 
   * be either "item", "status" or "task". See API documentation 
   * for details.
   */
  public function getSpace($space_id, $attributes = array()) {
    if ($response = $this->podio->get('/stream/space/'.$space_id.'/v2/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the stream for the given app. This includes items 
   * from the app and tasks on the app.
   */
  public function getApp($app_id, $attributes = array()) {
    if ($response = $this->podio->get('/stream/app/'.$app_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the personal stream from personal spaces and sub-orgs.
   */
  public function getPersonal($attributes = array()) {
    if ($response = $this->podio->get('/stream/personal/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the stream for the given user. This returns all objects the 
   * active user has access to sorted by the given user last touched the object.
   */
  public function getUser($user_id, $attributes = array()) {
    if ($response = $this->podio->get('/stream/user/'.$user_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns an object (item or status) as a stream object. This is useful 
   * when a new status has been posted and should be rendered directly in the 
   * stream without reloading the entire stream.
   */
  public function getObject($ref_type, $ref_id) {
    if ($response = $this->podio->get('/stream/'.$ref_type.'/'.$ref_id.'/v2')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the apps and spaces the user has muted in the global stream.
   */
  public function getMutes() {
    if ($response = $this->podio->get('/stream/mute/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Mutes the app from the users global stream
   */
  public function muteApp($app_id) {
    if ($response = $this->podio->post('/stream/mute/app/'.$app_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Unmutes the app from the users global stream
   */
  public function unmuteApp($app_id) {
    if ($response = $this->podio->delete('/stream/mute/app/'.$app_id)) {
      return TRUE;
    }
  }
  
  /**
   * Mutes the space from the users global stream.
   */
  public function muteSpace($space_id) {
    if ($response = $this->podio->post('/stream/mute/space/'.$space_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Unmutes the space from the users global stream.
   */
  public function unmuteSpace($space_id) {
    if ($response = $this->podio->delete('/stream/mute/space/'.$space_id)) {
      return TRUE;
    }
  }
}
