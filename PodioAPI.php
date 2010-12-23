<?php

require_once('Log.php');
require_once('HTTP/Request2.php');
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

/**
 * Primary Hoist API implementation class.
 */
class PodioAPI {
	
  public $api;
	public $notifications;
	public $conversation;
	public $status;
	public $task;
	public $app;
	public $item;
	public $user;
	public $comment;
	public $rating;
	public $space;
	public $org;
	public $contact;
	public $subscription;
	public $file;
	public $calendar;
	public $search;
	public $stream;
	public $app_store;
	public $tag;
	public $bulletin;
	public $widget;
	public $filter;

	public function __construct() {
	  $this->api = PodioBaseAPI::instance();
    $this->notification = new NotificationAPI();
    $this->conversation = new ConversationAPI();
    $this->status = new StatusAPI();
    $this->task = new TaskAPI();
    $this->app = new AppAPI();
    $this->item = new ItemAPI();
    $this->user = new UserAPI();
    $this->comment = new CommentAPI();
    $this->rating = new RatingAPI();
    $this->space = new SpaceAPI();
    $this->org = new OrgAPI();
    $this->contact = new ContactAPI();
    $this->subscription = new SubscriptionAPI();
    $this->file = new FileAPI();
    $this->calendar = new CalendarAPI();
    $this->search = new SearchAPI();
    $this->stream = new StreamAPI();
    $this->app_store = new AppStoreAPI();
    $this->tag = new TagAPI();
    $this->bulletin = new BulletinAPI();
    $this->widget = new WidgetAPI();
    $this->filter = new FilterAPI();
	}
}

class PodioBaseAPI {
  
  protected $url;
  protected $version;
  protected $mail;
  protected $secret;
  protected $last_error;
  private static $instance;

  private function __construct($url, $client_id, $client_secret, $upload_end_point, $frontend_token = '') {
    $this->url = $url;
    $this->client_id = $client_id;
    $this->secret = $client_secret;
    $this->frontend_token = $frontend_token;
    $this->upload_end_point = $upload_end_point;
  }

  public static function instance($url = '', $client_id = '', $client_secret = '', $upload_end_point = '', $frontend_token = '') {
    if (!self::$instance) {
      self::$instance = new PodioBaseAPI($url, $client_id, $client_secret, $upload_end_point, $frontend_token);
    }
    return self::$instance;
  }
  
