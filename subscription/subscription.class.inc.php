<?php

/**
 * Subscriptions allows the user to be notified when an object is created, 
 * updated, delete, comments added to it or rated.
 */
class PodioSubscriptionAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Subscribes the user to the given object. Based on the object type, the 
   * user will receive notifications when actions are performed on the object. 
   *
   * @param $ref_type Either "app", "item", "status" or "space"
   * @param $ref_id The matching id (app id, item id or status id)
   */
  public function create($ref_type, $ref_id) {
    if ($response = $this->podio->request('/subscription/'.$ref_type.'/'.$ref_id, array(), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Unsubscribe from getting notifications on actions on the given object.
   *
   * @param $ref_type Either "app", "item", "status" or "space"
   * @param $ref_id The matching id (app id, item id or status id)
   */
  public function delete($ref_type, $ref_id) {
    if ($response = $this->podio->request('/subscription/'.$ref_type.'/'.$ref_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Stops the subscription with the given id
   *
   * @param $subscription_id The id of the subscription to stop
   */
  public function deleteByID($subscription_id) {
    if ($response = $this->podio->request('/subscription/'.$subscription_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Returns the subscription with the given id
   *
   * @param $subscription_id The id of the subscription to retrieve
   *
   * @return Subscription object
   */
  public function get($subscription_id) {
    $response = $this->podio->request('/subscription/'.$subscription_id);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the subscription for the given object
   *
   * @param $ref_type The type of reference
   * @param $ref_id The id of the reference
   *
   * @return Subscription object
   */
  public function getByReference($ref_type, $ref_id) {
    $response = $this->podio->request('/subscription/'.$ref_type.'/'.$ref_id);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

