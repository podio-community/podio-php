<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;

class Podio
{
  public static $oauth, $debug, $logger, $session_manager, $last_response, $auth_type;
  /** @var \GuzzleHttp\Client */
  public static $http_client;
  protected static $url, $client_id, $client_secret, $secret, $headers;
  /** @var \Psr\Http\Message\ResponseInterface */
  private static $last_http_response;

  const VERSION = '6.0.0';

  const GET = 'GET';
  const POST = 'POST';
  const PUT = 'PUT';
  const DELETE = 'DELETE';

  public static function setup($client_id, $client_secret, $options = array('session_manager' => null, 'curl_options' => array())) {
    // Setup client info
    self::$client_id = $client_id;
    self::$client_secret = $client_secret;

    self::$url = empty($options['api_url']) ? 'https://api.podio.com:443' : $options['api_url'];
    self::$debug = self::$debug ? self::$debug : false;
    $client_config = [
      'base_uri' => self::$url,

      RequestOptions::HEADERS => [
        'Accept' => 'application/json',
        'User-Agent' => 'Podio PHP Client/' . self::VERSION . '-guzzle'
      ]
    ];
    if ($options && !empty($options['curl_options'])) {
      $client_config['curl'] = $options['curl_options'];
    }
    if (class_exists('\\Composer\\CaBundle\\CaBundle')) {
      /** @noinspection PhpFullyQualifiedNameUsageInspection */
      $client_config[RequestOptions::VERIFY] = \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath();
    }
    self::$http_client = new Client($client_config);

    self::$session_manager = null;
    if ($options && !empty($options['session_manager'])) {
      if (is_string($options['session_manager']) && class_exists($options['session_manager'])) {
        self::$session_manager = new $options['session_manager'];
      } else if (is_object($options['session_manager']) && method_exists($options['session_manager'], 'get') && method_exists($options['session_manager'], 'set')) {
        self::$session_manager = $options['session_manager'];
      }
      if (self::$session_manager) {
        self::$oauth = self::$session_manager->get();
      }
    }

    // Register shutdown function for debugging and session management
    register_shutdown_function('Podio::shutdown');
  }

  public static function authenticate_with_app($app_id, $app_token) {
    return static::authenticate('app', array('app_id' => $app_id, 'app_token' => $app_token));
  }

  public static function authenticate_with_password($username, $password) {
    return static::authenticate('password', array('username' => $username, 'password' => $password));
  }

  public static function authenticate_with_authorization_code($authorization_code, $redirect_uri) {
    return static::authenticate('authorization_code', array('code' => $authorization_code, 'redirect_uri' => $redirect_uri));
  }

  public static function refresh_access_token() {
    return static::authenticate('refresh_token', array('refresh_token' => self::$oauth->refresh_token));
  }

  public static function authenticate($grant_type, $attributes) {
    $data = array();
    $auth_type = array('type' => $grant_type);

    switch ($grant_type) {
      case 'password':
        $data['grant_type'] = 'password';
        $data['username'] = $attributes['username'];
        $data['password'] = $attributes['password'];

        $auth_type['identifier'] = $attributes['username'];
        break;
      case 'refresh_token':
        $data['grant_type'] = 'refresh_token';
        $data['refresh_token'] = $attributes['refresh_token'];
        break;
      case 'authorization_code':
        $data['grant_type'] = 'authorization_code';
        $data['code'] = $attributes['code'];
        $data['redirect_uri'] = $attributes['redirect_uri'];
        break;
      case 'app':
        $data['grant_type'] = 'app';
        $data['app_id'] = $attributes['app_id'];
        $data['app_token'] = $attributes['app_token'];

        $auth_type['identifier'] = $attributes['app_id'];
        break;
      default:
        break;
    }

    $request_data = array_merge($data, array('client_id' => self::$client_id, 'client_secret' => self::$client_secret));
    if ($response = static::request(self::POST, '/oauth/token', $request_data, array('oauth_request' => true))) {
      $body = $response->json_body();
      self::$oauth = new PodioOAuth($body['access_token'], $body['refresh_token'], $body['expires_in'], $body['ref'], $body['scope']);

      // Don't touch auth_type if we are refreshing automatically as it'll be reset to null
      if ($grant_type !== 'refresh_token') {
        self::$auth_type = $auth_type;
      }

      if (self::$session_manager) {
        self::$session_manager->set(self::$oauth, self::$auth_type);
      }

      return true;
    }
    return false;
  }

