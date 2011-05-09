<?php

/**
 * Conversations is private messaging between a number of users. Once a 
 * conversation has been started, new users cannot be added to a conversation. 
 * Conversations can be replied through, but cannot be (yet) be forwarded.
 */
class PodioConversationAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Gets the conversation including participants and messages with the 
   * given id. Only participants in the conversation is allowed to view 
   * the conversation.
   *
   * @param $conversation_id The id of the conversation
   *
   * @return A conversation object with all messages
   */
  public function get($conversation_id) {
    $response = $this->podio->request('/conversation/'.$conversation_id);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns a list of all the conversations on the object that the active user is part of.
   *
   * @param $ref_type Either "item" or "status"
   * @param $ref_id Item id or status id
   *
   * @return Array of conversation objects
   */
  public function getConversationOnObject($ref_type, $ref_id) {
    $response = $this->podio->request('/conversation/'.$ref_type.'/'.$ref_id.'/');
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Creates a new conversation with a list of users. Once a conversation is 
   * started, the participants cannot (yet) be changed.
   *
   * @param $subject The subject of the conversation
   * @param $text The body of the first message in the conversation
   * @param $participants Array of user ids in the conversation
   * @param $file_ids Array of file ids to be attached to the initial message
   */
  public function create($subject, $text, $participants, $file_ids = array()) {
    $url = '/conversation/';
    $data = array('subject' => $subject, 'text' => $text, 'participants' => $participants);
    if ($file_ids) {
      $data['file_ids'] = $file_ids;
    }
    if ($response = $this->podio->request($url, $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Creates a reply to the conversation.
   *
   * @param $conversation_id Id of the conversation being replied to
   * @param $text The text of the reply
   * @param $file_ids Array of file ids to be attached to the initial message
   */
  public function createReply($conversation_id, $text, $file_ids = array()) {
    $data = array('text' => $text);
    if ($file_ids) {
      $data['file_ids'] = $file_ids;
    }
    if ($response = $this->podio->request('/conversation/'.$conversation_id.'/reply', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

