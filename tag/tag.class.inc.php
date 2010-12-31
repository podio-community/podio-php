<?php

class TagAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Add a new set of tags to the object. If a tag with the same text is 
   * already present, the tag will be ignored.
   *
   * @param $text String. The tag to add
   * @param $ref_type The kind of object to tag. "item" or "status"
   * @param $ref_id The item id or status id
   */
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

  /**
   * Removes a single tag from an object.
   *
   * @param $text The tag to delete
   * @param $ref_type The kind of object to remove tag from. "item" or "status"
   * @param $ref_id The item id or status id
   */
  public function remove($text, $ref_type, $ref_id) {
    $url = '/tag/'.$ref_type.'/'.$ref_id.'/';

    $data = array('text' => $text);
    
    if ($response = $this->podio->request($url, $data, HTTP_Request2::METHOD_DELETE)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the tags on the given app. This includes only items. The tags are 
   * ordered firstly by the number of uses, secondly by the tag text.
   *
   * @param $app_id The id of the app to get tags for.
   *
   * @return An array of tag text and usage counts for each tag
   */
  public function getByApp($app_id) {
    if ($response = $this->podio->request('/tag/app/'.$app_id . '/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the tags on the given space. This includes both items and 
   * statuses. The tags are ordered firstly by the number of uses, 
   * secondly by the tag text.
   *
   * @param $space_id The id of the space to get tags for
   *
   * @return An array of tag text and usage counts for each tag
   */
  public function getBySpace($space_id) {
    if ($response = $this->podio->request('/tag/space/'.$space_id . '/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }    

  /**
   * Returns the objects that are tagged with the given text on the space. 
   * The objects are returned sorted descending by the time the tag 
   * was added.
   *
   * @param $space_id The id of the space to get tags for
   * @param $text The tag to search for
   *
   * @return An array of result objects
   */
  public function getBySpaceWithText($space_id, $text) {
    $data = array('text' => $text);
    $url = '/tag/space/'.$space_id . '/search/';
    if ($response = $this->podio->request($url, $data)) {
      $response = json_decode($response->getBody(), TRUE);
      return $response;
    }
  }
}

