<?php

class UserAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  public function get($uid) {
    static $list;
    if ($uid == 0 || $uid == 1) {
      
      // $logger = &Log::singleton('error_log', '', 'HTTP_REQUEST');
      // $logger->log('Trying to load anon or root user');
      
      return FALSE;
    }

    if (!isset($list[$uid])) {
      if ($response = $this->podio->request('/user/'.$uid)) {
        $list[$uid] = json_decode($response->getBody(), TRUE);
      }
    }
    return $list[$uid];
  }

  public function update($user_id, $mail, $old_password, $new_password, $locale, $timezone) {
    
    $data = array(
      'mail' => $mail, 
      'old_password' => $old_password,
      'new_password' => $new_password, 
      'locale' => $locale,
      'timezone' => $timezone,
    );
    if (!$old_password) {
      unset($data['old_password']);
      unset($data['new_password']);
      unset($data['mail']);
    }
    
    $response = $this->podio->request('/user/'.$user_id, $data, HTTP_Request2::METHOD_PUT);
    if ($response && $response->getStatus() == '204') {
      return TRUE;
    }
    return FALSE;
  }
  
  public function getOwnProfile() {
    static $list;
    if (!$list) {
      if ($response = $this->podio->request('/user/profile/')) {
        $list = json_decode($response->getBody(), TRUE);
      }
    }
    return $list;
  }

  public function getStatus() {
    static $list;
    if (!$list) {
      if ($response = $this->podio->request('/user/status')) {
        $list = json_decode($response->getBody(), TRUE);
      }
    }
    return $list;
  }
  
  public function getWithProfile($uid, $reset = FALSE) {
    static $list;
    
    if ($uid == 0) {
      return FALSE;
    }
    
    if ($reset == TRUE) {
      unset($list[$uid]);
    }
    
    if (!isset($list[$uid])) {
      $user = array('user_id' => $uid);
      if ($response = $this->podio->request('/contact/'.$uid)) {
        $user['profile'] = json_decode($response->getBody(), TRUE);
        if ($user['profile']) {
          $list[$uid] = $user;
        }
        else {
          return FALSE;
        }
      }
    }
    return $list[$uid];
  }

  public function setProperty($name, $value) {
    if ($response = $this->podio->request('/user/property/'.$name, array('value' => (bool)$value), HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function deleteProperty($name) {
    if ($response = $this->podio->request('/user/property/'.$name, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  public function updateProfile($data = array()) {
    if ($response = $this->podio->request('/user/profile/', $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  public function updateProfileField($profile_id, $field, $value) {
    if ($response = $this->podio->request('/user/profile/'.$field, $value, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  public function create($name, $mail, $password, $locale, $timezone) {
    $data =  array('name' => $name, 'mail' => $mail, 'password' => $password, 'locale' => $locale, 'timezone' => $timezone);
    $data['token'] = $this->frontend_token;
        
    if ($response = $this->podio->request('/user/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function recoverPassword($mail) {
    if ($response = $this->podio->request('/user/recover_password', array('mail' => $mail, 'token' => $this->frontend_token), HTTP_Request2::METHOD_POST)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  public function resetPassword($password, $recovery_code) {
    if ($response = $this->podio->request('/user/reset_password', array('password' => $password, 'recovery_code' => $recovery_code, 'token' => $this->frontend_token), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function activate($activation_code, $mail, $password, $name) {
    $data = array(
      'activation_code' => $activation_code,
      'mail' => $mail,
      'password' => $password,
      'name' => $name,
    );
    $data['token'] = $this->frontend_token;
    if ($response = $this->podio->request('/user/activate_user', $data, HTTP_Request2::METHOD_POST)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
    return FALSE;
  }
  

}

