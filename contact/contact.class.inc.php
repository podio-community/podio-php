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
   */
  public function getContactsTotals() {
    if ($response = $this->podio->request('/contact/totals/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the total number of contacts on the space.
   * 
   * @param $space_id The id of the space to get totals for
   * @param $exclude_self 1 to exclude self, 0 to include self. Defaults to 1.
   */
   public function getSpaceContactsTotals($space_id, $exclude_self = 1) {
     if ($response = $this->podio->request('/contact/space/'.$space_id.'totals/', array('exclude_self' => $exclude_self))) {
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
   * @param $offset The offset into the list
   *
   * @return Array of contact objects
   */
  public function getTopContacts($limit, $type = 'mini', $offset = 0) {
    if ($response = $this->podio->request('/contact/top/', array('limit' => $limit, 'type' => $type, 'offset' => $offset))) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the top contacts for a space ordered by their overall 
   * interactive with the active user.
   * 
   * @param $space_id The id of the space to get contacts for
   * @param $limit The maximum number of contacts to return, defaults to all.
   * @param $type How the contacts should be returned, "mini", "short" 
   *              or "full". Default is "mini"
   * @param $offset The offset into the list
   *
   * @return Array of contact objects
   */
  public function getTopSpaceContacts($space_id, $limit, $type = 'mini', $offset = 0) {
    if ($response = $this->podio->request('/contact/space/'.$space_id.'/top/', array('limit' => $limit, 'type' => $type, 'offset' => $offset))) {
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
   * @param $exclude_self If set to 1 (the default) the active user will not 
   *                      be returned, else the active user can be included 
   *                      in the results.
   * @param $external_id The external id of the contact
   *
   * @return Array of contact objects
   */
  public function getContacts($type = 'all', $ref_id = NULL, $contact_type = 'user', $format = 'mini', $order = 'name', $limit = NULL, $offset = 0, $required = array(), $field = array(), $exclude_self = 1, $external_id = NULL) {
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
    $requestData['exclude_self'] = $exclude_self;
    
    if ($offset) {
      $requestData['offset'] = $offset;
    }
    if (count($required) > 0) {
      $requestData['required'] = implode(',', $required);
    }
    if ($external_id) {
      $requestData['external_id'] = $external_id;
    }
    
    $requestData = array_merge($requestData, $field);

    if ($response = $this->podio->request($url, $requestData)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the contacts for the connection with the given id.
   *
   * @param $connection_id The id of the connection
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
  public function getByConnection($connection_id, $format = 'mini', $order = 'name', $limit = NULL, $offset = 0, $required = array(), $field = array()) {

    $requestData = array();
    $requestData['type'] = $format;
    $requestData['order'] = $order;
    $requestData['limit'] = $limit;
    
    if ($offset) {
      $requestData['offset'] = $offset;
    }
    if (count($required) > 0) {
      $requestData['required'] = implode(',', $required);
    }
    
    $requestData = array_merge($requestData, $field);

    if ($response = $this->podio->request('/contact/connection/'.$connection_id, $requestData)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the contacts for the connection with the given type.
   *
   * @param $connection_type The type of the connection
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
  public function getByConnectionType($connection_type, $format = 'mini', $order = 'name', $limit = NULL, $offset = 0, $required = array(), $field = array()) {

    $requestData = array();
    $requestData['type'] = $format;
    $requestData['order'] = $order;
    $requestData['limit'] = $limit;
    
    if ($offset) {
      $requestData['offset'] = $offset;
    }
    if (count($required) > 0) {
      $requestData['required'] = implode(',', $required);
    }
    
    $requestData = array_merge($requestData, $field);

    if ($response = $this->podio->request('/contact/connection/'.$connection_type, $requestData)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Creates a new space contact for use by everyone on the space.
   *
   * @param $space_id ID of the space to create contact on
   * @param $external_id Optional external id for the contact
   * @param $fields Array of contact fields to add
   */
  public function createSpaceContact($space_id, $external_id = FALSE, $fields = array()) {
    $data = $fields;
    if ($external_id) {
      $data['external_id'] = $external_id;
    }
    if ($response = $this->podio->request('/contact/space/'.$space_id.'/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the contact with the given id. It is currently only allowed 
   * to delete contacts of type "connection".
   *
   * @param $profile_id ID of the contact to delete
   */
  public function delete() {
    if ($response = $this->podio->request('/contact/'.$profile_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }

  /**
   * Returns the contact with the given user id.
   *
   * @param $user_id The ID of the user to get
   */
  public function getUserContact($user_id) {
    if ($response = $this->podio->request('/contact/user/'.$user_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the given field for the given profile.
   *
   * @param $profile_id The ID of the profile to get field for
   * @param $key The field key to get
   */
  public function getContactField($profile_id, $key) {
    if ($response = $this->podio->request('/contact/'.$profile_id.'/'.$key.'/v2')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the value of a contact with the specific field.
   *
   * @param $user_id The ID of the user to get field for
   * @param $key The field key to get
   */
  public function getUserContactField($user_id, $key) {
    if ($response = $this->podio->request('/contact/user/'.$user_id.'/'.$key.'/v2')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the vCard for the given contact.
   *
   * @param $profile_id The ID of the profile to get vcard for
   */
  public function vcard($profile_id) {
    if ($response = $this->podio->request('/contact/'.$profile_id.'/vcard')) {
      return $response->getBody();
    }
  }

  /**
   * Returns the total number of contacts for the active user.
   * 
   * @param $exclude_self 1to exclude self, 0 to include self. Defaults to 1.
   */
  public function getContactsTotalsV2($exclude_self = 1) {
    if ($response = $this->podio->request('/contact/totals/v2/', array('exclude_self' => $exclude_self))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the contact with the given profile id. It is currently only
   * possible to update contacts of type "connection".
   *
   * @param $profile_id The id of the profile to update
   * @param $fields Array of fields to update
   */
  public function update($profile_id, $fields) {
    if ($response = $this->podio->request('/contact/'.$profile_id, $fields, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Updates the given field on the given contact. Updates are currently only
   * allowed from contacts of type "connection".
   *
   * @param $profile_id The id of the profile to update
   * @param $key The field key to update
   * @param $value The value to use
   */
  public function updateField($profile_id, $key, $value) {
    if ($response = $this->podio->request('/contact/'.$profile_id.'/'.$key, array('value' => $value), HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  

}

