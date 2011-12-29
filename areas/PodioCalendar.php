<?php

/**
 * The calendar is used to get the calendar for a user. The calendar includes 
 * items with a date field in the interval and tasks with a due date in 
 * the interval.
 * 
 * Calendar entries are always sorted by date.
 */
class PodioCalendar {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Returns all items that the user have access to and all tasks that are 
   * assigned to the user. The items and tasks can be filtered by a list 
   * of space ids, but tasks without a reference will always be returned.
   */
  public function get($attributes = array()) {
    if ($response = $this->podio->get('/calendar/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all items and tasks that the user have access to in the given 
   * space. Tasks with reference to other spaces are not returned or tasks 
   * with no reference.
   */
  public function getSpace($space_id, $attributes = array()) {
    if ($response = $this->podio->get('/calendar/space/'.$space_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the items and tasks that are related to the given app
   */
  public function getApp($app_id, $attributes = array()) {
    if ($response = $this->podio->get('/calendar/app/'.$app_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the objects that are currently muted from the global calendar.
   */
  public function getMutes() {
    if ($response = $this->podio->get('/calendar/mute/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Mutes the given object types in the given scope from the global calendar.
   */
  public function muteObject($scope_type, $scope_id, $object_type) {
    if ($response = $this->podio->post('/calendar/mute/' . $scope_type .'/' .$scope_id . '/'. $object_type)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Unmutes the given object types in the given scope from the global calendar.
   */
  public function unmuteObject($scope_type, $scope_id, $object_type) {
    if ($response = $this->podio->delete('/calendar/mute/' . $scope_type .'/' .$scope_id . '/'. $object_type)) {
      return TRUE;
    }
  }
}
