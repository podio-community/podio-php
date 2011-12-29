<?php

/**
 * Tags are words or short sentences that are used as metadata for objects. 
 * For a more detailed explanation, see this wikipedia article: 
 * http://en.wikipedia.org/wiki/Tag_(metadata)
 * 
 * Podio supports tags on statuses and items and tags that include spaces.
 */
class PodioTag {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Add a new set of tags to the object. If a tag with the same text is 
   * already present, the tag will be ignored.
   */
  public function create($ref_type, $ref_id, $tags) {
    if ($response = $this->podio->post('/tag/'.$ref_type.'/'.$ref_id.'/', $tags)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the tags on the given object.
   */
  public function update($ref_type, $ref_id, $tags = array()) {
    if ($response = $this->podio->put('/tag/'.$ref_type.'/'.$ref_id.'/', $tags)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Removes a single tag from an object.
   */
  public function remove($ref_type, $ref_id, $attributes) {
    if ($response = $this->podio->delete('/tag/'.$ref_type.'/'.$ref_id.'/', $attributes)) {
      return TRUE;
    }
  }

  /**
   * Returns the tags on the given app. This includes only items. The tags 
   * are first limited ordered by their frequency of use, and then 
   * returned sorted alphabetically.
   */
  public function getByApp($app_id, $attributes) {
    if ($response = $this->podio->get('/tag/app/'.$app_id . '/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the top tags on the app.
   */
  public function getTopByApp($app_id, $attributes) {
    if ($response = $this->podio->get('/tag/app/'.$app_id . '/top/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the tags on the given space. This includes both items and 
   * statuses. The tags are ordered firstly by the number of uses, 
   * secondly by the tag text.
   */
  public function getBySpace($space_id) {
    if ($response = $this->podio->get('/tag/space/'.$space_id . '/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }    

  /**
   * Returns the tags on the given org. This includes both items and 
   * statuses. The tags are ordered firstly by the number of uses, 
   * secondly by the tag text.
   */
  public function getByOrg($org_id) {
    if ($response = $this->podio->get('/tag/org/'.$org_id . '/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }    

  /**
   * Returns the objects that are tagged with the given text on the space. 
   * The objects are returned sorted descending by the time the tag 
   * was added.
   */
  public function getBySpaceWithText($space_id, $attributes) {
    if ($response = $this->podio->get('/tag/space/'.$space_id . '/search/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the objects that are tagged with the given text on the app. 
   * The objects are returned sorted descending by the time the tag 
   * was added.
   */
  public function getByAppWithText($app_id, $attributes) {
    if ($response = $this->podio->get('/tag/app/'.$app_id . '/search/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the objects that are tagged with the given text on the org. 
   * The objects are returned sorted descending by the time the tag 
   * was added.
   */
  public function getByOrgWithText($org_id, $attributes) {
    if ($response = $this->podio->get('/tag/app/'.$org_id . '/search/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
