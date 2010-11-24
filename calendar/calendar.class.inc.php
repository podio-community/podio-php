<?php

class CalendarAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  public function getGlobal($date_from, $date_to, $space_ids = NULL, $types = NULL) {
    static $list;
    
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
    $key = serialize($data);
    
    if (!isset($list[$key])) {
      if ($response = $this->podio->request('/calendar/', $data)) {
        $list[$key] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$key];
  }

  public function getSpace($space_id, $date_from, $date_to, $types = NULL) {
    $data = array('date_from' => $date_from, 'date_to' => $date_to);
    if ($types) {
      $data['types'] = $types;
    }
    if ($response = $this->podio->request('/calendar/space/'.$space_id.'/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getApp($app_id, $date_from, $date_to) {
    $data = array('date_from' => $date_from, 'date_to' => $date_to);
    if ($response = $this->podio->request('/calendar/app/'.$app_id.'/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

}

