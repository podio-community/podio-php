<?php

class TagAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  public function create($text, $ref_type = NULL, $ref_id = NULL) {
        $url = '/tag/';

        if (!is_null($ref_id) && !is_null($ref_type)) {
          $url = '/tag/'.$ref_type.'/'.$ref_id.'/';
        }

        $data = array($text);

        if ($response = $this->podio->request($url, $data, HTTP_Request2::METHOD_POST)) {
          return json_decode($response->getBody(), TRUE);
        }
  }

  public function remove($text, $ref_type, $ref_id) {
        $url = '/tag/'.$ref_type.'/'.$ref_id.'/';

        $data = array('text' => $text);
        
        if ($response = $this->podio->request($url, $data, HTTP_Request2::METHOD_DELETE)) {
          return json_decode($response->getBody(), TRUE);
        }
  }

  public function getByApp($app_id) {
        if ($response = $this->podio->request('/tag/app/'.$app_id . '/')) {
            return json_decode($response->getBody(), TRUE);
        }
  }

  public function getBySpace($space_id) {
        if ($response = $this->podio->request('/tag/space/'.$space_id . '/')) {
            return json_decode($response->getBody(), TRUE);
        }
  }    

  public function getBySpaceWithText($space_id, $text) {
        // $logger = &Log::singleton('error_log', '', 'HTTP_REQUEST');
        // $logger->log('*** getTagsForSpaceWithText ARGUMENTS *** '.print_r($text, true));

        $data = array('text' => $text);
        $url = '/tag/space/'.$space_id . '/search/';
        
        if ($response = $this->podio->request($url, $data)) {
            $response = json_decode($response->getBody(), TRUE);

            // $logger->log('*** getTagsForSpaceWithText RESPONSE: *** '.print_r($response, true));

            return $response;
        }
  }

}

