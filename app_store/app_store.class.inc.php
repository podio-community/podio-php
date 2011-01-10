<?php

/**
 * Apps can be shared to the app store so it can be installed and used by 
 * anyone. From the app store the apps can be browsed by top apps, 
 * category and author. From here it can also be installed into a space 
 * the user is administrator on.
 */
class PodioAppStoreAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Returns the top apps in the app store in the given language.
   *
   * @param $locale The language of the shares to return. English apps will 
   *                always be returned.
   * @param $type The type of share to return, either "app", "pack" or 
   *              leave out for both.
   * @param $limit The maximum number of apps to return. Defaults to 5
   * @param $offset The offset to used when returning the apps.
   *
   * @return Array of shares
   */
  public function getTopApps($locale, $type = '', $limit = 5, $offset = 0) {
    if ($response = $this->podio->request('/app_store/top/v2/', array('type' => $type, 'language' => $locale, 'limit' => $limit, 'offset' => $offset))) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the apps in the app store in the given category and language.
   *
   * @param $category_id Id of the category to get apps for
   * @param $locale The language of the shares to return. English apps will 
   *                always be returned.
   * @param $sort The sorting of the shares, either "install", "rating" or 
   *              "name". Defaults to "name".
   * @param $type The type of share to return, either "app", "pack" or 
   *              leave out for both.
   * @param $limit The maximum number of apps to return. Defaults to 30
   * @param $offset The offset to used when returning the apps.
   *
   * @return Array of shares
   */
  public function getByCategory($category_id, $locale, $sort = 'name', $type = '', $limit = 30, $offset = 0) {
    if ($response = $this->podio->request('/app_store/category/' . $category_id . '/', array('language' => $locale, 'type' => $type, 'limit' => $limit, 'offset' => $offset, 'sort' => $sort))) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the shares of the given object. The active users shares will be 
   * first followed by other users shares. Besides that the shares will be 
   * sorted descending by when they were shared.
   * 
   * @param $ref_type Type of reference. "space" or "app"
   * @param $ref_id Space id or app id
   *
   * @return Array of shares
   */
  public function getByReference($ref_type, $ref_id) {
    if ($response = $this->podio->request('/app_store/' . $ref_type . '/' . $ref_id . '/', array())) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Searches the app store for apps with the given language and texts.
   *
   * @param $words Comma-separated list of texts to search for
   * @param $locale The language of the shares to return. English apps will 
   *                always be returned.
   * @param $sort The sorting of the shares, either "install", "rating" or 
   *              "name". Defaults to "name".
   * @param $type The type of share to return, either "app", "pack" or 
   *              leave out for both.
   * @param $limit The maximum number of apps to return. Defaults to 30
   * @param $offset The offset to used when returning the apps.
   *
   * @return Array of shares
   */
  public function search($words, $locale, $sort = 'name', $type = '', $limit = 30, $offset = 0) {
    if ($response = $this->podio->request('/app_store/search/', array('texts' => $words, 'language' => $locale, 'sort' => $sort, 'type' => $type, 'limit' => $limit, 'offset' => $offset))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the categories available in the system.
   *
   * @param $language If specified, only categories with shares in the given 
   *                  language will be returned
   *
   * @return Array of verticals and function categories
   */
  public function getCategories($language = NULL) {
    $data = array();
    if ($language) {
      $data = array('language' => $language);
    }
    if ($response = $this->podio->request('/app_store/category/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the apps that the active user has shared.
   *
   * @param $type The type of shares to return, either "app" or "pack", leave 
   *              out for all shares
   * @param $limit The maximum number of shares to return
   * @param $offset The offset into the list of shares
   *
   * @return Array of shares
   */
  public function getOwn($type = NULL, $limit = 99, $offset = 0) {
    $data = array('limit' => $limit, 'offset' => $offset);
    if ($type) {
      $data['type'] = $type;
    }
    if ($response = $this->podio->request('/app_store/own/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the profile for the app store author.
   */
  public function getProfile() {
    if ($response = $this->podio->request('/app_store/author/' . $user_id . '/profile')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Installs the share with the given id on the space.
   *
   * @param $share_id Id of the share to install
   * @param $space_id The id of the space the shared app should be installed to
   * @param $dependencies The list of ids of the dependent shares that should 
   *                      also be installed, if not already present
   */
  public function install($share_id, $space_id, $dependencies) {
    $response = $this->podio->request('/app_store/' . $share_id . '/install/v2', array('space_id' => $space_id, 'dependencies' => $dependencies), HTTP_Request2::METHOD_POST);
    if ($response->getStatus() == '204') {
      return TRUE;
    }
    return json_decode($response->getBody(), TRUE);
  }

  /**
   * Returns the shared app from the app store with the given id. It will 
   * also return all comments and fivestar ratings of the app.
   *
   * @param $share_id The id of the share to retrieve
   *
   * @return A single share object
   */
  public function get($share_id) {
    if ($response = $this->podio->request('/app_store/' . $share_id . '/v2')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Shares the app in the app store
   *
   * @param $app_id Id of the app to share
   * @param $abstract The abstract of the app
   * @param $description The description of the app
   * @param $language The language the app is written in
   * @param $category_ids The ids of the categories the app should be placed in
   * @param $file_ids The file ids to use as screenshots for the app
   * @param $features Array of features to enable
   * @param $children Array of ids of the child shares that should be included
   *
   * @return Array with the new share id
   */
  public function shareApp($app_id, $abstract, $description, $language, $category_ids, $file_ids, $features, $children = array()) {
    $request_data = array('ref_id' => $app_id, 'ref_type' => 'app', 'abstract' => $abstract, 'description' => $description, 'language' => $language, 'category_ids' => $category_ids, 'file_ids' => $file_ids, 'children' => $children , 'features' => array());
    if ($features) {
      $request_data['features'] = $features;
    }
    if ($response = $this->podio->request('/app_store/', $request_data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Shares the pack in the app store
   *
   * @param $space_id Id of the space to share
   * @param $name The name of the share
   * @param $abstract The abstract of the pack
   * @param $description The description of the pack
   * @param $language The language the pack is written in
   * @param $category_ids The ids of the categories the pack should be placed in
   * @param $file_ids The file ids to use as screenshots for the pack
   * @param $features Array of features to enable
   * @param $children Array of ids of the child shares that should be included
   *
   * @return Array with the new share id
   */
  public function sharePack($space_id, $name, $abstract, $description, $language, $category_ids, $file_ids, $features, $children = array()) {
    $request_data = array('ref_id' => $space_id, 'ref_type' => 'space', 'name' => $name, 'abstract' => $abstract, 'description' => $description, 'language' => $language, 'category_ids' => $category_ids, 'file_ids' => $file_ids, 'children' => $children , 'features' => array());
    if ($features) {
      $request_data['features'] = $features;
    }
    if ($response = $this->podio->request('/app_store/', $request_data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Updates the share with changes to abstract, description, etc.
   *
   * @param $share_id Id of the share to update
   * @param $abstract The new abstract
   * @param $description The new description
   * @param $language The new language
   * @param $category_ids The ids of the categories the app should be placed in
   * @param $file_ids The file ids to use as screenshots for the app
   * @param $name The name of the share, only valid if the share is a space
   */
  public function update($share_id, $abstract, $description, $language, $category_ids, $file_ids, $name = NULL) {
    $data = array('abstract' => $abstract, 'description' => $description, 'language' => $language, 'category_ids' => $category_ids, 'file_ids' => $file_ids);
    if ($name) {
      $data['name'] = $name;
    }
    if ($response = $this->podio->request('/app_store/' . $share_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Unshares the given app from the app store
   * 
   * @param $share_id The id of the share to unpublish
   */
  public function unshare($share_id) {
    if ($response = $this->podio->request('/app_store/' . $share_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }

  /**
   * Returns a random featured app with the given language.
   *
   * @param $language The language of the shares to return. English apps will 
   *                  always be returned.
   * @param $type The type of share to return, either "app", "pack" or leave 
   *              out for both.
   */
  public function getFeatured($language, $type = '') {
    if ($response = $this->podio->request('/app_store/featured', array('language' => $language, 'type' => $type))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

