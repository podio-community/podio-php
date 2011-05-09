<?php

/**
 * Hooks are webhooks that can call external sites when certain actions occur 
 * in Podio. Hooks needs to be verified before they become active.
 */
class PodioHookAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Returns the hooks on an object.
   *
   * @param $ref_type The type of object
   * @param $ref_id The id of the reference
   */
  public function get($ref_type, $ref_id) {
    if ($response = $this->podio->request('/hook/'.$ref_type.'/'.$ref_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Create a new hook on the given object.
   *
   * @param $ref_type The type of object to hook into
   * @param $ref_id The id of the reference
   * @param $url The url of endpoint
   * @param $type The type of events to listen to, see the area for options
   */
  public function create($ref_type, $ref_id, $url, $type) {
    if ($response = $this->podio->request('/hook/'.$ref_type.'/'.$ref_id.'/', array('url' => $url, 'type' => $type), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Request the hook to be validated.
   *
   * @param $hook_id The id of the hook to act on
   */
  public function verify($hook_id) {
    if ($response = $this->podio->request('/hook/'.$hook_id.'/verify/request', array(), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Request the hook to be validated.
   *
   * @param $hook_id The id of the hook to act on
   * @param $code The verification code
   */
  public function validate($hook_id, $code) {
    if ($response = $this->podio->request('/hook/'.$hook_id.'/verify/validate', array(), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the hook with the given id.
   *
   * @param $hook_id The id of the hook to act on
   */
  public function delete($hook_id) {
    if ($response = $this->podio->request('/hook/'.$hook_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
}

