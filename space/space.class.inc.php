<?php

class SpaceAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  public function getMembersTop($space_id) {
    if ($response = $this->podio->request('/space/'.$space_id.'/member/top')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getTop() {
    if ($response = $this->podio->request('/space/top/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  public function create($org_id, $name, $post_on_new_app = TRUE, $post_on_new_member = TRUE) {
    $data = array(
      'org_id' => $org_id,
      'name' => $name,
      'post_on_new_app' => $post_on_new_app,
      'post_on_new_member' => $post_on_new_member,
    );
    
    if ($response = $this->podio->request('/space/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function update($space_id, $data) {
    if ($response = $this->podio->request('/space/'.$space_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function delete($space_id) {
    if ($response = $this->podio->request('/space/'.$space_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  public function get($space_id) {
    static $list;
    
    if (!$space_id) {
      return FALSE;
    }
    
    // xdebug_print_function_stack();
    
    if (!$list[$space_id]) {
      if ($response = $this->podio->request('/space/'.$space_id)) {
        $list[$space_id] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$space_id];
  }
  public function getShared($user_id) {
    if ($response = $this->podio->request('/org/shared/'.$user_id)) {
      return json_decode($response->getBody(), TRUE);
    }
    
  }
  public function createMember($space_id, $data = array()) {
    if ($response = $this->podio->request('/space/'.$space_id.'/add', $data, HTTP_Request2::METHOD_POST)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
    return FALSE;
  }
  public function deleteMember($space_id, $user_id) {
    if ($space_id > 0 && $user_id > 0 && $response = $this->podio->request('/space/'.$space_id.'/member/'.$user_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  public function updateMember($space_id, $user_id, $role) {
    if ($response = $this->podio->request('/space/'.$space_id.'/member/'.$user_id, array('role' => $role), HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getMember($space_id, $user_id) {
    static $list;
    $key = $space_id.'_'.$user_id;
    
    if (!$space_id) {
      return FALSE;
    }
    
    if (!isset($list[$key])) {
      if ($response = $this->podio->request('/space/'.$space_id.'/member/'.$user_id)) {
        $list[$key] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$key];
  }
  public function getMembers($space_id) {
    static $list;
    if (!$list[$space_id]) {
      if ($response = $this->podio->request('/space/'.$space_id.'/member/')) {
        $list[$space_id] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$space_id];
  }
  public function getMembersEnded($space_id) {
    static $list;
    if (!$list[$space_id]) {
      if ($response = $this->podio->request('/space/'.$space_id.'/member/ended')) {
        $list[$space_id] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$space_id];
  }
  public function getByURL($url) {
    if ($response = $this->podio->request('/space/url', array('url' => $url))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getInvites($space_id) {
    if ($response = $this->podio->request('/space/'.$space_id.'/member/invited')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getInviteByToken($invite_code) {
    if ($response = $this->podio->request('/space/invite/status', array('invite_code' => $invite_code))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function invite($space_id, $role, $subject, $message, $resend, $notify, $users, $mails) {
    $data = array(
      'role' => $role,
      'subject' => $subject,
      'message' => $message,
      'resend' => $resend,
      'notify' => $notify,
      'users' => $users,
      'mails' => $mails,
    );
    if ($response = $this->podio->request('/space/'.$space_id.'/invite', $data, HTTP_Request2::METHOD_POST)) {
      return TRUE;
    }
    return FALSE;
  }
  public function inviteAccept($invite_code) {
    if ($response = $this->podio->request('/space/invite/accept', array('invite_code' => $invite_code), HTTP_Request2::METHOD_POST)) {
      return TRUE;
    }
    return FALSE;
  }
  public function inviteDecline($invite_code) {
    if ($response = $this->podio->request('/space/invite/decline', array('invite_code' => $invite_code), HTTP_Request2::METHOD_POST)) {
      return TRUE;
    }
    return FALSE;
  }
  public function inviteResend($space_id, $user_id, $subject, $message) {
    $data = array(
      'subject' => $subject,
      'message' => $message,
    );
    if ($response = $this->podio->request('/space/'.$space_id.'/member/'.$user_id.'/resend_invite', $data, HTTP_Request2::METHOD_POST)) {
      return TRUE;
    }
    return FALSE;
  }
}

