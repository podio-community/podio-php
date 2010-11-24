<?php

class FilterAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  public function create($app_id, $name, $sort_by, $sort_desc = FALSE, $filters = array()) {
    $data = array('name' => $name, 'sort_by' => $sort_by, 'sort_desc' => $sort_desc, 'filters' => $filters);
    if ($response = $this->podio->request('/filter/app/'.$app_id.'/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function get($filter_id) {
    if ($response = $this->podio->request('/filter/'.$filter_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getLast($app_id) {
    static $list;
    if (!isset($list[$app_id])) {
      if ($response = $this->podio->request('/filter/app/'.$app_id.'/last')) {
        $list[$app_id] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$app_id];
  }
  public function getForApp($app_id) {
    if ($response = $this->podio->request('/filter/app/'.$app_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function delete($filter_id) {
    if ($response = $this->podio->request('/filter/'.$filter_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
}