  public static function clear_authentication() {
    self::$oauth = new PodioOAuth();

    if (self::$session_manager) {
      self::$session_manager->set(self::$oauth, self::$auth_type);
    }
  }

  public static function authorize_url($redirect_uri, $scope) {
    $parsed_url = parse_url(self::$url);
    $host = str_replace('api.', '', $parsed_url['host']);
    return 'https://' . $host . '/oauth/authorize?response_type=code&client_id=' . self::$client_id . '&redirect_uri=' . rawurlencode($redirect_uri) . '&scope=' . rawurlencode($scope);
  }

  public static function is_authenticated() {
    return self::$oauth && self::$oauth->access_token;
  }

  /**
   * @throws PodioBadRequestError
   * @throws PodioConflictError
   * @throws PodioRateLimitError
   * @throws PodioUnavailableError
   * @throws PodioGoneError
   * @throws PodioDataIntegrityError
   * @throws PodioForbiddenError
   * @throws PodioNotFoundError
   * @throws PodioError
   * @throws PodioInvalidGrantError
   * @throws PodioAuthorizationError
   * @throws PodioConnectionError
   * @throws PodioServerError
   * @throws Exception when client is not setup
   */
  public static function request($method, $url, $attributes = array(), $options = array()) {
    if (!self::$http_client) {
      throw new Exception('Client has not been setup with client id and client secret.');
    }

    $original_url = $url;
    $encoded_attributes = null;

    if (is_object($attributes) && substr(get_class($attributes), 0, 5) == 'Podio') {
      $attributes = $attributes->as_json(false);
    }

    if (!is_array($attributes) && !is_object($attributes)) {
      throw new PodioDataIntegrityError('Attributes must be an array');
    }

    $request = new Request($method, $url);
    switch ($method) {
      case self::DELETE:
      case self::GET:
        $request = $request->withHeader('Content-type', 'application/x-www-form-urlencoded');
        $request = $request->withHeader('Content-length', '0');

        $separator = strpos($url, '?') ? '&' : '?';
        if ($attributes) {
          $query = static::encode_attributes($attributes);
          $request = $request->withUri(new Uri($url . $separator . $query));
        }
        break;
      case self::POST:
        if (!empty($options['upload'])) {
          $request = $request->withBody(new MultipartStream([
            [
              'name' => 'source',
              'contents' => fopen($options['upload'], 'r'),
              'filename' => $options['upload']
            ], [
              'name' => 'filename',
              'contents' => $attributes['filename']
            ]
          ]));
        } elseif (empty($options['oauth_request'])) {
          // application/json
          $encoded_attributes = json_encode($attributes);
          $request = $request->withBody(GuzzleHttp\Psr7\Utils::streamFor($encoded_attributes));
          $request = $request->withHeader('Content-type', 'application/json');
        } else {
          // x-www-form-urlencoded
          $encoded_attributes = static::encode_attributes($attributes);
          $request = $request->withBody(GuzzleHttp\Psr7\Utils::streamFor($encoded_attributes));
          $request = $request->withHeader('Content-type', 'application/x-www-form-urlencoded');
        }
        break;
      case self::PUT:
        $encoded_attributes = json_encode($attributes);
        $request = $request->withBody(GuzzleHttp\Psr7\Utils::streamFor($encoded_attributes));
        $request = $request->withHeader('Content-type', 'application/json');
        break;
    }

    // Add access token to request
    if (isset(self::$oauth) && !empty(self::$oauth->access_token) && !(isset($options['oauth_request']) && $options['oauth_request'] == true)) {
      $token = self::$oauth->access_token;
      $request = $request->withHeader('Authorization', "OAuth2 {$token}");
    }

    // File downloads can be of any type
    if (!empty($options['file_download'])) {
      $request = $request->withHeader('Accept', '*/*');
    }

    $response = new PodioResponse();

    try {
      $transferTime = 0;
      /** \Psr\Http\Message\ResponseInterface */
      $http_response = self::$http_client->send($request, [
        RequestOptions::ON_STATS => function (TransferStats $stats) use (&$transferTime) {
          $transferTime = $stats->getTransferTime();
        }
      ]);
      $response->status = $http_response->getStatusCode();
      $response->headers = array_map(function ($values) {
        return implode(', ', $values);
      }, $http_response->getHeaders());
      self::$last_http_response = $http_response;
      if (!isset($options['return_raw_as_resource_only']) || $options['return_raw_as_resource_only'] != true) {
        $response->body = $http_response->getBody()->getContents();
      }
      self::$last_response = $response;

    } catch (RequestException $requestException) {
      throw new PodioConnectionError('Connection to Podio API failed: [' . get_class($requestException) . '] ' . $requestException->getMessage(), $requestException->getCode());
    } catch (GuzzleException $e) { // this generally should not happen as RequestOptions::HTTP_ERRORS is set to `false`
      throw new PodioConnectionError('Connection to Podio API failed: [' . get_class($e) . '] ' . $e->getMessage(), $e->getCode());
    }

    if (!isset($options['oauth_request'])) {
      static::log_request($method, $url, $encoded_attributes, $response, $transferTime);
    }

    switch ($response->status) {
      case 200 :
      case 201 :
      case 204 :
        if (isset($options['return_raw_as_resource_only']) && $options['return_raw_as_resource_only'] === true) {
          return $http_response->getBody();
        }
        return $response;
      case 400 :
        // invalid_grant_error or bad_request_error
        $body = $response->json_body();
        if (strstr($body['error'], 'invalid_grant')) {
          // Reset access token & refresh_token
          static::clear_authentication();
          throw new PodioInvalidGrantError($response->body, $response->status, $url);
        } else {
          throw new PodioBadRequestError($response->body, $response->status, $url);
        }
      case 401 :
        $body = $response->json_body();
        if (strstr($body['error_description'], 'expired_token') || strstr($body['error'], 'invalid_token')) {
          if (self::$oauth->refresh_token) {
            // Access token is expired. Try to refresh it.
            if (static::refresh_access_token()) {
              // Try the original request again.
              return static::request($method, $original_url, $attributes);
            } else {
              static::clear_authentication();
              throw new PodioAuthorizationError($response->body, $response->status, $url);
            }
          } else {
            // We have tried in vain to get a new access token. Log the user out.
            static::clear_authentication();
            throw new PodioAuthorizationError($response->body, $response->status, $url);
          }
        } elseif (strstr($body['error'], 'invalid_request') || strstr($body['error'], 'unauthorized')) {
          // Access token is invalid.
          static::clear_authentication();
          throw new PodioAuthorizationError($response->body, $response->status, $url);
        }
        break;
      case 403 :
        throw new PodioForbiddenError($response->body, $response->status, $url);
      case 404 :
        throw new PodioNotFoundError($response->body, $response->status, $url);
      case 409 :
        throw new PodioConflictError($response->body, $response->status, $url);
      case 410 :
        throw new PodioGoneError($response->body, $response->status, $url);
      case 420 :
        throw new PodioRateLimitError($response->body, $response->status, $url);
      case 500 :
        throw new PodioServerError($response->body, $response->status, $url);
      case 502 :
      case 503 :
      case 504 :
        throw new PodioUnavailableError($response->body, $response->status, $url);
      default :
        throw new PodioError($response->body, $response->status, $url);
    }
    return false;
  }

