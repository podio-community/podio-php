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
  
  public function getViewed($limit = 20, $offset = 0, $sent = 0, $filter_values = NULL) {
    $data['limit'] = $limit;
    $data['offset'] = $offset;
    $data['sent'] = $sent;
    if( $sent == 1 ) {
      $data['types'] = 'message';
      $data['date_type'] = 'created';
    }
    
    if( isset($filter_values) ) {
      if( isset($filter_values['contacts']) ) {
        $data['users'] = implode(',', $filter_values['contacts']);
      }
      if( isset($filter_values['month']) && isset($filter_values['year']) ) {
        $start_date = mktime(0, 0, 0, $filter_values['month'], 1, $filter_values['year']);
        $end_date = mktime(0, 0, 0, $filter_values['month']+1, 1, $filter_values['year']);
        $data['date_from'] = date('Y-m-d h:i:s', $start_date);
        $data['date_to'] = date('Y-m-d h:i:s', $end_date);
      }
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
}

