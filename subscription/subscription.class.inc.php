<?php

class SubscriptionAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  public function create($ref_type, $ref_id) {
    if ($response = $this->podio->request('/subscription/'.$ref_type.'/'.$ref_id, array('frequency' => 'immediately'), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function delete($ref_type, $ref_id) {
    if ($response = $this->podio->request('/subscription/'.$ref_type.'/'.$ref_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  public function deleteByID($subscription_id) {
    if ($response = $this->podio->request('/subscription/'.$subscription_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  public function getById($subscription_id) {
    $response = $this->podio->request('/subscription/'.$subscription_id);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

