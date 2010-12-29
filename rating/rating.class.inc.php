<?php

class RatingAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  public function create($item_id, $type, $data, $user_id, $ref_type = 'item') {
    if ($response = $this->podio->request('/rating/'.$ref_type.'/'.$item_id.'/'.$type, $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getRatings($item_id) {
    static $list;
    
    $key = $item_id;
    if (!isset($list[$key])) {
      if ($item_id > 0 && $response = $this->podio->request('/rating/item/'.$item_id)) {
        $ratings = json_decode($response->getBody(), TRUE);
        $list[$key] = $ratings;
      }
    }
    return $list[$key];
  }
  public function get($item_id, $type, $user_id = NULL) {
    static $list;

    $url = '/rating/item/'.$type.'/'.$item_id;
    if ($user_id) {
      $url .= '/'.$user_id;
    }
    
    if (!isset($list[$url])) {
      if ($item_id > 0 && $response = $this->podio->request($url)) {
        $value = json_decode($response->getBody(), TRUE);
        if (is_array($value)) {
          $list[$url] = $value;
        } else {
          $list[$url] = FALSE;
        }
      }
    }
    return $list[$url];
  }
  public function delete2($item_id, $type, $user_id, $ref_type = 'item') {
    if ($item_id > 0 && $response = $this->podio->request('/rating/' . $ref_type . '/'.$item_id.'/'.$type, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }

  public function delete($item_id, $type, $user_id) {
    if ($item_id > 0 && $response = $this->podio->request('/rating/item/'.$item_id.'/'.$type, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
}

