<?php

/**
 * A notification is an information about an event that occured in Podio. 
 * A notification is directed against a single user, and can have a status 
 * of either unread or viewed. Notifications have a reference to the action 
 * that caused the notification.
 */
class PodioNotificationAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Returns a single notification from an id. The notification will contain 
   * the bare data, but will have a reference to the object that caused 
   * the notification.
   *
   * @param $notification_id The id of the notification to retrieve
   */
  public function get($notification_id) {
    $response = $this->podio->request('/notification/'.$notification_id);
    if ($response) {
      $notification = json_decode($response->getBody(), TRUE);
      return $notification;
    }
  }

  /**
   * Returns a list of notifications that have not yet been viewed. 
   * The notifications will be sorted descending by the time of creation.
   *
   * @param $limit The limit on the number of notifications to 
   *               return, default is 20
   * @param $offset The offset on the notifications to return, default is 0
   *
   * @return Array of notifications
   */
  public function getNew($limit = 20, $offset = 0) {
    $data['limit'] = $limit;
    $data['offset'] = $offset;
    $response = $this->podio->request('/notification/inbox/new', $data);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the number of unread notifications for the active user.
   */
  public function getNewCount() {
    $response = $this->podio->request('/notification/inbox/new/count');
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the notifications in the inbox that has already been viewed. 
   * The notifications are sorted in descending order, either by viewed time 
   * or creation time.
   *
   * @param $limit The limit on the number of notifications to return, 
   *               default is 10
   * @param $offset The offset on the notifications to return, default is 0
   * @param $date_type The type of date to use for sorting and filtering. 
   *                   Can be either "created" or "viewed". 
   *                   Default is "created"
   * @param $types Array of the types of notifications to see in the inbox
   * @param $date_from The earliest date to get notifications from
   * @param $date_to The latest date to get notifications from
   * @param $users Array of user ids to see notifications from
   * @param $sent 1 if sent notifications should be returned, 0 otherwise
   */
  public function getViewed($limit, $offset = 0, $date_type = 'created', $types = NULL, $date_from = NULL, $date_to = NULL, $users = NULL, $sent = 0) {
  //public function getViewed($limit = 20, $offset = 0, $sent = 0, $filter_values = NULL) {
    $data = array();
    $data['limit'] = $limit;
    $data['offset'] = $offset;
    $data['sent'] = $sent;
    $data['date_type'] = $date_type;

    if($types) {
      $data['types'] = implode(',', $types);
    }
    if($date_from) {
      $data['date_from'] = $date_from;
    }
    if($date_to) {
      $data['date_to'] = $date_to;
    }
    if($users) {
      $data['users'] = implode(',', $users);
    }
    
    $response = $this->podio->request('/notification/inbox/viewed', $data);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }  
  
  /**
   * Return the top filter options for the inbox viewed.
   *
   * @param $limit The limit on how many filters of each type 
   *               should be returned
   * @param $date_type Which kind of date should be used for the date 
   *                   filtering, can be either "created" or "viewed"
   * @param $sent 1 if only sent notifications should be 
   *              used, 0 otherwise
   *
   * @return Array of "months" and "users" available for filtering
   */
  public function getFiltersAndCounts($limit = NULL, $date_type = NULL, $sent = NULL) {
    $data = array();
    if ($limit) {
      $data['limit'] = $limit;
    }
    if ($date_type) {
      $data['date_type'] = $date_type;
    }
    if ($sent) {
      $data['sent'] = $sent;
    }
    $response = $this->podio->request('/notification/inbox/viewed/filters', $data);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Mark the notification as viewed or unviewed.
   *
   * @param $notification_id The id of the notification to act on
   * @param $viewed 1 to mark as viewed, 0 to mark as unviewed
   */
  public function markViewed($notification_id, $viewed) {
    if( $viewed == 1) {
      $method = HTTP_Request2::METHOD_POST;
    }
    else {
      $method = HTTP_Request2::METHOD_DELETE;
    }
    $response = $this->podio->request('/notification/'.$notification_id.'/viewed', array(), $method);
  }

  /**
   * Marks all the users notifications as viewed.
   */
  public function markAllViewed() {
    $response = $this->podio->request('/notification//viewed', array(), HTTP_Request2::METHOD_POST);
  }
  
  /**
   * Returns the notification settings for the active user
   */
  public function getSettings() {
    $response = $this->podio->request('/notification/settings', array());
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the direct notification settings for the user
   *
   * @param $send_notifications True to enable direct mails, false otherwise
   */
  public function setSetting($send_notifications) {
    $data['direct'] = $send_notifications;
    $response = $this->podio->request('/notification/settings', $data, HTTP_Request2::METHOD_PUT);
  }
  
  /**
   * Updates the digest notification settings for the user
   *
   * @param $send_digest True to enable digest mails, false otherwise
   */
  public function setDigestSetting($send_digest) {
    $data['digest'] = $send_digest;
    $response = $this->podio->request('/notification/settings', $data, HTTP_Request2::METHOD_PUT);
  }

  /**
   * Updates the bulletin notification settings for the user
   *
   * @param $send_bulletin True to enable bulletin mails, false otherwise
   */
  public function setBulletinSetting($send_bulletin) {
    $data['bulletin'] = $send_bulletin;
    $response = $this->podio->request('/notification/settings', $data, HTTP_Request2::METHOD_PUT);
  }
}

