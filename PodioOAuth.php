<?php
// PEAR Packages
require_once('Log.php');
require_once('HTTP/Request2.php');

/**
 * Singleton class for handling OAuth with the Podio API.
 */
class PodioOAuth {
  
  private static $instance;
  /**
   * The current access token. Used on every API call
   */
  public $access_token;
  /**
   * The current refresh token. Used to retrieve a new access token
   */
  public $refresh_token;
  /**
   * The current expiration date for the access token
   */
  public $expires_in;
  /**
   * Error callback function.
   */
  protected $error_handler;

  private function __construct($error_handler = '') {
    $this->access_token = '';
    $this->refresh_token = '';
    
    if ($error_handler) {
      $this->error_handler = $error_handler;
    }
  }

  /**
   * Constructor for the singleton instance. Call with parameters first time, 
   * call without parameters subsequent times.
   *
   * @param $error_handler A function name to use as the error handler
   *
   * @return Singleton instance of PodioOAuth object
   */
  public static function instance($error_handler = '') {
    if (!self::$instance) {
      self::$instance = new PodioOAuth($error_handler);
    }
    return self::$instance;
  }

  /**
   * Get an access token or refresh an expired one.
   *
   * @param $grant_type The type of request. Can be:
   * - password: Use username and password to get access token
   * - refresh token: Refresh expired access token
   * - authorization_code: Use the authorization code obtained from step 
   *   one of the authorization
   * @param $data Request data. Varies by grant type. See OAuth specification
   *
   * @return TRUE if access token was retrieved
   */
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
          case 502 : 
          case 503 : 
          case 504 : 
            $this->access_token = '';
            $this->refresh_token = '';
            $this->expires_in = '';
            if ($api->getLogLevel('error')) {
              $api->log($request->getMethod() .' '. $response->getStatus().' '.$response->getReasonPhrase().' '.$request->getUrl(), PEAR_LOG_WARNING);
              $api->log($response->getBody(), PEAR_LOG_WARNING);
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
      if ($api->getLogLevel('error')) {
        $api->log($e->getMessage(), PEAR_LOG_WARNING);
      }
    }
    return FALSE;
  }
  
  /**
   * Throws an OAuth error. If an error callback is defined it will be called.
   */
  public function throwError($error) {
    if ($this->error_handler) {
      call_user_func($this->error_handler, $error);
    }
  }
}


