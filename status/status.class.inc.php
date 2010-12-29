<?php

class StatusAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  public function get($status_id) {
    $response = $this->podio->request('/status/'.$status_id);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getLatest($user_id, $space_id) {
    $response = $this->podio->request('/status/user/'.$user_id.'/space/'.$space_id.'/latest/');
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function create($space_id, $message, $alerts, $file_ids) {
    $data = array('space_id' => $space_id, 'value' => $message, 'alerts' => array(), 'file_ids' => array());

    if ($alerts) {
      $data['alerts'] = $alerts;
    }
    if ($file_ids) {
      $data['file_ids'] = $file_ids;
    }
    
    if ($response = $this->podio->request('/status/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function update($status_id, $data) {
    if ($response = $this->podio->request('/status/'.$status_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function delete($status_id) {
    if ($response = $this->podio->request('/status/'.$status_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
}

