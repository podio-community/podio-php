<?php

// PEAR Packages
require_once('Log.php');
require_once('HTTP/Request2.php');

// Internal
require_once('PodioOAuth.php');
require_once('notification/notification.class.inc.php');
require_once('status/status.class.inc.php');
require_once('conversation/conversation.class.inc.php');
require_once('task/task.class.inc.php');
require_once('app/app.class.inc.php');
require_once('item/item.class.inc.php');
require_once('comment/comment.class.inc.php');
require_once('user/user.class.inc.php');
require_once('rating/rating.class.inc.php');
require_once('space/space.class.inc.php');
require_once('org/org.class.inc.php');
require_once('contact/contact.class.inc.php');
require_once('subscription/subscription.class.inc.php');
require_once('file/file.class.inc.php');
require_once('calendar/calendar.class.inc.php');
require_once('search/search.class.inc.php');
require_once('stream/stream.class.inc.php');
require_once('app_store/app_store.class.inc.php');
require_once('tag/tag.class.inc.php');
require_once('bulletin/bulletin.class.inc.php');
require_once('widget/widget.class.inc.php');
require_once('filter/filter.class.inc.php');
require_once('form/form.class.inc.php');

/**
 * Primary Podio API implementation class. This is merely a container for 
 * the specific API areas.
 */
class PodioAPI {

  /**
   * Reference to PodioBaseAPI instance
   */
  public $api;
  /**
   * Reference to PodioNotificationAPI instance
   */
  public $notification;
  /**
   * Reference to PodioConversationAPI instance
   */
  public $conversation;
  /**
   * Reference to PodioStatusAPI instance
   */
  public $status;
  /**
   * Reference to PodioTaskAPI instance
   */
  public $task;
  /**
   * Reference to PodioAppAPI instance
   */
  public $app;
  /**
   * Reference to PodioItemAPI instance
   */
  public $item;
  /**
   * Reference to PodioUserAPI instance
   */
  public $user;
  /**
   * Reference to PodioCommentAPI instance
   */
  public $comment;
  /**
   * Reference to PodioRatingAPI instance
   */
  public $rating;
  /**
   * Reference to PodioSpaceAPI instance
   */
  public $space;
  /**
   * Reference to PodioOrgAPI instance
   */
  public $org;
  /**
   * Reference to PodioContactAPI instance
   */
  public $contact;
  /**
   * Reference to PodioSubscriptionAPI instance
   */
  public $subscription;
  /**
   * Reference to PodioFileAPI instance
   */
  public $file;
  /**
   * Reference to PodioCalendarAPI instance
   */
  public $calendar;
  /**
   * Reference to PodioSearchAPI instance
   */
  public $search;
  /**
   * Reference to PodioStreamAPI instance
   */
  public $stream;
  /**
   * Reference to PodioAppStoreAPI instance
   */
  public $app_store;
  /**
   * Reference to PodioTagAPI instance
   */
  public $tag;
  /**
   * Reference to PodioBulletinAPI instance
   */
  public $bulletin;
  /**
   * Reference to PodioWidgetAPI instance
   */
  public $widget;
  /**
   * Reference to PodioFilterAPI instance
   */
  public $filter;
  /**
   * Reference to PodioFormAPI instance
   */
  public $form;

  public function __construct() {
    $this->api = PodioBaseAPI::instance();
    $this->notification = new PodioNotificationAPI();
    $this->conversation = new PodioConversationAPI();
    $this->status = new PodioStatusAPI();
    $this->task = new PodioTaskAPI();
    $this->app = new PodioAppAPI();
    $this->item = new PodioItemAPI();
    $this->user = new PodioUserAPI();
    $this->comment = new PodioCommentAPI();
    $this->rating = new PodioRatingAPI();
    $this->space = new PodioSpaceAPI();
    $this->org = new PodioOrgAPI();
    $this->contact = new PodioContactAPI();
    $this->subscription = new PodioSubscriptionAPI();
    $this->file = new PodioFileAPI();
    $this->calendar = new PodioCalendarAPI();
    $this->search = new PodioSearchAPI();
    $this->stream = new PodioStreamAPI();
    $this->app_store = new PodioAppStoreAPI();
    $this->tag = new PodioTagAPI();
    $this->bulletin = new PodioBulletinAPI();
    $this->widget = new PodioWidgetAPI();
    $this->filter = new PodioFilterAPI();
    $this->form = new PodioFormAPI();
  }
}

