<?php

class SearchAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Searches in all items and statuses. The objects will be returned sorted 
   * descending by the time the object was created.
   *
   * @param $words An array of search strings
   * @param $type The type of search. Can be one of the following:
   * - space: Search only a single space
   * - org: Search an organization
   *
   * Defaults to 'space'
   * @param $ref_id The id of the space or the organization to be searched
   *
   * @return An array of search results
   */
  public function search($words, $type = 'space', $ref_id = NULL) {
    $url = '/search/'.$type.'/'.$ref_id.'/';
    if ($response = $this->podio->request($url, $words, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

