<?php

/**
 * The calendar is used to get the calendar for a user. The calendar includes 
 * items with a date field in the interval and tasks with a due date in 
 * the interval.
 * 
 * Calendar entries are always sorted by date.
 */
class PodioCalendarAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Returns all items that the user have access to and all tasks that are 
   * assigned to the user. The items and tasks can be filtered by a list 
   * of space ids, but tasks without a reference will always be returned.
   *
   * @param $date_from The date to serach from
   * @param $date_to The date to search to
   * @param $space_ids An optional array of space ids to which the search 
   *                   should be restricted
   * @param $types The types of objects to be included. Valid options are 
   *               "item" and "task". If left blank, all types of objects 
   *               will be returned
   *
   * @return Array of calendar events
   */
  public function getGlobal($date_from, $date_to, $space_ids = NULL, $types = NULL) {
    $data = array(
      'date_from' => $date_from,
      'date_to' => $date_to,
    );
    if ($space_ids) {
      $data['space_ids'] = implode(',', $space_ids);
    }
    if ($types) {
      $data['types'] = $types;
    }
    if ($response = $this->podio->request('/calendar/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all items and tasks that the user have access to in the given 
   * space. Tasks with reference to other spaces are not returned or tasks 
   * with no reference.
   *
   * @param $space_id The id of the space to get calendar for
   * @param $date_from The date to serach from
   * @param $date_to The date to search to
   * @param $types The types of objects to be included. Valid options are 
   *               "item" and "task". If left blank, all types of objects 
   *               will be returned
   *
   * @return Array of calendar events
   */
  public function getSpace($space_id, $date_from, $date_to, $types = NULL) {
    $data = array('date_from' => $date_from, 'date_to' => $date_to);
    if ($types) {
      $data['types'] = $types;
    }
    if ($response = $this->podio->request('/calendar/space/'.$space_id.'/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the items and tasks that are related to the given app
   * 
   * @param $app_id The id of the app to get events for
   * @param $date_from The date to serach from
   * @param $date_to The date to search to
   *
   * @return Array of calendar events
   */
  public function getApp($app_id, $date_from, $date_to) {
    $data = array('date_from' => $date_from, 'date_to' => $date_to);
    if ($response = $this->podio->request('/calendar/app/'.$app_id.'/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }


  /**
   * Returns the objects that are currently muted from the global calendar.
   *
   *
   * @return Array of calendar mutes
   */
  public function getMutes() {
    if ($response = $this->podio->request('/calendar/mute/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Mutes the given object types in the given scope from the global calendar.
   *
   * @param $scope_type
   * @param $scope_id
   * @param $object_type
   *
   * @return Array of calendar mutes
   */
  public function muteObject($scope_type, $scope_id, $object_type) {
    if ($response = $this->podio->request('/calendar/mute/' . $scope_type .'/' .$scope_id . '/'. $object_type , $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Unmutes the given object types in the given scope from the global calendar.
   *
   * @param $scope_type
   * @param $scope_id
   * @param $object_type
   *
   * @return Array of calendar mutes
   */
  public function unmuteObject($scope_type, $scope_id, $object_type) {
    if ($response = $this->podio->request('/calendar/mute/' . $scope_type .'/' .$scope_id . '/'. $object_type , $data, HTTP_Request2::METHOD_DELETE)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

}