  public static function get($url, $attributes = array(), $options = array()) {
    return static::request(Podio::GET, $url, $attributes, $options);
  }
  public static function post($url, $attributes = array(), $options = array()) {
    return static::request(Podio::POST, $url, $attributes, $options);
  }
  public static function put($url, $attributes = array()) {
    return static::request(Podio::PUT, $url, $attributes);
  }
  public static function delete($url, $attributes = array()) {
    return static::request(Podio::DELETE, $url, $attributes);
  }

  public static function encode_attributes($attributes) {
    $return = array();
    foreach ($attributes as $key => $value) {
      $return[] = urlencode($key) . '=' . urlencode($value);
    }
    return join('&', $return);
  }
  public static function url_with_options($url, $options) {
    $parameters = array();

    if (isset($options['silent']) && $options['silent']) {
      $parameters[] = 'silent=1';
    }

    if (isset($options['hook']) && !$options['hook']) {
      $parameters[] = 'hook=false';
    }

    if (!empty($options['fields'])) {
      $parameters[] = 'fields=' . $options['fields'];
    }

    return $parameters ? $url . '?' . join('&', $parameters) : $url;
  }

  public static function rate_limit_remaining() {
    if (isset(self::$last_http_response)) {
      return implode(self::$last_http_response->getHeader('x-rate-limit-remaining'));
    }
    return '-1';
  }

