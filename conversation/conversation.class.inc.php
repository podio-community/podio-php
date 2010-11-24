<?php

class ConversationAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  public function get($conversation_id) {
    $response = $this->podio->request('/conversation/'.$conversation_id);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getConversationOnObject($ref_type, $ref_id) {
    $response = $this->podio->request('/conversation/'.$ref_type.'/'.$ref_id.'/');
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function create($data) {
    $url = '/conversation/';
    if ($data['ref_type'] == 'item' || $data['ref_type'] == 'bulletin') {
      $url = '/conversation/'.$data['ref_type'].'/'.$data['ref_id'].'/';
      unset($data['ref_type']);
      unset($data['ref_id']);
    }
    if ($response = $this->podio->request($url, $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function createReply($conversation_id, $data) {
    if ($response = $this->podio->request('/conversation/'.$conversation_id.'/reply', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

