<?php

/**
 * Each user have a profile attached, that holds all the personal details of 
 * the user. This includes very basic information like the name and mail 
 * addresses, but can also include more advanced fields like billing address 
 * and IM addresses. Fields can have either one or multiple values. There can 
 * f.ex. only be one name, but multiple mail addresses. The value of a field 
 * can either be a string, a number or a date.
 */
class PodioContactAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Returns the total number of contacts by organization.
   *
   * @return Array of contact objects
   */
  public function getContactsTotals() {
    if ($response = $this->podio->request('/contact/totals/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the contact details about the user with the given id.
   *
   * @param $user_id The id of the contact
   *
   * @return A contact object
   */
  public function getContact($user_id) {
    if ($response = $this->podio->request('/contact/' . $user_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the contact details about the contact with the 
   * given profile id.
   *
   * @param $profile_id The id of the profile to retrieve
   */
  public function getContactV2($profile_id) {
    if ($response = $this->podio->request('/contact/' . $profile_id . '/v2')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the top contacts for the user ordered by their overall 
   * interactive with the active user.
   * 
   * @param $limit The maximum number of contacts to return, defaults to all.
   * @param $type How the contacts should be returned, "mini", "short" 
   *              or "full". Default is "mini"
   *
   * @return Array of contact objects
   */
  public function getTopContacts($limit, $type = 'mini') {
    if ($response = $this->podio->request('/contact/top/', array('limit' => $limit, 'type' => $type))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Used to get a list of contacts for the user. Either global or within 
   * a context (space or organization).
   *
   * @param $type Context for call. "all", "space" or "org"
   * @param $ref_id The id of the reference, if any
   * @param $contact_type Comma-separated list of contacts to return, 
   *                      can be either "user", "connection" or "space". 
   *                      Defaults to "user". To get all types of 
   *                      contacts supply a blank value for the parameter.
   * @param $format Determines the way the result is returned. Valid options 
   *                are "mini", "short" and "full". Default is "mini".
   * @param $order The order in which the contacts can be returned. See the 
   *               area for details on the ordering options.
   * @param $limit The maximum number of contacts that should be returned.
   * @param $offset he offset to use when returning contacts.
   * @param $required An array of fields that should exist for 
   *                  the contacts returned. Useful for only getting 
   *                  contacts with an email address or phone number.
   * @param $field An array with one key/value pair. The key is name of a 
   *               required field. The value is the value for the field. 
   *               For text fields partial matches will be returned.
   *
   * @return Array of contact objects
   */
  public function getContacts($type = 'all', $ref_id = NULL, $contact_type = 'user', $format = 'mini', $order = 'name', $limit = NULL, $offset = 0, $required = array(), $field = array()) {
    if ($type != 'all' && !$ref_id) {
      return FALSE;
    }
    
    if ($type == 'space') {
      $url = '/contact/space/'.$ref_id;
    }
    elseif ($type == 'org') {
      $url = '/contact/org/'.$ref_id;
    }
    else {
      $url = '/contact/';
    }

    $requestData = array();
    $requestData['type'] = $format;
    $requestData['order'] = $order;
    $requestData['limit'] = $limit;
    $requestData['contact_type'] = $contact_type;
    
    if ($offset) {
      $requestData['offset'] = $offset;
    }
    if (count($required) > 0) {
      $requestData['required'] = implode(',', $required);
    }
    $requestData = array_merge($requestData, $field);

    if ($response = $this->podio->request($url, $requestData)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

}

