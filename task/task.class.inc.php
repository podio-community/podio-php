<?php

class TaskAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  public function create($text, $private = FALSE, $due_date = '', $responsible = NULL, $ref_type = NULL, $ref_id = NULL) {
    $url = '/task/';
    if ($ref_id && $ref_type) {
      $url = '/task/'.$ref_type.'/'.$ref_id.'/';
    }
    $data = array('text' => $text, 'private' => $private);
    if ($due_date) {
      $data['due_date'] = $due_date;
    }
    if ($responsible) {
      $data['responsible'] = (int)$responsible;
    }
    
    if ($response = $this->podio->request($url, $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getById($task_id) {
    static $list;
    if (!isset($list[$task_id])) {
      if ($response = $this->podio->request('/task/'.$task_id)) {
        $list[$task_id] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$task_id];
  }
  public function getByRef($ref_type, $ref_id) {
    static $list;
    if (!isset($list[$ref_type][$ref_id])) {
      if ($response = $this->podio->request('/task/'.$ref_type.'/'.$ref_id.'/')) {
        $list[$ref_type][$ref_id] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$ref_type][$ref_id];
  }
  public function updatePrivacy($task_id, $private) {
    if ($response = $this->podio->request('/task/'.$task_id.'/private', array('private' => $private), HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
  }
  public function updateText($task_id, $text) {
    if ($response = $this->podio->request('/task/'.$task_id.'/text', array('text' => $text), HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
  }
  public function updateDue($task_id, $due) {
    if ($response = $this->podio->request('/task/'.$task_id.'/due_date', array('due_date' => $due), HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
  }
  public function updateAssign($task_id, $responsible) {
    if ($response = $this->podio->request('/task/'.$task_id.'/assign', array('responsible' => (int)$responsible), HTTP_Request2::METHOD_POST)) {
      return TRUE;
    }
  }
  public function getActive() {
    static $list;
    if (!isset($list)) {
      if ($response = $this->podio->request('/task/active/')) {
        $list = json_decode($response->getBody(), TRUE);
      }
    }
    return $list;
  }
  public function getTotal($space_id = NULL) {
    $url = '/task/total';
    $data = array();
    if ($space_id) {
      $data['space_id'] = $space_id;
    }
    if ($response = $this->podio->request($url, $data)) {
      $list = json_decode($response->getBody(), TRUE);
    }
    return $list;
  }
  public function getStarted() {
    static $list;
    if (!isset($list)) {
      if ($response = $this->podio->request('/task/started/')) {
        $list = json_decode($response->getBody(), TRUE);
      }
    }
    return $list;
  }
  public function getCompleted() {
    static $list;
    if (!isset($list)) {
      if ($response = $this->podio->request('/task/completed/')) {
        $list = json_decode($response->getBody(), TRUE);
      }
    }
    return $list;
  }
  public function getAssignedActive() {
    static $list;
    if (!isset($list)) {
      if ($response = $this->podio->request('/task/assigned/active/')) {
        $list = json_decode($response->getBody(), TRUE);
      }
    }
    return $list;
  }
  public function getAssignedCompleted() {
    static $list;
    if (!isset($list)) {
      if ($response = $this->podio->request('/task/assigned/completed/')) {
        $list = json_decode($response->getBody(), TRUE);
      }
    }
    return $list;
  }
  public function getBySpace($space_id, $sort_by = 'due_date') {
    static $list;
    if (!isset($list[$space_id])) {
      if ($response = $this->podio->request('/task/in_space/'.$space_id.'/', array('sort_by' => $sort_by))) {
        $list[$space_id] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$space_id];
  }
  public function complete($task_id) {
    $this->podio->request('/task/'.$task_id.'/complete', array(), HTTP_Request2::METHOD_POST);
  }
  public function incomplete($task_id) {
    $this->podio->request('/task/'.$task_id.'/incomplete', array(), HTTP_Request2::METHOD_POST);
  }
  public function start($task_id) {
    $this->podio->request('/task/'.$task_id.'/start', array(), HTTP_Request2::METHOD_POST);
  }
  public function stop($task_id) {
    $this->podio->request('/task/'.$task_id.'/stop', array(), HTTP_Request2::METHOD_POST);
  }
}

