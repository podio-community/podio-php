<?php

/**
 * Hooks are webhooks that can call external sites when certain actions occur 
 * in Podio. Hooks needs to be verified before they become active.
 */
class PodioHook {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Returns the hooks on an object.
   */
  public function get($ref_type, $ref_id) {
    if ($response = $this->podio->get('/hook/'.$ref_type.'/'.$ref_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Create a new hook on the given object.
   */
  public function create($ref_type, $ref_id, $attributes = array()) {
    if ($response = $this->podio->post('/hook/'.$ref_type.'/'.$ref_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Request the hook to be validated.
   */
  public function verify($hook_id) {
    if ($response = $this->podio->post('/hook/'.$hook_id.'/verify/request')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Request the hook to be validated.
   */
  public function validate($hook_id, $attributes = array()) {
    if ($response = $this->podio->post('/hook/'.$hook_id.'/verify/validate', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the hook with the given id.
   */
  public function delete($hook_id) {
    if ($response = $this->podio->delete('/hook/'.$hook_id)) {
      return TRUE;
    }
  }
  
}

