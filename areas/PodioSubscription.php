<?php

/**
 * Subscriptions allows the user to be notified when an object is created, 
 * updated, delete, comments added to it or rated.
 */
class PodioSubscription {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Subscribes the user to the given object. Based on the object type, the 
   * user will receive notifications when actions are performed on the object. 
   */
  public function create($ref_type, $ref_id) {
    if ($response = $this->podio->post('/subscription/'.$ref_type.'/'.$ref_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Unsubscribe from getting notifications on actions on the given object.
   */
  public function delete($ref_type, $ref_id) {
    if ($response = $this->podio->delete('/subscription/'.$ref_type.'/'.$ref_id)) {
      return TRUE;
    }
  }
  
  /**
   * Stops the subscription with the given id
   */
  public function deleteByID($subscription_id) {
    if ($response = $this->podio->delete('/subscription/'.$subscription_id)) {
      return TRUE;
    }
  }
  
  /**
   * Returns the subscription with the given id
   */
  public function get($subscription_id) {
    if ($response = $this->podio->get('/subscription/'.$subscription_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the subscription for the given object
   */
  public function getByReference($ref_type, $ref_id) {
    if ($response = $this->podio->get('/subscription/'.$ref_type.'/'.$ref_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
