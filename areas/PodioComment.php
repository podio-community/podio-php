<?php

/**
 * Comments are made by users on different objects. Objects can f.ex. be 
 * items, status, etc. Comments is simply a text that can be any length.
 *
 * Comments are made from the API of the object, see f.ex. Items for more 
 * details. Comments are however updated and deleted from the comment API.
 */
class PodioComment {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Adds a new comment to the object of the given type and id.
   */
  public function create($ref_type, $ref_id, $attributes) {
    if ($response = $this->podio->post('/comment/'.$ref_type.'/'.$ref_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates an already created comment. This should only be used to correct 
   * spelling and grammatical mistakes in the comment.
   */
  public function update($comment_id, $attributes) {
    if ($response = $this->podio->put('/comment/'.$comment_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes a comment made by a user. This can be used to retract a comment 
   * that was made and which the user regrets.
   */
  public function delete($comment_id) {
    if ($response = $this->podio->delete('/comment/'.$comment_id)) {
      return TRUE;
    }
  }
  
  /**
   * Used to retrieve all the comments that have been made on an object of 
   * the given type and with the given id. It returns a list of all the 
   * comments sorted in ascending order by time created.
   */
  public function getComments($ref_type, $ref_id) {
    if ($response = $this->podio->get('/comment/'.$ref_type.'/'.$ref_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the contents of a comment. It is not possible to see where the 
   * comment was made, only the comment itself.
   */
  public function get($comment_id) {
    if ($response = $this->podio->get('/comment/'.$comment_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
