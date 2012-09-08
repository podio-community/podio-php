<?php

require_once 'PodioResponse.php';
require_once 'PodioOAuth.php';
require_once 'PodioError.php';
require_once 'PodioObject.php';
require_once 'models/PodioAction.php';
require_once 'models/PodioActivity.php';
require_once 'models/PodioAppMarketShare.php';
require_once 'models/PodioBatch.php';
require_once 'models/PodioComment.php';
require_once 'models/PodioEmbed.php';
require_once 'models/PodioFile.php';
require_once 'models/PodioHook.php';
require_once 'models/PodioImporter.php';
require_once 'models/PodioItem.php';
require_once 'models/PodioItemField.php';
require_once 'models/PodioItemRevision.php';
require_once 'models/PodioItemDiff.php';
require_once 'models/PodioLinkedAccountData.php';
require_once 'models/PodioRecurrence.php';
require_once 'models/PodioReminder.php';
require_once 'models/PodioSearchResult.php';
require_once 'models/PodioSpace.php';
require_once 'models/PodioStreamMute.php';
require_once 'models/PodioStreamObject.php';

require_once 'models/PodioByLine.php';
require_once 'models/PodioReference.php';
require_once 'models/PodioVia.php';

class Podio {
  public static $oauth, $debug;
  protected static $url, $client_id, $client_secret, $secret, $ch, $headers;

  const GET = 'GET';
  const POST = 'POST';
  const PUT = 'PUT';
  const DELETE = 'DELETE';

  public static function setup($client_id, $client_secret) {
    self::$client_id = $client_id;
    self::$client_secret = $client_secret;

    self::$url = 'https://api.podio.com:443';
    self::$debug = false;
    self::$ch = curl_init();
    self::$headers = array(
      'Accept' => 'application/json',
    );
    curl_setopt(self::$ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(self::$ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(self::$ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt(self::$ch, CURLOPT_USERAGENT, 'Podio PHP Client/2.0');
  }

  public static function authenticate($grant_type, $attributes) {
    $data = array();
    $data['client_id'] = self::$client_id;
    $data['client_secret'] = self::$client_secret;

    switch ($grant_type) {
      case 'password':
        $data['grant_type'] = 'password';
        $data['username'] = $attributes['username'];
        $data['password'] = $attributes['password'];
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
      default:
        break;
    }
    if ($response = self::request(self::POST, '/oauth/token', $data, array('oauth_request' => true))) {
      $body = $response->json_body();
      self::$oauth = new PodioOAuth($body['access_token'], $body['refresh_token'], $body['expires_in'], $body['ref']);
      return true;
    }
    return false;
  }

  public static function request($method, $url, $attributes = array(), $options = array()) {
    unset(self::$headers['Content-length']);
    $original_url = $url;
    switch ($method) {
      case self::GET:
        curl_setopt(self::$ch, CURLOPT_CUSTOMREQUEST, self::GET);
        self::$headers['Content-type'] = 'application/x-www-form-urlencoded';
        if ($attributes) {
          $query = self::encode_attributes($attributes);
          $url = $url.'?'.$query;
        }
        self::$headers['Content-length'] = "0";
        break;
      case self::DELETE:
        curl_setopt(self::$ch, CURLOPT_CUSTOMREQUEST, self::DELETE);
        self::$headers['Content-type'] = 'application/x-www-form-urlencoded';
        $query = self::encode_attributes($attributes);
        $url = $url.'?'.$query;
        self::$headers['Content-length'] = "0";
        break;
      case self::POST:
        curl_setopt(self::$ch, CURLOPT_CUSTOMREQUEST, self::POST);
        if (!empty($options['upload'])) {
          curl_setopt(self::$ch, CURLOPT_POST, TRUE);
          curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $attributes);
          self::$headers['Content-type'] = 'multipart/form-data';
        }
        elseif (empty($options['oauth_request'])) {
          // application/json
          $encoded_attributes = json_encode($attributes);
          curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $encoded_attributes);
          self::$headers['Content-type'] = 'application/json';
        }
        else {
          // x-www-form-urlencoded
          $encoded_attributes = self::encode_attributes($attributes);
          curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $encoded_attributes);
          self::$headers['Content-type'] = 'application/x-www-form-urlencoded';
        }
        break;
      case self::PUT:
        $encoded_attributes = json_encode($attributes);
        curl_setopt(self::$ch, CURLOPT_CUSTOMREQUEST, self::PUT);
        curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $encoded_attributes);
        self::$headers['Content-type'] = 'application/json';
        break;
    }