  public function getUrl() {
    return $this->url;
  }
  public function getAccessToken($data) {
    $data['client_id'] = $this->client_id;
    $data['client_secret'] = $this->secret;
    if ($response = $this->request('/oauth/token', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  public function getError() {
    return $this->last_error;
  }
  
  /**
   * Upload a file for later use.
   */
  public function upload($file, $name) {
    $logger = &Log::singleton('error_log', '', 'HTTP_UPLOAD');
    $oauth = PodioOAuth::instance();
    $request = new HTTP_Request2($this->upload_end_point, HTTP_Request2::METHOD_POST, array(
      'ssl_verify_peer'   => false,
      'ssl_verify_host'   => false
    ));

    $request->setConfig('use_brackets', FALSE);
    $request->setConfig('follow_redirects', TRUE);
    $request->setHeader('User-Agent', 'Podio API Client/1.0');
    $request->setHeader('Accept', 'application/json');
    $request->setHeader('AccessToken', $oauth->access_token);
    $request->setHeader('RefreshToken', $oauth->refresh_token);
    
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
            $logger->log($request->getMethod() .' '. $response->getStatus().' '.$response->getReasonPhrase().' '.$request->getUrl(), PEAR_LOG_ERR);
            $logger->log('*** '.$response->getBody(), PEAR_LOG_ERR);
            $this->last_error = json_decode($response->getBody(), TRUE);
            return FALSE;
            break;
          default : 
            break;
        }
    } catch (HTTP_Request2_Exception $e) {
      $logger->log($e->getMessage(), PEAR_LOG_ERR);
    }
    
  }
  
  /**
   * Build and perform the request.
   */
  public function request($url, $data = '', $method = HTTP_Request2::METHOD_GET) {
    $oauth = PodioOAuth::instance();
    $request = new HTTP_Request2($this->url . $url, $method, array(
      'ssl_verify_peer'   => false,
      'ssl_verify_host'   => false
    ));
    
    $logger = &Log::singleton('error_log', '', 'HTTP_REQUEST');
    //$logger->log($url.' *** '.print_r($data, true));
    // $logger->log('Making request to: '.$url);
    // $logger->log('Making request using: '.$oauth->access_token);
    
    // $request->setMethod($method);
    $request->setConfig('use_brackets', FALSE);
    $request->setConfig('follow_redirects', TRUE);
    $request->setHeader('User-Agent', 'Podio API Client/1.0');
    $request->setHeader('Accept', 'application/json');
    $request->setHeader('Accept-Encoding', 'gzip');
    $location = $request->getUrl();
    
    // These URLs can be called without an access token.
    $no_token_list = array(
      '/',
      '/oauth/token',
      '/space/invite/status',
      '/user/activate_user',
      '/user/recover_password',
      '/user/reset_password',
      '/space/invite/decline',
    );
    if (!($url == '/user/' && $method == HTTP_Request2::METHOD_POST) && !in_array($url, $no_token_list)) {
      
      if (!$oauth->access_token && !(substr($url, 0, 6) == '/file/' && substr($url, -9) == '/location')) {
        // $logger->log('No access token. Returning FALSE: '.$url, PEAR_LOG_ERR);
        return FALSE;
      }
      
      if ($oauth->access_token) {
        $request->setHeader('Authorization', 'OAuth2 '.$oauth->access_token);
      }
    }
    
    // $parsed = parse_url($request->getUrl());
    // $query = array();
    // parse_str($parsed['query'], $query);
    // unset($query['oauth_token']);
    // $q_str = array();
    // foreach ($query as $k => $v) {
    //   $q_str[] = $k.'='.$v;
    // }

    
    switch ($method) {
      case HTTP_Request2::METHOD_GET : 
        $request->setHeader('Content-type', 'application/x-www-form-urlencoded');
        if (is_array($data)) {
          foreach ($data as $key => $value) {
            $location->setQueryVariable($key, $value);
          }
        }
        
        // $get = &Log::singleton('error_log', '', $request->getMethod());
        // $get->log($_SERVER['REQUEST_URI'] . ' --- ' . $parsed['path'] . '?' . implode('&', $q_str), PEAR_LOG_ERR);
        
        break;
      case HTTP_Request2::METHOD_DELETE : 
        // $del = &Log::singleton('error_log', '', $request->getMethod());
        // $del->log($_SERVER['REQUEST_URI'] . ' --- ' . $parsed['path'] . '?' . implode('&', $q_str), PEAR_LOG_ERR);
        $request->setHeader('Content-type', 'application/x-www-form-urlencoded');
        if (is_array($data)) {
          foreach ($data as $key => $value) {
            $location->setQueryVariable($key, $value);
          }
        }
        break;
      case HTTP_Request2::METHOD_POST : 
      case HTTP_Request2::METHOD_PUT : 
      
        if ($url == '/oauth/token') {
          $request->setHeader('Content-type', 'application/x-www-form-urlencoded');
          foreach ($data as $key => $value) {
            $request->addPostParameter($key, $value);
          }
        }
        else {
          $request->setHeader('Content-type', 'application/json');
          $request->setBody(json_encode($data));
        }
        
        $logger->log($request->getMethod().' '.$request->getUrl());
        $logger->log($request->getBody());
        
        break;
      default : 
        break;
    }

    try {
        $response = $request->send();
        // $logger->log($request->getUrl() . ' *** Duration: '.(microtime()-$start));
        // $logger->log(microtime()-$start);
        
        // $logger->log(print_r($response, true));
      

        switch ($response->getStatus()) {
          case 200 : 
            return $response;
            break;
          case 201 : 
            // Only POST requests can result in 201 Created.
            if ($request->getMethod() == HTTP_Request2::METHOD_POST) {
              return $response;
            }
            $logger->log($request->getMethod() .' '. $response->getStatus().' '.$response->getReasonPhrase().' '.$request->getUrl(), PEAR_LOG_ERR);
            break;
          case 204 : 
            return $response;
            break;
          case 401 : 
            $body = json_decode($response->getBody(), TRUE);
            if (strstr($body['error_description'], 'expired_token')) {
              if ($oauth->refresh_token) {
                // $logger->log('Refreshing access token using: '.$oauth->refresh_token);
                // Access token is expired. Try to refresh it.
                $grant_data = array(
                  'grant_type' => 'refresh_token',
                  'refresh_token' => $oauth->refresh_token,
                );

                $oauth->refresh_token = '';

                $new = $this->getAccessToken($grant_data);
                if ($new) {
                  // $logger->log('Access token refreshed with success. Trying original request.');
                  
                  $oauth = PodioOAuth::instance();
                  $oauth->access_token = $new['access_token'];
                  $oauth->refresh_token = $new['refresh_token'];

                  // Try the original request again.
                  return $this->request($url, $data, $method);

                }
                else {
                  // $logger->log('Refresh request failed.');
                  // New token could not be fetched. Log user out.
                  $oauth->throw_error('refresh_failed', 'Refreshing access token failed.');
                  // user_logout();
                }
              }
              else {
                // We have tried in vain to get a new access token. Log the user out.
                $oauth->throw_error('no_refresh_token', 'No refresh token available.');
                // user_logout();
              }
            }
            elseif (strstr($body['error'], 'invalid_token') || strstr($body['error'], 'invalid_request')) {
              // Access token is invalid. Log the user out and try again.
              $oauth->throw_error('invalid_token', 'Invalid token.');
              // user_logout();
            }
            break;
          case 400 : 
            if ($url != '/oauth/token') {
              $body = json_decode($response->getBody(), TRUE);
              if (strstr($body['error'], 'invalid_grant') && $url != 'oauth/token') {

                // $logger->log('Hitting invalid grant. Logging out.');

                $oauth = PodioOAuth::instance();
                $oauth->access_token = '';
                $oauth->refresh_token = '';

                $oauth->throw_error('invalid_grant', 'Invalid grant.');

                // user_logout();
                break;
              }
            }
          case 403 : 
          case 404 : 
          case 410 : 
          case 500 : 
          case 503 : 
            $logger->log($request->getMethod() .' '. $response->getStatus().' '.$response->getReasonPhrase().' '.$request->getUrl(), PEAR_LOG_ERR);
            $logger->log('*** '.$response->getBody(), PEAR_LOG_ERR);
            $this->last_error = json_decode($response->getBody(), TRUE);
            return FALSE;
            break;
          default : 
            break;
        }
    } catch (HTTP_Request2_Exception $e) {
      $logger->log($e->getMessage(), PEAR_LOG_ERR);
    }
    $logger->close();
  }
}
