<?php
// PEAR Packages
require_once('Log.php');
require_once('HTTP/Request2.php');

/**
 * Singleton class for handling OAuth with the Podio API.
 */
class PodioOAuth {
  
  private static $instance;
  public $access_token;
  public $refresh_token;
  public $expires_in;
  protected $error_handler;

  private function __construct($error_handler = '') {
    $this->access_token = '';
    $this->refresh_token = '';
    
    if ($error_handler) {
      $this->error_handler = $error_handler;
    }
  }

  public static function instance($error_handler = '') {
    if (!self::$instance) {
      self::$instance = new PodioOAuth($error_handler);
    }
    return self::$instance;
  }

  public function getAccessToken($grant_type, $data) {
    $api = PodioBaseAPI::instance();
    $post_data = array();
    $post_data['client_id'] = $api->getClientId();
    $post_data['client_secret'] = $api->getClientSecret();
    
    switch ($grant_type) {
      case 'password':
        $post_data['grant_type'] = 'password';
        $post_data['username'] = $data['username'];
        $post_data['password'] = $data['password'];
        break;
      case 'refresh_token':
        $post_data['grant_type'] = 'refresh_token';
        $post_data['refresh_token'] = $data['refresh_token'];
        break;
      case 'authorization_code':
        $post_data['grant_type'] = 'authorization_code';
        $post_data['code'] = $data['code'];
        $post_data['redirect_uri'] = $data['redirect_uri'];
        break;
      default:
        break;
    }
    
    $request = new HTTP_Request2($api->getUrl() . '/oauth/token', HTTP_Request2::METHOD_POST, array(
      'ssl_verify_peer'   => false,
      'ssl_verify_host'   => false
    ));

    $request->setConfig('use_brackets', FALSE);
    $request->setConfig('follow_redirects', TRUE);
    $request->setHeader('User-Agent', 'Podio API Client/1.0');
    $request->setHeader('Accept', 'application/json');
    $request->setHeader('Accept-Encoding', 'gzip');

    $request->setHeader('Content-type', 'application/x-www-form-urlencoded');
    foreach ($post_data as $key => $value) {
      $request->addPostParameter($key, $value);
    }
    
    try {
        $response = $request->send();
        switch ($response->getStatus()) {
          case 200 : 
          case 201 : 
          case 204 : 
            $token = json_decode($response->getBody(), TRUE);
            if ($token) {
              $this->access_token = $token['access_token'];
              $this->refresh_token = $token['refresh_token'];
              $this->expires_in = $token['expires_in'];
              return TRUE;
            }
            break;
          case 401 : 
          case 400 : 
          case 403 : 
          case 404 : 
          case 410 : 
          case 500 : 
          case 503 : 
            $this->access_token = '';
            $this->refresh_token = '';
            $this->expires_in = '';
            if ($api->static('error')) {
              $api->log($request->getMethod() .' '. $response->getStatus().' '.$response->getReasonPhrase().' '.$request->getUrl(), PEAR_LOG_ERR);
              $api->log($response->getBody(), PEAR_LOG_ERR);
            }
            return FALSE;
            break;
          default : 
            $this->access_token = '';
            $this->refresh_token = '';
            $this->expires_in = '';
            break;
        }
    } catch (HTTP_Request2_Exception $e) {
      if ($api->static('error')) {
        $api->log($e->getMessage(), PEAR_LOG_ERR);
      }
    }
    return FALSE;
  }
  
  public function throwError($error) {
    if ($this->error_handler) {
      call_user_func($this->error_handler, $error);
    }
  }
}


