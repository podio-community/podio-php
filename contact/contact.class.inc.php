<?php

class ContactAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  public function getProfiles($data) {
    static $list;
    if ($data['key'] && !$data['value']) {
      unset($data['key']);
    }
    $key = serialize($data);
    if (!isset($list[$key])) {
      if ($response = $this->podio->request('/contact/', $data)) {
        $list[$key] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$key];
  }
  public function getContactsTotals() {
    static $list;
    if (!isset($list)) {
      if ($response = $this->podio->request('/contact/totals/')) {
        $list = json_decode($response->getBody(), TRUE);
      }
    }
    return $list;
  }

  public function getContact($user_id) {
    if ($response = $this->podio->request('/contact//' . $user_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getTopContacts($limit, $type = 'mini') {
    if ($response = $this->podio->request('/contact/top/', array('limit' => $limit, 'type' => $type))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getContacts($type = 'all', $ref_id = NULL, $format = 'mini', $order = NULL, $limit = NULL) {
    static $list;
    $key = $type . '_' . $ref_id . '_' . $format . '_' . $order . '_' . $limit;
    if ($type != 'all' && !$ref_id) {
      return FALSE;
    }
    
    if ($type == 'space') {
      $url = '/contact/space/'.$ref_id;
    }
    elseif ($type == 'org') {
      $url = '/contact/org/'.$ref_id;
    }
    else {
      $url = '/contact/';
    }

    $requestData = array();
    $requestData['type'] = $format;
    $requestData['order'] = $order;
    $requestData['limit'] = $limit;

    if (!$list[$key]) {
      if ($response = $this->podio->request($url, $requestData)) {
        $list[$key] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$key];
  }

}

