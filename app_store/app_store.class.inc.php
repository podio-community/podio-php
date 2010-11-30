<?php

class AppStoreAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  public function getTopApps($locale, $limit = 5) {
    if ($response = $this->podio->request('/app_store/top/'.$locale.'/', array('limit' => $limit, 'offset' => 0))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getAppsByCategory($locale, $category, $limit = 100, $offset = 0) {
    if ($response = $this->podio->request('/app_store/'.$category.'/'.$locale.'/', array('limit' => $limit, 'offset' => $offset))) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  public function getTopAppsV2($locale, $limit = 5) {
    if ($response = $this->podio->request('/app_store/top/'.$locale.'/v2/', array('limit' => $limit, 'offset' => 0))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getAppsByCategoryV2($locale, $category, $limit = 100, $offset = 0) {
    if ($response = $this->podio->request('/app_store/'.$category.'/'.$locale.'/v2/', array('limit' => $limit, 'offset' => $offset))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getCategories() {
    static $list;
    if (!$list) {
      if ($response = $this->podio->request('/app_store/category/')) {
        $list = json_decode($response->getBody(), TRUE);
      }
    }
    return $list;
  }
  public function getOwn() {
    if ($response = $this->podio->request('/app_store/own/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getAppsByAuthor($locale, $author, $limit = 100) {
    if ($response = $this->podio->request('/app_store/author/'.$author.'/'.$locale.'/', array('limit' => $limit, 'offset' => 0))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function getProfile($user_id) {
    if ($response = $this->podio->request('/app_store/author/'.$user_id.'/profile')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function install($app_id, $space_id) {
    if ($response = $this->podio->request('/app_store/'.$app_id.'/install', array('space_id' => $space_id), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  public function installV2($app_id, $space_id) {
    if ($response = $this->podio->request('/app_store/'.$app_id.'/install/v2', array('space_id' => $space_id), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function get($app_id) {
    if ($response = $this->podio->request('/app_store/'.$app_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  public function getSharedApp($share_id) {
    if ($response = $this->podio->request('/app_store/'.$share_id . '/v2')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
    
  public function shareApp($app_id, $abstract, $description, $language, $category_ids, $file_ids) {
    if ($response = $this->podio->request('/app_store/', array('app_id' => $app_id, 'abstract' => $abstract, 'description' => $description, 'language' => $language, 'category_ids'=> $category_ids, 'file_ids' => $file_ids ), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function update($share_id, $abstract, $description, $language, $category_ids, $file_ids) {
    if ($response = $this->podio->request('/app_store/'.$share_id, array('abstract' => $abstract, 'description' => $description, 'language' => $language, 'category_ids'=> $category_ids, 'file_ids' => $file_ids ), HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  public function unshare($share_id) {
    if ($response = $this->podio->request('/app_store/'.$share_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }

  public function getShareDependencies($share_id) {
    if ($response = $this->podio->request('/app_store/'.$share_id.'/dependencies/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  public function getFeaturedApp($language) {
    if ($response = $this->podio->request('/app_store/featured/'.$language)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

}

