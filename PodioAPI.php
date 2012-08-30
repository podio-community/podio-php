<?php

require_once 'PodioObject.php';
require_once 'models/PodioHook.php';
require_once 'models/PodioByLine.php';

require_once 'PodioResponse.php';
require_once 'PodioOAuth.php';
require_once 'PodioError.php';
require_once 'areas/PodioAction.php';
require_once 'areas/PodioApp.php';
require_once 'areas/PodioAppStore.php';
require_once 'areas/PodioBulletin.php';
require_once 'areas/PodioCalendar.php';
require_once 'areas/PodioComment.php';
require_once 'areas/PodioConnection.php';
require_once 'areas/PodioContact.php';
require_once 'areas/PodioConversation.php';
require_once 'areas/PodioEmbed.php';
require_once 'areas/PodioFile.php';
require_once 'areas/PodioFilter.php';
require_once 'areas/PodioForm.php';
// require_once 'areas/PodioHook.php';
require_once 'areas/PodioImporter.php';
require_once 'areas/PodioIntegration.php';
require_once 'areas/PodioItem.php';
require_once 'areas/PodioMeeting.php';
require_once 'areas/PodioMobile.php';
require_once 'areas/PodioNews.php';
require_once 'areas/PodioNotification.php';
require_once 'areas/PodioOrganization.php';
require_once 'areas/PodioQuestion.php';
require_once 'areas/PodioRating.php';
require_once 'areas/PodioSearch.php';
require_once 'areas/PodioSpace.php';
require_once 'areas/PodioSpaceMember.php';
require_once 'areas/PodioStatus.php';
require_once 'areas/PodioStream.php';
require_once 'areas/PodioSubscription.php';
require_once 'areas/PodioTag.php';
require_once 'areas/PodioTask.php';
require_once 'areas/PodioUser.php';
require_once 'areas/PodioWidget.php';

class Podio {
  protected $url, $client_id, $secret, $ch, $headers;
  public $oauth, $debug;
  private static $instance;

  const GET = 'GET';
  const POST = 'POST';
  const PUT = 'PUT';
  const DELETE = 'DELETE';

  /**
   * Constructor for the singleton instance. Call with parameters first time,
   * call without parameters subsequent times.
   *
   * @param $client_id OAuth Client id
   * @param $client_secret OAuth client secret
   * @param $url Optional. URL for the API server
   *
   * @return Singleton instance of Podio object
   */
  public static function instance($client_id = '', $client_secret = '', $url = 'https://api.podio.com:443') {
    if (!self::$instance) {
      self::$instance = new Podio;
      self::$instance->url = $url;
      self::$instance->client_id = $client_id;
      self::$instance->client_secret = $client_secret;
      self::$instance->debug = false;
      self::$instance->ch = curl_init();
      self::$instance->headers = array(
        'Accept' => 'application/json',
      );
      // curl_setopt(self::$instance->ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt(self::$instance->ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt(self::$instance->ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt(self::$instance->ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt(self::$instance->ch, CURLOPT_USERAGENT, 'Podio PHP Client/2.0');

      self::$instance->action = new PodioAction();
      self::$instance->app = new PodioApp();
      self::$instance->appStore = new PodioAppStore();
      self::$instance->bulletin = new PodioBulletin();
      self::$instance->calendar = new PodioCalendar();
      self::$instance->comment = new PodioComment();
      self::$instance->connection = new PodioConnection();
      self::$instance->contact = new PodioContact();
      self::$instance->conversation = new PodioConversation();
      self::$instance->embed = new PodioEmbed();
      self::$instance->file = new PodioFile();
      self::$instance->filter = new PodioFilter();
      self::$instance->form = new PodioForm();
      self::$instance->hook = new PodioHook();
      self::$instance->importer = new PodioImporter();
      self::$instance->integration = new PodioIntegration();
      self::$instance->item = new PodioItem();
      self::$instance->meeting = new PodioMeeting();
      self::$instance->mobile = new PodioMobile();
      self::$instance->news = new PodioNews();
      self::$instance->notification = new PodioNotification();
      self::$instance->organization = new PodioOrganization();
      self::$instance->question = new PodioQuestion();
      self::$instance->rating = new PodioRating();
      self::$instance->search = new PodioSearch();
      self::$instance->space = new PodioSpace();
      self::$instance->spaceMember = new PodioSpaceMember();
      self::$instance->status = new PodioStatus();
      self::$instance->stream = new PodioStream();
      self::$instance->subscription = new PodioSubscription();
      self::$instance->tag = new PodioTag();
      self::$instance->task = new PodioTask();
      self::$instance->user = new PodioUser();
      self::$instance->widget = new PodioWidget();
    }
    return self::$instance;
  }

  public function authenticate($grant_type, $attributes) {
    $data = array();
    $data['client_id'] = $this->client_id;
    $data['client_secret'] = $this->client_secret;

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
    if ($response = $this->request(self::POST, '/oauth/token', $data, array('oauth_request' => true))) {
      $body = json_decode($response->getBody(), true);
      $this->oauth = new PodioOAuth($body['access_token'], $body['refresh_token'], $body['expires_in'], $body['ref']);
      return true;
    }
    return false;
  }

