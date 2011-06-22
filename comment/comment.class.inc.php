<?php

/**
 * Comments are made by users on different objects. Objects can f.ex. be 
 * items, status, etc. Comments is simply a text that can be any length.
 *
 * Comments are made from the API of the object, see f.ex. Items for more 
 * details. Comments are however updated and deleted from the comment API.
 */
class PodioCommentAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Adds a new comment to the object of the given type and id.
   *
   * @param $ref_type The type of reference. E.g. "item" or "status" or "share"
   * @param $ref_id The id of the reference object
   * @param $value The updated comment
   * @param $external_id The external id for the comment, if any
   * @param $file_ids Array of file ids attached to this comment
   * @param $alerts Array of users who should be alerted about this comment
   * @param $embed_id The id of an embedded link that has been created with the Add an mebed operation in the Embed area,
   * @param $embed_file_id The id of a thumbnail that has been returned from the Add an embed operation,
   *
   * @return Array with new comment id
   */
  public function create($ref_type, $ref_id, $value, $external_id = NULL, $file_ids = array(), $alerts = array(), $embed_id = NULL, $embed_file_id = NULL) {
    $data = array('value' => $value);
    if ($external_id) {
      $data['external_id'] = $external_id;
    }
    if ($embed_id) {
      $data['embed_id'] = $embed_id;
    }
    if ($embed_file_id) {
      $data['embed_file_id'] = $embed_file_id;
    }
    $data['alerts'] = $alerts;
    $data['file_ids'] = $file_ids;
    if ($response = $this->podio->request('/comment/'.$ref_type.'/'.$ref_id, $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates an already created comment. This should only be used to correct 
   * spelling and grammatical mistakes in the comment.
   *
   * @param $comment_id The id of the comment to update
   * @param $value The updated comment
   * @param $external_id The external id for the comment, if any
   * @param $file_ids Array of file ids attached to this comment
   */
  public function update($comment_id, $value, $external_id = NULL, $file_ids = array()) {
    $data = array('value' => $value, 'file_ids' => $file_ids);
    if ($external_id) {
      $data['external_id'] = $external_id;
    }
    if ($response = $this->podio->request('/comment/'.$comment_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes a comment made by a user. This can be used to retract a comment 
   * that was made and which the user regrets.
   *
   * @param $comment_id The id of the comment to delete
   */
  public function delete($comment_id) {
    if ($response = $this->podio->request('/comment/'.$comment_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Used to retrieve all the comments that have been made on an object of 
   * the given type and with the given id. It returns a list of all the 
   * comments sorted in ascending order by time created.
   *
   * @param $ref_type
   * @param $ref_id
   *
   * @return Array of comment objects
   */
  public function getComments($ref_type, $ref_id) {
    if ($ref_id > 0 && $response = $this->podio->request('/comment/'.$ref_type.'/'.$ref_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the contents of a comment. It is not possible to see where the 
   * comment was made, only the comment itself.
   *
   * @param $comment_id The id of the comment to retrieve
   *
   * @return A comment object
   */
  public function get($comment_id) {
    if ($response = $this->podio->request('/comment/'.$comment_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

