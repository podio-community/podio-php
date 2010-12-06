<?php

class AppAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  public function create($data) {
    if ($response = $this->podio->request('/app/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function update($app_id, $data = array(), $user_id = 0) {
    if ($response = $this->podio->request('/app/'.$app_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getField($app_id, $field_id) {
    if ($response = $this->podio->request('/app/'.$app_id.'/field/'.$field_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function get($app_id) {
    static $list;
    $key = $app_id;
    
    if (!isset($list[$key])) {
      if ($response = $this->podio->request('/app/'.$app_id)) {
        $app = json_decode($response->getBody(), TRUE);
      }
      $list[$key] = $app;
      
    }
    return $list[$key];
  }
  public function getSpaceApps($space_id) {
    static $list;
    if (!isset($list[$space_id])) {
      if ($response = $this->podio->request('/app/space/'.$space_id.'/')) {
        $list[$space_id] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$space_id];
  }
  public function getAvailableApps($space_id) {
    static $list;
    if (!isset($list[$space_id])) {
      if ($response = $this->podio->request('/app/space/'.$space_id.'/available/')) {
        $list[$space_id] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$space_id];
  }
  public function getApps($data, $type = NULL) {
    static $list;
    
    if ($type == 'full' || $type == 'simple') {
      $data['type'] = $type;
    }
    
    if ($data['space_ids'] == '0') {
      return FALSE;
    }

    // if ($data['space_ids'] == '') {
    //   xdebug_print_function_stack();
    //   die();
    // }
    
    // xdebug_print_function_stack();

    
    $key = serialize($data);
    if (!isset($list[$key])) {
      if ($response = $this->podio->request('/app/', $data)) {
        if ($data['type'] == 'full') {
          $logger = &Log::singleton('error_log', '', 'HTTP_REQUEST');
          // $logger->log($key .' '. print_r($data, true));
          // $logger->log(print_r(xdebug_get_function_stack( ), true));
        }
        $apps = json_decode($response->getBody(), TRUE);
        $list[$key] = $apps;
      }
    }
    return $list[$key];
  }
  public function createField($app_id, $data, $user_id) {
    if ($response = $this->podio->request('/app/'.$app_id.'/field/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function updateField($app_id, $field_id, $data, $user_id) {
    if ($response = $this->podio->request('/app/'.$app_id.'/field/'.$field_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function reorder($space_id, $list) {
    if ($response = $this->podio->request('/app/space/'.$space_id.'/order', $list, HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
    return FALSE;
  }
  public function install($app_id, $space_id) {
    if ($response = $this->podio->request('/app/'.$app_id.'/install', array('space_id' => $space_id), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function share($app_id, $language, $category) {
    if ($response = $this->podio->request('/app/'.$app_id.'/share', array('language' => $language, 'category' => $category), HTTP_Request2::METHOD_POST)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
    }
  }
}