/**
 * A Singleton class that handles all communication with the API server.
 */
class PodioBaseAPI {
  
  /**
   * URL for the API server
   */
  protected $url;
  /**
   * OAuth client id
   */
  protected $client_id;
  /**
   * OAuth client secret
   */
  protected $secret;
  /**
   * Contains the last error message from the API server
   */
  protected $last_error;
  /**
   * Current log handler for the API log
   */
  protected $log_handler;
  /**
   * Current log name for the API log
   */
  protected $log_name;
  /**
   * Current log identification for the API log
   */
  protected $log_ident;
  /**
   * Current log levels for the API log
   */
  protected $log_levels;
  private static $instance;

  private function __construct($url, $client_id, $client_secret, $upload_end_point, $frontend_token = '') {
    $this->url = $url;
    $this->client_id = $client_id;
    $this->secret = $client_secret;
    $this->frontend_token = $frontend_token;
    $this->upload_end_point = $upload_end_point;
    $this->log_handler = 'error_log';
    $this->log_name = '';
    $this->log_ident = 'PODIO_API_CLIENT';
    $this->log_levels = array(
      'error' => TRUE,
      'GET' => FALSE,
      'POST' => 'verbose',
      'PUT' => 'verbose',
      'DELETE' => FALSE,
    );
  }
  
  /**
   * Constructor for the singleton instance. Call with parameters first time, 
   * call without parameters subsequent times.
   *
   * @param $url URL for the API server
   * @param $client_id OAuth Client id
   * @param $client_secret OAuth client secret
   * @param $upload_end_point Upload end point for file uploads
   * @param $frontend_token Special token used by Podio
   *
   * @return Singleton instance of PodioBaseAPI object
   */
  public static function instance($url = '', $client_id = '', $client_secret = '', $upload_end_point = '', $frontend_token = '') {
    if (!self::$instance) {
      self::$instance = new PodioBaseAPI($url, $client_id, $client_secret, $upload_end_point, $frontend_token);
    }
    return self::$instance;
  }
  
  /**
   * Log a message to the API log
   *
   * @param $message The message to log
   * @param $level The log level. See:
   *               http://www.indelible.org/php/Log/guide.html#log-levels
   */
  public function log($message, $level = PEAR_LOG_INFO) {
    $logger = &Log::singleton($this->log_handler, $this->log_name, $this->log_ident);
    $logger->log('[api] ' . $message, $level);
  }
  
  /**
   * Set the log handler for the API log. See:
   * http://www.indelible.org/php/Log/guide.html#configuring-a-handler
   *
   * @param $handler
   * @param $name
   */
  public function setLogHandler($handler, $name, $ident) {
    $this->log_handler = $handler;
    $this->log_name = $name;
    $this->log_ident = $ident;
  }
  
  /**
   * Get the current log level for an area.
   *
   * @param $name Area to get log level for. Can be:
   * - error: Any error from API server
   * - GET: GET requests
   * - POST: POST requests
   * - PUT: PUT requests
   * - DELETE: DELETE requests
   */
  public function getLogLevel($name) {
    return $this->log_levels[$name];
  }
  
  /**
   * Set the current log level for an area.
   * @param $name Area to set. Can be:
   * - error: Any error from API server
   * - GET: GET requests
   * - POST: POST requests
   * - PUT: PUT requests
   * - DELETE: DELETE requests
   * @param $value New log level. Either TRUE, FALSE, "concise" or "verbose"
   */
  public function setLogLevel($name, $value) {
    $this->log_levels[$name] = $value;
  }
  
  /**
   * Get the current API server URL
   */
  public function getUrl() {
    return $this->url;
  }
  
  /**
   * Get the OAuth client id
   */
  public function getClientId() {
    return $this->client_id;
  }
  
  /**
   * Get the OAuth client secret
   */
  public function getClientSecret() {
    return $this->secret;
  }
  
  /**
   * Get the last error message from the API server
   */
  public function getError() {
    return $this->last_error;
  }
  
