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
class PodioRatingAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Add a new rating of the user to the object. The rating can be one of many 
   * different types. For more details see the area.
   *
   * @param $ref_type The type of reference. E.g. "item", "status" or "share"
   * @param $ref_id The id of the reference
   * @param $rating_type The type of rating. 
   * @param $value The value of the rating. Depends on the rating type
   *
   * @return Array with the new rating id
   */
  public function create($ref_type, $ref_id, $rating_type, $value) {
    if ($response = $this->podio->request('/rating/'.$ref_type.'/'.$ref_id.'/'.$rating_type, $value, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the ratings for the given object. It will only return the 
   * ratings that are enabled for the object.
   *
   * @param $ref_type The type of reference. E.g. "item", "status" or "share"
   * @param $ref_id The id of the reference
   *
   * @return Array of ratings
   */
  public function getRatings($ref_type, $ref_id) {
    static $list;
    
    $key = $ref_id;
    if (!isset($list[$key])) {
      if ($ref_id > 0 && $response = $this->podio->request('/rating/'.$ref_type.'/'.$ref_id)) {
        $ratings = json_decode($response->getBody(), TRUE);
        $list[$key] = $ratings;
      }
    }
    return $list[$key];
  }
  
  /**
   * Returns the rating value for the given rating type, object and user.
   *
   * @param $ref_type The type of reference. E.g. "item", "status" or "share"
   * @param $ref_id The id of the reference
   * @param $rating_type The type of rating.
   * @param $user_id The user to get rating for. Defaults to current user
   *
   * @return A single rating value
   */
  public function get($ref_type, $ref_id, $rating_type, $user_id = NULL) {
    static $list;

    $url = '/rating/'.$ref_type.'/'.$ref_id.'/'.$rating_type;
    if ($user_id) {
      $url .= '/'.$user_id;
    }
    
    if (!isset($list[$url])) {
      if ($ref_id > 0 && $response = $this->podio->request($url)) {
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

  /**
   * Removes a previous rating of the given type by the user of the 
   * specified object.
   *
   * @param $ref_type The type of reference. E.g. "item", "status" or "share"
   * @param $ref_id The id of the reference
   * @param $rating_type The type of rating.
   */
  public function delete($ref_type, $ref_id, $rating_type) {
    if ($ref_id > 0 && $response = $this->podio->request('/rating/'.$ref_type.'/'.$ref_id.'/'.$rating_type, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
}

