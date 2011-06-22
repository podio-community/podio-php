<?php

/**
 * Status messages are small texts that the users wishes to share with the 
 * other users in a space. It can be anything from a note that the user will 
 * be in later today over links to interesting resources and information 
 * about what the user is working on a the moment.
 */
class PodioStatusAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Retrieves a status message by its id. The id of the status message is 
   * usually gotten from the stream.
   *
   * @param $status_id The id of the status to retrieve
   *
   * @return A status message object
   */
  public function get($status_id) {
    $response = $this->podio->request('/status/'.$status_id);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Retrieves the latest status message on a space from a user.
   *
   * @param $user_id User to get status for
   * @param $space_id Space to get status for
   *
   * @return A status message object
   */
  public function getLatest($user_id, $space_id) {
    $response = $this->podio->request('/status/user/'.$user_id.'/space/'.$space_id.'/latest/');
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Creates a new status message for a user on a specific space. A status 
   * update is simply a short text message that the user wishes to share with 
   * the rest of the space.
   *
   * @param $space_id Id of space to create message on
   * @param $value The actual status message
   * @param $file_ids Temporary files that have been uploaded and should be 
   *                  attached to this item
   * @param $alerts The users who should be alerted about this status message
   * @param $embed_id The id of an embedded link that has been created with the Add an embed operation in the Embed area,
   * @param $embed_file_id  The id of a thumbnail that has been returned from the Add an embed operation
   */
  public function create($space_id, $value, $file_ids = array(), $alerts = array(), $embed_id = NULL, $embed_file_id = NULL) {
    $data = array('space_id' => $space_id, 'value' => $value, 'alerts' => array(), 'file_ids' => array());

    if ($alerts) {
      $data['alerts'] = $alerts;
    }
    if ($file_ids) {
      $data['file_ids'] = $file_ids;
    }

    if ($embed_id) {
      $data['embed_id'] = $embed_id;
    }

    if ($embed_file_id) {
      $data['embed_file_id'] = $embed_file_id;
    }
    
    if ($response = $this->podio->request('/status/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * This will update an existing status message. This will normally only be 
   * used to correct spelling and grammatical mistakes.
   *
   * @param $status_id Id of status to update
   * @param $value The updated status message
   * @param $file_ids Temporary files that have been uploaded and should 
   *                  be attached to this item
   */
  public function update($status_id, $value, $file_ids = array()) {
    $data = array('value' => $value, 'file_ids' => $file_ids);
    if ($response = $this->podio->request('/status/'.$status_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * This is used to delete a status message. This is normally only done if 
   * the user regrets his status update. After deletion the status message 
   * will no longer be viewable by anyone.
   *
   * @param $status_id Id of the status to delete
   */
  public function delete($status_id) {
    if ($response = $this->podio->request('/status/'.$status_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
}

