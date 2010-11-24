<?php

class WidgetAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  public function create($ref_type, $ref_id, $type, $title, $config) {
    $data = array('type' => $type, 'title' => $title, 'config' => $config);
    if ($response = $this->podio->request('/widget/'.$ref_type.'/'.$ref_id.'/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function update($widget_id, $title, $config) {
    $data = array('title' => $title, 'config' => $config);
    if ($response = $this->podio->request('/widget/'.$widget_id, $data, HTTP_Request2::METHOD_PUT)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  public function get($widget_id) {
    if ($response = $this->podio->request('/widget/'.$widget_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getForDisplay($ref_type, $ref_id) {
    if ($response = $this->podio->request('/widget/'.$ref_type.'/'.$ref_id.'/display/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function delete($widget_id) {
    if ($response = $this->podio->request('/widget/'.$widget_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  public function updateOrder($ref_type, $ref_id, $list) {
    if ($response = $this->podio->request('/widget/'.$ref_type.'/'.$ref_id.'/order', $list, HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
    return FALSE;
  }
}

