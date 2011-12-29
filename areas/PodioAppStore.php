<?php

/**
 * Apps can be shared to the app store so it can be installed and used by 
 * anyone. From the app store the apps can be browsed by top apps, 
 * category and author. From here it can also be installed into a space 
 * the user is administrator on.
 */
class PodioAppStore {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Returns the top apps in the app store in the given language.
   */
  public function getTopApps($attributes) {
    if ($response = $this->podio->get('/app_store/top/v2/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the apps in the app store in the given category and language.
   */
  public function getByCategory($category_id, $attributes) {
    if ($response = $this->podio->get('/app_store/category/'.$category_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the shares of the given object. The active users shares will be 
   * first followed by other users shares. Besides that the shares will be 
   * sorted descending by when they were shared.
   */
  public function getByReference($ref_type, $ref_id) {
    if ($response = $this->podio->get('/app_store/'.$ref_type.'/'.$ref_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the shares the organization with the give URL has shared in the app store.
   */
  public function getByOrganization($organization_url, $attributes) {
    if ($response = $this->podio->get('/app_store/org/'.$organization_url.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the shares the organization with the given id has shared in the private app store
   */
  public function getPrivateByOrganization($organization_id, $attributes) {
    if ($response = $this->podio->get('/app_store/org/'.$organization_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Searches the app store for apps with the given language and texts.
   */
  public function search($attributes) {
    if ($response = $this->podio->get('/app_store/search/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the categories available in the system.
   */
  public function getCategories($attributes) {
    if ($response = $this->podio->get('/app_store/category/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the apps that the active user has shared.
   */
  public function getOwn($attributes) {
    if ($response = $this->podio->get('/app_store/own/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Installs the share with the given id on the space.
   */
  public function install($share_id, $attributes) {
    if ($response = $this->podio->post('/app_store/'.$share_id.'/install/v2', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the shared app from the app store with the given id. It will 
   * also return all comments and fivestar ratings of the app.
   */
  public function get($share_id) {
    if ($response = $this->podio->get('/app_store/'.$share_id.'/v2')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Shares the app/pack in the app store
   */
  public function share($attributes) {
    if ($response = $this->podio->post('/app_store/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Updates the share with changes to abstract, description, etc.
   */
  public function update($share_id, $attributes) {
    if ($response = $this->podio->put('/app_store/'.$share_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Unshares the given app from the app store
   */
  public function unshare($share_id) {
    if ($response = $this->podio->delete('/app_store/'.$share_id)) {
      return TRUE;
    }
  }

  /**
   * Returns a random featured app with the given language.
   */
  public function getFeatured($attributes) {
    if ($response = $this->podio->get('/app_store/featured', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the app store profile of the organization.
   */
  public function getOrganizationProfile($organization_url) {
    if ($response = $this->podio->get('/app_store/org/'.$organization_url.'/profile')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the orgs, that the user is member of, and that has shared private apps.
   */
  public function getOrganizationsWithPrivateShares() {
    if ($response = $this->podio->get('/app_store/org/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
