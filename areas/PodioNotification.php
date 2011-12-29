<?php

/**
 * A notification is an information about an event that occured in Podio. 
 * A notification is directed against a single user, and can have a status 
 * of either unread or viewed. Notifications have a reference to the action 
 * that caused the notification.
 */
class PodioNotification {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Returns a single notification from an id. The notification will contain 
   * the bare data, but will have a reference to the object that caused 
   * the notification.
   */
  public function get($notification_id) {
    if ($response = $this->podio->get('/notification/'.$notification_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns a list of notifications that have not yet been viewed. 
   * The notifications will be sorted descending by the time of creation.
   */
  public function getNew($attributes = array()) {
    if ($response = $this->podio->get('/notification/inbox/new', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the number of unread notifications for the active user.
   */
  public function getNewCount() {
    if ($response = $this->podio->get('/notification/inbox/new/count')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the notifications in the inbox that has already been viewed. 
   * The notifications are sorted in descending order, either by viewed time 
   * or creation time.
   */
  public function getViewed($attributes = array()) {
    if ($response = $this->podio->get('/notification/inbox/viewed', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns a list of notifications based on the query parameters. 
   * The notifications will be grouped based on their context.
   */
  public function getNotifications($attributes = array()) {
    if ($response = $this->podio->get('/notification/inbox/viewed', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Return the top filter options for the inbox viewed.
   */
  public function getFiltersAndCounts($attributes = array()) {
    if ($response = $this->podio->get('/notification/inbox/viewed/filters', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Mark the notification as viewed.
   */
  public function markViewed($notification_id) {
    if ($response = $this->podio->post('/notification/'.$notification_id.'/viewed')) {
      return TRUE;
    }
  }

  /**
   * Mark the notification as unviewed.
   */
  public function markUnviewed($notification_id) {
    if ($response = $this->podio->delete('/notification/'.$notification_id.'/viewed')) {
      return TRUE;
    }
  }
  
  /**
   * Star the given notification to move it to the star list.
   */
  public function star($notification_id) {
    $response = $this->podio->post('/notification/'.$notification_id.'/star');
  }

  /**
   * Removes the star on the notification
   */
  public function unstar($notification_id) {
    $response = $this->podio->delete('/notification/'.$notification_id.'/star');
  }

  /**
   * Marks all the users notifications as viewed.
   */
  public function markAllViewed() {
    $response = $this->podio->post('/notification/viewed');
  }
  
  /**
   * Returns the notification settings for the active user
   */
  public function getSettings() {
    if ($response = $this->podio->get('/notification/settings')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Updates the notification settings for the user
   */
  public function updateSettings($attributes = array()) {
    if ($response = $this->podio->put('/notification/settings', $attributes)){
      return TRUE;
    }
  }
}