  /**
   * Normalize filters for GET requests
   */
  public function normalizeFilters($filters) {
    $data = array();
    foreach ($filters as $filter) {
      if (empty($filter['values'])) {
        $data[$filter['key']] = '';
      }
      else if ($filter['key'] == 'created_by') {
        $created_bys = array();
        foreach ($filter['values'] as $value) {
          $created_bys[] = $value['type'].':'.$value['id'];
        }
        $data['created_by'] = implode(';', $created_bys);
      }
      else if (is_array($filter['values'])) {
        if (array_key_exists('from', $filter['values'])) {
          $from = isset($filter['values']['from']) ? $filter['values']['from'] : '';
          $to = $filter['values']['to'] ? $filter['values']['to'] : '';
          $data[$filter['key']] = $from.'-'.$to;
        }
        else {
          foreach ($filter['values'] as $k => $v) {
            if ($v === NULL) {
              $filter['values'][$k] = 'null';
            }
          }
          $data[$filter['key']] = implode(';', $filter['values']);
        }
      }
    }
    return $data;
  }
  
  /**
   * Upload a file for later use.
   *
   * @param $file Path to file for upload
   * @param $name File name to use
   *
   * @return Array with new file id
   */
  public function upload($file, $name) {
    $oauth = PodioOAuth::instance();
    $request = new HTTP_Request2($this->upload_end_point, HTTP_Request2::METHOD_POST, array(
      'ssl_verify_peer'   => false,
      'ssl_verify_host'   => false
    ));

    $request->setConfig('use_brackets', FALSE);
    $request->setConfig('follow_redirects', TRUE);
    $request->setHeader('User-Agent', 'Podio API Client/1.0');
    $request->setHeader('Accept', 'application/json');
    $request->setHeader('Authorization', 'OAuth2 '.$oauth->access_token);
    
    $request->addUpload('file', $file);
    $request->addPostParameter('name', $name);

    try {
        $response = $request->send();
        switch ($response->getStatus()) {
          case 200 : 
          case 201 : 
          case 204 : 
            return json_decode($response->getBody(), TRUE);
            break;
          case 401 : 
          case 400 : 
          case 403 : 
          case 404 : 
          case 410 : 
          case 500 : 
          case 503 : 
            if ($this->getLogLevel('error')) {
              $this->log($request->getMethod() .' '. $response->getStatus().' '.$response->getReasonPhrase().' '.$request->getUrl(), PEAR_LOG_WARNING);
              $this->log($response->getBody(), PEAR_LOG_WARNING);
            }
            $this->last_error = json_decode($response->getBody(), TRUE);
            return FALSE;
            break;
          default : 
            break;
        }
    } catch (HTTP_Request2_Exception $e) {
      if ($this->getLogLevel('error')) {
        $this->log($e->getMessage(), PEAR_LOG_WARNING);
      }
    }
  }
  