  public static function rate_limit() {
    if (isset(self::$last_http_response)) {
      return implode(self::$last_http_response->getHeader('x-rate-limit-limit'));
    }
    return '-1';
  }

  /**
   * Set debug config
   *
   * @param $toggle boolean True to enable debugging. False to disable
   * @param $output string Output mode. Can be "stdout" or "file". Default is "stdout"
   */
  public static function set_debug($toggle, $output = "stdout") {
    if ($toggle) {
      self::$debug = $output;
    } else {
      self::$debug = false;
    }
  }

  public static function log_request($method, $url, $encoded_attributes, $response, $transferTime) {
    if (self::$debug) {
      $timestamp = gmdate('Y-m-d H:i:s');
      $text = "{$timestamp} {$response->status} {$method} {$url}\n";
      if (!empty($encoded_attributes)) {
        $text .= "{$timestamp} Request body: " . $encoded_attributes . "\n";
      }
      $text .= "{$timestamp} Reponse: {$response->body}\n\n";

      if (self::$debug === 'file') {
        if (!self::$logger) {
          self::$logger = new PodioLogger();
        }
        self::$logger->log($text);
      } elseif (self::$debug === 'stdout' && php_sapi_name() === 'cli') {
        print $text;
      } elseif (self::$debug === 'stdout' && php_sapi_name() === 'cli') {
        require_once 'vendor/kint/Kint.class.php';
        Kint::dump("{$method} {$url}", $encoded_attributes, $response);
      }

      self::$logger->call_log[] = $transferTime;
    }

  }

  public static function shutdown() {
    // Write any new access and refresh tokens to session.
    if (self::$session_manager) {
      self::$session_manager->set(self::$oauth, self::$auth_type);
    }

    // Log api call times if debugging
    if (self::$debug && self::$logger) {
      $timestamp = gmdate('Y-m-d H:i:s');
      $count = sizeof(self::$logger->call_log);
      $duration = 0;
      if (self::$logger->call_log) {
        foreach (self::$logger->call_log as $val) {
          $duration += $val;
        }
      }

      $text = "\n{$timestamp} Performed {$count} request(s) in {$duration} seconds\n";
      if (self::$debug === 'file') {
        if (!self::$logger) {
          self::$logger = new PodioLogger();
        }
        self::$logger->log($text);
      } elseif (self::$debug === 'stdout' && php_sapi_name() === 'cli') {
        print $text;
      }
    }
  }
}
