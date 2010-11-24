<?php

class ItemAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  public function getFieldCount($field_id) {
    if ($response = $this->podio->request('/item/field/'.$field_id.'/count')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getValues($app_id) {
    if ($response = $this->podio->request('/item/app/'.$app_id.'/values')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getActivity($app_id) {
    if ($response = $this->podio->request('/item/app/'.$app_id.'/activity')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function searchField($field_id, $string) {
    if ($response = $this->podio->request('/item/field/'.$field_id.'/find', array('text' => $string))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function get($item_id, $reset = FALSE) {
    static $list;
    
    if (!$item_id) {
      return FALSE;
    }

    if ($reset == TRUE) {
      unset($list[$item_id]);
    }
    
    if (!isset($list[$item_id])) {
      if ($response = $this->podio->request('/item/'.$item_id)) {
        $list[$item_id] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$item_id];
  }
  public function getPrevious($item_id) {
    if ($response = $this->podio->request('/item/'.$item_id.'/previous')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getNext($item_id) {
    if ($response = $this->podio->request('/item/'.$item_id.'/next')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  public function getItems($app_id, $data = array()) {
    static $list;

    $key = serialize($data);
    if (!isset($list[$key])) {
      if ($response = $this->podio->request('/item/app/'.$app_id.'/', $data)) {
        $list[$key] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$key];
  }

  public function getItemsV2($app_id, $limit, $offset, $sort_by, $sort_desc, $filters = array()) {

    // Change filter structure for GET request.
    $data = array('limit' => $limit, 'offset' => $offset, 'sort_by' => $sort_by, 'sort_desc' => $sort_desc);
    foreach ($filters as $filter) {
      if (is_array($filter['values'])) {
        if (isset($filter['values']['from'])) {
          $data[$filter['key']] = $filter['values']['from'].'-'.$filter['values']['to'];
        }
        else {
          $data[$filter['key']] = implode(';', $filter['values']);
        }
      }
    }

    if ($response = $this->podio->request('/item/app/'.$app_id.'/v2/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  public function getRevisions($item_id) {
    static $list;
    
    if (!$item_id) {
      return FALSE;
    }

    $key = $item_id;
    if (!isset($list[$key])) {
      if ($response = $this->podio->request('/item/'.$item_id.'/revision')) {
        $list[$key] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$key];
  }

  public function getRevisionDiff($item_id, $from, $to) {
    static $list;
    $key = $item_id . '|' . $from . '|' . $to;
    if (!isset($list[$key])) {
      if ($response = $this->podio->request('/item/'.$item_id.'/revision/'.$from.'/'.$to)) {
        $list[$key] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$key];
  }


  public function create($data, $user_id) {
    $app_id = $data['app_id'];
    unset($data['app_id']);
    if ($response = $this->podio->request('/item/app/'.$app_id.'/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function update($item_id, $data, $user_id) {
    if ($response = $this->podio->request('/item/'.$item_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function delete($item_id, $user_id) {
    if ($response = $this->podio->request('/item/'.$item_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  public function updateFieldValue($item_id, $field_id, $data) {
    if ($response = $this->podio->request('/item/'.$item_id.'/value/'.$field_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

