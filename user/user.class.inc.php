<?php

/**
 * This area holds all the users which just includes basic operations.
 */
class PodioUserAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Gets the active user
   *
   * @return User object for the current user
   */
  public function get() {
    if ($response = $this->podio->request('/user/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Updates the active user. The old and new password can be left out, 
   * in which case the password will not be changed. If the mail is 
   * changed, the old password has to be supplied as well.
   *
   * @param $mail The new email of the user
   * @param $old_password The users current password
   * @param $new_password The users new password
   * @param $locale The locale of the new user
   * @param $timezone The timezone of the user
   */
  public function update($mail, $old_password, $new_password, $locale, $timezone) {
    
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
    
    $response = $this->podio->request('/user/', $data, HTTP_Request2::METHOD_PUT);
    if ($response && $response->getStatus() == '204') {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Updates the active user. The old and new password can be left out, 
   * in which case the password will not be changed. If the mail is 
   * changed, the old password has to be supplied as well.
   *
   * @param $mails The new email of the user
   * @param $old_password The users current password
   * @param $new_password The users new password
   * @param $locale The locale of the new user
   * @param $timezone The timezone of the user
   */
  public function updateV2($mails, $old_password, $new_password, $locale, $timezone) {
    
    $data = array(
      'mails' => $mails,
      'old_password' => $old_password,
      'new_password' => $new_password, 
      'locale' => $locale,
      'timezone' => $timezone,
    );
    if (!$old_password) {
      unset($data['old_password']);
      unset($data['new_password']);
      unset($data['mails']);
    }
    
    $response = $this->podio->request('/user/', $data, HTTP_Request2::METHOD_PUT);
    if ($response && $response->getStatus() == '204') {
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Returns the profile of the active user
   */
  public function getOwnProfile() {
    if ($response = $this->podio->request('/user/profile/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the current status for the user. This includes the user data, 
   * profile data and notification data.
   */
  public function getStatus() {
    if ($response = $this->podio->request('/user/status')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the value (true or false) of the property for the active user with the given name.
   * The property is specific to the auth client used.
   *
   * @param $name Property name to get
   */
  public function getProperty($name) {
    if ($response = $this->podio->request('/user/property/'.$name)) {
      $result = json_decode($response->getBody(), TRUE);
      return (bool)$result['value'];
    } else {
      return false;
    }
  }

  /**
   * Sets the value of the property for the active user with the given name. 
   * The property is specific to the auth client used.
   *
   * @param $name String. Name of property to set.
   * @param $value True or false value for this property
   */
  public function setProperty($name, $value) {
    if ($response = $this->podio->request('/user/property/'.$name, array('value' => (bool)$value), HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the property for the active user with the given name.
   * The property is specific to the auth client used.
   *
   * @param $name Property name to delete
   */
  public function deleteProperty($name) {
    if ($response = $this->podio->request('/user/property/'.$name, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Updates the fields of an existing profile. Fields not specified will 
   * not be updated. To delete a field set the value of the field to null.
   *
   * @param $data Array of field name/value pairs
   */
  public function updateProfile($data = array()) {
    if ($response = $this->podio->request('/user/profile/', $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the specific field on the user.
   *
   * @param $field Name of profile field to update
   * @param $value New value for this field
   */
  public function updateProfileField($field, $value) {
    if ($response = $this->podio->request('/user/profile/'.$field, $value, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Add a new user to Podio.
   *
   * @param $name Profile name for the new user
   * @param $mail The email of the user
   * @param $password The users password
   * @param $locale The locale of the new user
   * @param $timezone The timezone of the user
   * @param $source_type The type of the source, if any, should only use "share"
   * @param $source_id The id of the source, if any
   */
  public function create($name, $mail, $password, $locale, $timezone, $source_type = '', $source_id = '') {
    $data =  array('name' => $name, 'mail' => $mail, 'password' => $password, 'locale' => $locale, 'timezone' => $timezone);
    if ($source_type) {
      $data['source_type'] = $source_type;
    }
    if ($source_id) {
      $data['source_id'] = $source_id;
    }
    if ($response = $this->podio->request('/user/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Used to recover a password. This method will send a recovery code to the 
   * users mail address, which can then be used to reset the password.
   *
   * @param $mail The e-mail to recover password for
   */
  public function recoverPassword($mail) {
    if ($response = $this->podio->request('/user/recover_password', array('mail' => $mail), HTTP_Request2::METHOD_POST)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Resets a users password by supplying a recovery code gotten in a mail 
   * from the recover password operation.
   *
   * @param $password The new password for this user
   * @param $recovery_code The recovery code from the recovery e-mail
   *
   * @return The mail address of the user for which the password was just reset
   */
  public function resetPassword($password, $recovery_code) {
    if ($response = $this->podio->request('/user/reset_password', array('password' => $password, 'recovery_code' => $recovery_code), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Activates the given user. This will make it possible to login as the user.
   *
   * @param $activation_code The activation code to use
   * @param $mail The mail to use when logging in
   * @param $password The new password for the user
   * @param $name The full name of the user
   */
  public function activate($activation_code, $mail, $password, $name, $locale = NULL, $timezone = NULL) {
    $data = array(
      'activation_code' => $activation_code,
      'mail' => $mail,
      'password' => $password,
      'name' => $name,
    );
    if ($locale) {
      $data['locale'] = $locale;
    }
    if ($timezone) {
      $data['timezone'] = $timezone;
    }
    
    if ($response = $this->podio->request('/user/activate_user', $data, HTTP_Request2::METHOD_POST)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
    return FALSE;
  }

  /**
   * Invites the contact to try out Podio. 
   * Either profile_id or mail must given.
   *
   * @param $profile_id The id of the profile to invite. 
   *                    The profile must have an mail address
   * @param $mail The mail address of the person to invite
   */
  public function invite($profile_id = NULL, $mail = NULL) {
    $data = array();
    if ($profile_id) {
      $data['profile_id'] = $profile_id;
    }
    elseif ($mail) {
      $data['mail'] = $mail;
    }
    if ($response = $this->podio->request('/user/invite', $data, HTTP_Request2::METHOD_POST)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }

  /**
   * Deletes the active user.
   */
  public function delete() {
    if ($response = $this->podio->request('/user/', array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Returns the current first time user experience status for the user.
   * This includes user's number of orgs, spaces, apps and users.
   * This method is only available to Podio.
   */
  public function get1UXStatus() {
    if ($response = $this->podio->request('/user/status/1ux')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the current first time user experience status for the user on the given space.
   * This includes user's number of apps and users.
   * This method is only available to Podio.
   *
   * @param $space_id The id of the space to status for
   */
  public function get1UXStatusForSpace($space_id) {
    if ($response = $this->podio->request('/user/status/1ux/space/'.$space_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
}

