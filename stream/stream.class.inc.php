<?php

class StreamAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  public function get($type = 'global', $ref_id = NULL, $limit = 20, $offset = 0) {
    $url = '/stream/';
    if ($type != 'global') {
      $url = '/stream/'.$type.'/'.$ref_id.'/';
    }

    if ($response = $this->podio->request($url, array('limit' => $limit, 'offset' => $offset))) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  public function getObject($ref_type = 'status', $ref_id = NULL) {
    $url = '/stream/'.$ref_type.'/'.$ref_id ;

    if ($response = $this->podio->request($url)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

