<?php

/**
 * Each user have a profile attached, that holds all the personal details of 
 * the user. This includes very basic information like the name and mail 
 * addresses, but can also include more advanced fields like billing address 
 * and IM addresses. Fields can have either one or multiple values. There can 
 * f.ex. only be one name, but multiple mail addresses. The value of a field 
 * can either be a string, a number or a date.
 */
class PodioContact {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Returns the total number of contacts by organization.
   */
  public function getContactsTotals() {
    if ($response = $this->podio->get('/contact/totals/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the total number of contacts on the space.
   */
   public function getSpaceContactsTotals($space_id, $attributes) {
     if ($response = $this->podio->get('/contact/space/'.$space_id.'totals/', $attributes)) {
       return json_decode($response->getBody(), TRUE);
     }
   }

  /**
   * Returns all the contact details about the contact with the 
   * given profile id.
   */
  public function getContact($profile_id) {
    if ($response = $this->podio->get('/contact/'.$profile_id.'/v2')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the top contacts for the user ordered by their overall 
   * interactive with the active user.
   */
  public function getTopContacts($attributes) {
    if ($response = $this->podio->get('/contact/top/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the top contacts for a space ordered by their overall 
   * interactive with the active user.
   */
  public function getTopSpaceContacts($space_id, $attributes) {
    if ($response = $this->podio->get('/contact/space/'.$space_id.'/top/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Used to get a list of contacts for the user.
   */
  public function getContacts($attributes) {
    if ($response = $this->podio->get('/contact/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Used to get a list of organization contacts for the user.
   */
  public function getOrgContacts($org_id, $attributes) {
    if ($response = $this->podio->get('/contact/org/'.$org_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Used to get a list of space contacts for the user.
   */
  public function getSpaceContacts($space_id, $attributes) {
    if ($response = $this->podio->get('/contact/space/'.$space_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the contacts for the connection with the given id.
   */
  public function getByConnection($connection_id, $attributes) {
    if ($response = $this->podio->get('/contact/connection/'.$connection_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the contacts for the connection with the given type.
   */
  public function getByConnectionType($connection_type, $attributes) {
    if ($response = $this->podio->get('/contact/connection/'.$connection_type, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Creates a new space contact for use by everyone on the space.
   */
  public function createSpaceContact($space_id, $attributes) {
    if ($response = $this->podio->post('/contact/space/'.$space_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the contact with the given id. It is currently only allowed 
   * to delete contacts of type "connection".
   */
  public function delete($profile_id) {
    if ($response = $this->podio->delete('/contact/'.$profile_id)) {
      return TRUE;
    }
  }

  /**
   * Returns the contact with the given user id.
   */
  public function getUserContact($user_id) {
    if ($response = $this->podio->get('/contact/user/'.$user_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the given field for the given profile.
   */
  public function getContactField($profile_id, $key) {
    if ($response = $this->podio->get('/contact/'.$profile_id.'/'.$key.'/v2')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the value of a contact with the specific field.
   */
  public function getUserContactField($user_id, $key) {
    if ($response = $this->podio->get('/contact/user/'.$user_id.'/'.$key.'/v2')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the vCard for the given contact.
   */
  public function vcard($profile_id) {
    if ($response = $this->podio->get('/contact/'.$profile_id.'/vcard')) {
      return $response->getBody();
    }
  }

  /**
   * Returns the total number of contacts for the active user.
   */
  public function getContactsTotalsV2($attributes) {
    if ($response = $this->podio->get('/contact/totals/v2/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the contact with the given profile id. It is currently only
   * possible to update contacts of type "connection".
   */
  public function update($profile_id, $attributes) {
    if ($response = $this->podio->put('/contact/'.$profile_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Updates the given field on the given contact. Updates are currently only
   * allowed from contacts of type "connection".
   */
  public function updateField($profile_id, $key, $attributes) {
    if ($response = $this->podio->put('/contact/'.$profile_id.'/'.$key, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
