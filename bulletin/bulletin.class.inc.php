<?php
class BulletinAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  public function getAll() {
    if ($response = $this->podio->request('/bulletin/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  public function get($id) {
    if ($response = $this->podio->request('/bulletin/'.$id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  public function create($title, $summary, $text) {
    $data = array('title'=>$title, 'summary'=>$summary, 'text'=>$text);
    if ($response = $this->podio->request('/bulletin/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  public function update($id, $title, $summary, $text) {
    $data = array('title'=>$title, 'summary'=>$summary, 'text'=>$text);
    if ($response = $this->podio->request('/bulletin/'.$id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
}