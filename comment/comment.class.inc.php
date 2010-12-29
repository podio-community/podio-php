<?php

class CommentAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  public function create($item_id, $data, $file_ids, $user_id, $ref_type = 'item') {
    if (!$data['alerts']) {
      $data['alerts'] = array();
    }
    if (count($file_ids) > 0) {
      $data['file_ids'] = $file_ids;
    }
    if ($response = $this->podio->request('/comment/'.$ref_type.'/'.$item_id, $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function update($comment_id, $data, $user_id) {
    if ($response = $this->podio->request('/comment/'.$comment_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function delete($comment_id, $user_id = NULL) {
    if ($response = $this->podio->request('/comment/'.$comment_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  public function getComments($item_id, $ref_type = 'item') {
    static $list;
    
    $key = $item_id;
    if (!isset($list[$key])) {
      if ($item_id > 0 && $response = $this->podio->request('/comment/'.$ref_type.'/'.$item_id)) {
        $comments = json_decode($response->getBody(), TRUE);
        $list[$key] = $comments;
      }
    }
    return $list[$key];
  }
  public function get($comment_id) {
    static $list;
    
    $key = $comment_id;
    if (!isset($list[$key])) {
      if ($response = $this->podio->request('/comment/'.$comment_id)) {
        $comment = json_decode($response->getBody(), TRUE);
        $list[$key] = $comment;
      }
    }
    return $list[$key];
  }
}

