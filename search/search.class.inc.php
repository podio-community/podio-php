<?php

class SearchAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  public function search($words, $type = 'space', $ref_id = NULL) {
    $url = '/search/'.$type.'/'.$ref_id.'/';
    if ($response = $this->podio->request($url, $words, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