  /**
   * Build and perform an API request.
   *
   * @param $url URL to make call to. E.g. /user/status
   * @param $data Any data to send with the request
   * @param method HTTP method to be used for call
   *
   * @return Varies by API call
   */
  public function request($url, $data = '', $method = HTTP_Request2::METHOD_GET) {
    $oauth = PodioOAuth::instance();
    $request = new HTTP_Request2($this->url . $url, $method, array(
      'ssl_verify_peer'   => false,
      'ssl_verify_host'   => false
    ));
    
    $request->setConfig('use_brackets', FALSE);
    $request->setConfig('follow_redirects', TRUE);
    $request->setHeader('User-Agent', 'Podio API Client/1.0');
    $request->setHeader('Accept', 'application/json');
    $request->setHeader('Accept-Encoding', 'gzip');
    if ($this->frontend_token) {
      $request->setHeader('X-Podio-Frontend-Token', $this->frontend_token);
    }
    $location = $request->getUrl();
    
    // These URLs can be called without an access token.
    $no_token_list = array(
      '@^/$@',
      '@^/space/invite/status$@',
      '@^/user/activate_user$@',
      '@^/user/recover_password$@',
      '@^/user/reset_password$@',
      '@^/space/invite/decline$@',
      '@^/app_store/author/[0-9]+/profile$@',
      '@^/app_store/category/$@',
      '@^/app_store/category/[0-9]+$@',
      '@^/app_store/featured$@',
      '@^/app_store/[0-9]+/v2$@',
      '@^/app_store/author/[0-9]+/v2/$@',
      '@^/app_store/category/[0-9]+/$@',
      '@^/app_store/search/$@',
      '@^/app_store/top/v2/$@',
    );
    
    $is_on_no_token_list = FALSE;
    foreach ($no_token_list as $regex) {
      if (preg_match($regex, $url)) {
        $is_on_no_token_list = TRUE;
        break;
      }
    }
    
    if (!($url == '/user/' && $method == HTTP_Request2::METHOD_POST) && !$is_on_no_token_list) {
      if (!$oauth->access_token && !(substr($url, 0, 6) == '/file/' && substr($url, -9) == '/location')) {
        return FALSE;
      }
      if ($oauth->access_token) {
        $request->setHeader('Authorization', 'OAuth2 '.$oauth->access_token);
      }
    }
    
    switch ($method) {
      case HTTP_Request2::METHOD_GET : 
        $request->setHeader('Content-type', 'application/x-www-form-urlencoded');
        if (is_array($data)) {
          foreach ($data as $key => $value) {
            $location->setQueryVariable($key, $value);
          }
        }
        break;
      case HTTP_Request2::METHOD_DELETE : 
        $request->setHeader('Content-type', 'application/x-www-form-urlencoded');
        if (is_array($data)) {
          foreach ($data as $key => $value) {
            $location->setQueryVariable($key, $value);
          }
        }
        break;
      case HTTP_Request2::METHOD_POST : 
      case HTTP_Request2::METHOD_PUT : 
        $request->setHeader('Content-type', 'application/json');
        $request->setBody(json_encode($data));
        break;
      default : 
        break;
    }

    // Log request if needed.
    if ($this->getLogLevel($method)) {
      $this->log($request->getMethod().' '.$request->getUrl());
      if ($this->getLogLevel($method) == 'verbose') {
        $this->log($request->getBody());
      }
    }

    try {
        $response = $request->send();
        switch ($response->getStatus()) {
          case 200 : 
            return $response;
            break;
          case 201 : 
            // Only POST requests can result in 201 Created.
            if ($request->getMethod() == HTTP_Request2::METHOD_POST) {
              return $response;
            }
            break;
          case 204 : 
            return $response;
            break;
          case 401 : 
            $body = json_decode($response->getBody(), TRUE);
            if (strstr($body['error_description'], 'expired_token')) {
              if ($oauth->refresh_token) {
                // Access token is expired. Try to refresh it.
                $refresh_token = $oauth->refresh_token;
                $oauth->getAccessToken('refresh_token', array('refresh_token' => $refresh_token));

                if ($oauth->access_token) {
                  // Try the original request again.
                  return $this->request($url, $data, $method);
                }
                else {
                  // New token could not be fetched. Log user out.
                  $oauth->throwError('refresh_failed', 'Refreshing access token failed.');
                }
              }
              else {
                // We have tried in vain to get a new access token. Log the user out.
                $oauth->throwError('no_refresh_token', 'No refresh token available.');
              }
            }
            elseif (strstr($body['error'], 'invalid_token') || strstr($body['error'], 'invalid_request')) {
              // Access token is invalid. Log the user out and try again.
              $oauth->throwError('invalid_token', 'Invalid token.');
            }
            break;
          case 400 : 
            $body = json_decode($response->getBody(), TRUE);
            if (strstr($body['error'], 'invalid_grant')) {
              $oauth = PodioOAuth::instance();
              $oauth->access_token = '';
              $oauth->refresh_token = '';

              $oauth->throwError('invalid_grant', 'Invalid grant.');
              break;
            }
          case 403 : 
          case 404 : 
          case 410 : 
          case 500 : 
          case 503 : 
            if ($this->getLogLevel('error')) {
              $this->log($request->getMethod() .' '. $response->getStatus().' '.$response->getReasonPhrase().' '.$request->getUrl(), PEAR_LOG_WARNING);
              $this->log($response->getBody(), PEAR_LOG_WARNING);
            }
            $this->last_error = json_decode($response->getBody(), TRUE);
            return FALSE;
            break;
          default : 
            break;
        }
    } catch (HTTP_Request2_Exception $e) {
      if ($this->getLogLevel('error')) {
        $this->log($e->getMessage(), PEAR_LOG_WARNING);
      }
    }
  }
}