    // Add access token to request
    if (isset(self::$oauth) && !empty(self::$oauth->access_token) && !(isset($options['oauth_request']) && $options['oauth_request'] == true)) {
      $token = self::$oauth->access_token;
      self::$headers['Authorization'] = "OAuth2 {$token}";
    }
    else {
      unset(self::$headers['Authorization']);
    }

    // TODO: Debug, remove
    curl_setopt(self::$ch, CURLINFO_HEADER_OUT, true);

    curl_setopt(self::$ch, CURLOPT_HTTPHEADER, self::curl_headers());
    curl_setopt(self::$ch, CURLOPT_URL, self::$url.$url);

    $response = new PodioResponse();
    $response->body = curl_exec(self::$ch);
    $response->status = curl_getinfo(self::$ch, CURLINFO_HTTP_CODE);

    if (self::$debug) {
      error_log("[PODIO] {$response->status} {$method} {$url}");
      if (!empty($encoded_attributes)) {
        error_log("[PODIO] Request body: ".$encoded_attributes);
      }
      error_log("[PODIO] Reponse: {$response->body}");

      // TODO: Debug, remove
      error_log(curl_getinfo(self::$ch, CURLINFO_HEADER_OUT));
    }

    switch ($response->status) {
      case 200 :
      case 201 :
      case 204 :
        return $response;
        break;
      case 400 :
        // invalid_grant_error or bad_request_error
        $body = $response->json_body();
        if (strstr($body['error'], 'invalid_grant')) {
          // Reset access token & refresh_token
          self::$oauth = new PodioOAuth();
          throw new PodioInvalidGrantError($response->body, $response->status, $url);
          break;
        }
        else {
          throw new PodioBadRequestError($response->body, $response->status, $url);
        }
        break;
      case 401 :
        $body = $response->json_body();
        if (strstr($body['error_description'], 'expired_token') || strstr($body['error'], 'invalid_token')) {
          if (self::$oauth->refresh_token) {
            // Access token is expired. Try to refresh it.
            if (self::authenticate('refresh_token', array('refresh_token' => self::$oauth->refresh_token))) {
              // Try the original request again.
              return self::request($method, $original_url, $attributes);
            }
            else {
              self::$oauth = new PodioOAuth();
              throw new PodioAuthorizationError($response->body, $response->status, $url);
            }
          }
          else {
            // We have tried in vain to get a new access token. Log the user out.
            self::$oauth = new PodioOAuth();
            throw new PodioAuthorizationError($response->body, $response->status, $url);
          }
        }
        elseif (strstr($body['error'], 'invalid_request')) {
          // Access token is invalid. Log the user out and try again.
          self::$oauth = new PodioOAuth();
          throw new PodioAuthorizationError($response->body, $response->status, $url);
        }
        break;
      case 403 :
        throw new PodioAuthorizationError($response->body, $response->status, $url);
        break;
      case 404 :
        throw new PodioNotFoundError($response->body, $response->status, $url);
        break;
      case 409 :
        throw new PodioConflictError($response->body, $response->status, $url);
        break;
      case 410 :
        throw new PodioGoneError($response->body, $response->status, $url);
        break;
      case 420 :
        throw new PodioRateLimitError($response->body, $response->status, $url);
        break;
      case 500 :
        throw new PodioServerError($response->body, $response->status, $url);
        break;
      case 502 :
      case 503 :
      case 504 :
        throw new PodioUnavailableError($response->body, $response->status, $url);
        break;
      default :
        throw new PodioError($response->body, $response->status, $url);
        break;
    }
    return false;
  }

  public static function get($url, $attributes = array()) {
    return self::request(Podio::GET, $url, $attributes);
  }
  public static function post($url, $attributes = array(), $options = array()) {
    return self::request(Podio::POST, $url, $attributes, $options);
  }
  public static function put($url, $attributes = array()) {
    return self::request(Podio::PUT, $url, $attributes);
  }
  public static function delete($url, $attributes = array()) {
    return self::request(Podio::DELETE, $url, $attributes);
  }

  public static function curl_headers() {
    $headers = array();
    foreach (self::$headers as $header => $value) {
      $headers[] = "{$header}: {$value}";
    }
    return $headers;
  }
  public static function encode_attributes($attributes) {
    $return = array();
    foreach ($attributes as $key => $value) {
      $return[] = urlencode($key).'='.urlencode($value);
    }
    return join('&', $return);
  }
}
