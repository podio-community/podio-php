<?php

/**
 * Singleton class for handling OAuth with the Podio API.
 */
class PodioOAuth {
  
  private static $instance;
  public $access_token;
  public $refresh_token;
  protected $client_id;
  protected $client_secret;
  protected $error_handler;

  private function __construct($client_id, $client_secret, $error_handler = '') {
    $this->access_token = '';
    $this->refresh_token = '';
    $this->client_id = $client_id;
    $this->client_secret = $client_secret;
    
    if ($error_handler) {
      $this->error_handler = $error_handler;
    }
  }

  public static function instance($client_id = '', $client_secret = '', $error_handler = '') {
    if (!self::$instance) {
      self::$instance = new PodioOAuth($client_id, $client_secret, $error_handler);
    }
    return self::$instance;
  }
  
  public function throw_error($error) {
    if ($this->error_handler) {
      call_user_func($this->error_handler, $error);
    }
  }
}


