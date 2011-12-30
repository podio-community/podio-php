<?php

/**
 * Many objects can be rated including items and status messages. Ratings can 
 * be on of the following types:
 *
 * - approved: Signals that the user approves (0) or disapproves(1)
 * - rsvp: Indicates that the user can attend (0), cannot attend (1) 
 *         or can maybe attend (2)
 * - fivestar: A rating from 1-5 where 5 is the best
 * - yesno: Signals the user says yes (0) or no (1)
 * - thumbs: Signals a thumbs up (0) or thumbs down (1)
 * - like: Signals the user likes the item (1)
 *
 * For items, the different types, except like, can be turned on or off on the 
 * app configuration. Status messages only support the like rating.
 */
class PodioRating {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Add a new rating of the user to the object. The rating can be one of many 
   * different types. For more details see the area.
   */
  public function create($ref_type, $ref_id, $rating_type, $attributes = array()) {
    if ($response = $this->podio->post('/rating/'.$ref_type.'/'.$ref_id.'/'.$rating_type, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the ratings for the given object. It will only return the 
   * ratings that are enabled for the object.
   */
  public function getAllRatings($ref_type, $ref_id) {
    if ($response = $this->podio->get('/rating/'.$ref_type.'/'.$ref_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the rating value for the given rating type and object.
   */
  public function getRatings($ref_type, $ref_id, $rating_type) {
    if ($response = $this->podio->get('/rating/'.$ref_type.'/'.$ref_id.'/'.$rating_type)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the rating value for the given rating type, object and user.
   */
  public function get($ref_type, $ref_id, $rating_type, $user_id) {
    if ($response = $this->podio->get('/rating/'.$ref_type.'/'.$ref_id.'/'.$rating_type.'/'.$user_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the active users rating value for the given rating type and object.
   */
  public function getOwn($ref_type, $ref_id, $rating_type) {
    if ($response = $this->podio->get('/rating/'.$ref_type.'/'.$ref_id.'/'.$rating_type.'/self')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Removes a previous rating of the given type by the user of the 
   * specified object.
   */
  public function delete($ref_type, $ref_id, $rating_type) {
    if ($response = $this->podio->delete('/rating/'.$ref_type.'/'.$ref_id.'/'.$rating_type)) {
      return TRUE;
    }
  }
}
