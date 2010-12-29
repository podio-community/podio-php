<?php

class OrgAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  public function create($name, $logo = NULL) {
    if ($response = $this->podio->request('/org/', array('name' => $name, 'logo' => $logo), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function update($org_id, $data) {
    if ($response = $this->podio->request('/org/'.$org_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function updateAdmin($org_id, $user_limit, $premium) {
    if ($response = $this->podio->request('/org/'.$org_id.'/admin', array('user_limit' => $user_limit, 'premium' => $premium), HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function delete($org_id) {
    if ($response = $this->podio->request('/org/'.$org_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  public function get($org_id) {
    if ($response = $this->podio->request('/org/'.$org_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  public function getByUrl($url) {
    if ($response = $this->podio->request('/org/url', array('url' => $url))) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  public function getOrgSpaces($org_id) {
    if ($response = $this->podio->request('/org/'.$org_id.'/space')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getOrgs() {
    static $list;
    if (!isset($list)) {
      if ($response = $this->podio->request('/org/')) {
        $list = json_decode($response->getBody(), TRUE);
      }
    }
    return $list;
  }
}