  public function request($method, $url, $attributes = array(), $options = array()) {
    unset($this->headers['Content-length']);
    $original_url = $url;
    switch ($method) {
      case self::GET:
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, self::GET);
        $this->headers['Content-type'] = 'application/x-www-form-urlencoded';
        $query = $this->encode_attributes($attributes);
        $url = $url.'?'.$query;
        $this->headers['Content-length'] = "0";
        break;
      case self::DELETE:
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, self::DELETE);
        $this->headers['Content-type'] = 'application/x-www-form-urlencoded';
        $query = $this->encode_attributes($attributes);
        $url = $url.'?'.$query;
        $this->headers['Content-length'] = "0";
        break;
      case self::POST:
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, self::POST);
        if (!empty($options['upload'])) {
          curl_setopt($this->ch, CURLOPT_POST, TRUE);
          curl_setopt($this->ch, CURLOPT_POSTFIELDS, $attributes);
          $this->headers['Content-type'] = 'multipart/form-data';
        }
        elseif (empty($options['oauth_request'])) {
          // application/json
          $encoded_attributes = json_encode($attributes);
          curl_setopt($this->ch, CURLOPT_POSTFIELDS, $encoded_attributes);
          $this->headers['Content-type'] = 'application/json';
        }
        else {
          // x-www-form-urlencoded
          $encoded_attributes = $this->encode_attributes($attributes);
          curl_setopt($this->ch, CURLOPT_POSTFIELDS, $encoded_attributes);
          $this->headers['Content-type'] = 'application/x-www-form-urlencoded';
        }
        break;
      case self::PUT:
        $encoded_attributes = json_encode($attributes);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, self::PUT);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $encoded_attributes);
        $this->headers['Content-type'] = 'application/json';
        break;
    }

    // Add access token to request
    if (isset($this->oauth) && !empty($this->oauth->access_token) && !(isset($options['oauth_request']) && $options['oauth_request'] == true)) {
      $this->headers['Authorization'] = "OAuth2 {$this->oauth->access_token}";
    }
    else {
      unset($this->headers['Authorization']);
    }

    // TODO: Debug, remove
    curl_setopt($this->ch, CURLINFO_HEADER_OUT, true);

    curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->curl_headers());
    curl_setopt($this->ch, CURLOPT_URL, $this->url.$url);

    $response = new PodioResponse();
    $response->body = curl_exec($this->ch);
    $response->status = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

    if ($this->debug) {
      error_log("[PODIO] {$response->status} {$method} {$url}");
      if (!empty($encoded_attributes)) {
        error_log("[PODIO] Request body: ".$encoded_attributes);
      }
      error_log("[PODIO] Reponse: {$response->body}");

      // TODO: Debug, remove
      error_log(curl_getinfo($this->ch, CURLINFO_HEADER_OUT));
    }

    switch ($response->getStatus()) {
      case 200 :
      case 201 :
      case 204 :
        return $response;
        break;
      case 400 :
        // invalid_grant_error or bad_request_error
        $body = json_decode($response->getBody(), TRUE);
        if (strstr($body['error'], 'invalid_grant')) {
          // Reset access token & refresh_token
          $this->oauth = new PodioOAuth();
          throw new PodioInvalidGrantError($response->getBody(), $response->getStatus(), $url);
          break;
        }
        else {
          throw new PodioBadRequestError($response->getBody(), $response->getStatus(), $url);
        }
        break;
      case 401 :
        $body = json_decode($response->getBody(), TRUE);
        if (strstr($body['error_description'], 'expired_token') || strstr($body['error'], 'invalid_token')) {
          if ($this->oauth->refresh_token) {
            // Access token is expired. Try to refresh it.
            if ($this->authenticate('refresh_token', array('refresh_token' => $this->oauth->refresh_token))) {
              // Try the original request again.
              return $this->request($method, $original_url, $attributes);
            }
            else {
              $this->oauth = new PodioOAuth();
              throw new PodioAuthorizationError($response->getBody(), $response->getStatus(), $url);
            }
          }
          else {
            // We have tried in vain to get a new access token. Log the user out.
            $this->oauth = new PodioOAuth();
            throw new PodioAuthorizationError($response->getBody(), $response->getStatus(), $url);
          }
        }
        elseif (strstr($body['error'], 'invalid_request')) {
          // Access token is invalid. Log the user out and try again.
          $this->oauth = new PodioOAuth();
          throw new PodioAuthorizationError($response->getBody(), $response->getStatus(), $url);
        }
        break;
      case 403 :
        throw new PodioAuthorizationError($response->getBody(), $response->getStatus(), $url);
        break;
      case 404 :
        throw new PodioNotFoundError($response->getBody(), $response->getStatus(), $url);
        break;
      case 409 :
        throw new PodioConflictError($response->getBody(), $response->getStatus(), $url);
        break;
      case 410 :
        throw new PodioGoneError($response->getBody(), $response->getStatus(), $url);
        break;
      case 420 :
        throw new PodioRateLimitError($response->getBody(), $response->getStatus(), $url);
        break;
      case 500 :
        throw new PodioServerError($response->getBody(), $response->getStatus(), $url);
        break;
      case 502 :
      case 503 :
      case 504 :
        throw new PodioUnavailableError($response->getBody(), $response->getStatus(), $url);
        break;
      default :
        throw new PodioError($response->getBody(), $response->getStatus(), $url);
        break;
    }
    return false;
  }

  public function get($url, $attributes = array()) {
    return $this->request(Podio::GET, $url, $attributes);
  }
  public function post($url, $attributes = array(), $options = array()) {
    return $this->request(Podio::POST, $url, $attributes, $options);
  }
  public function put($url, $attributes = array()) {
    return $this->request(Podio::PUT, $url, $attributes);
  }
  public function delete($url, $attributes = array()) {
    return $this->request(Podio::DELETE, $url, $attributes);
  }

  public function curl_headers() {
    $headers = array();
    foreach ($this->headers as $header => $value) {
      $headers[] = "{$header}: {$value}";
    }
    return $headers;
  }
  public function encode_attributes($attributes) {
    $return = array();
    foreach ($attributes as $key => $value) {
      $return[] = urlencode($key).'='.urlencode($value);
    }
    return join('&', $return);
  }
}
