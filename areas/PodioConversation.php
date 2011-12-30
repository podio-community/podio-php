<?php

/**
 * Conversations is private messaging between a number of users. 
 * Conversations can be replied through, but cannot be (yet) be forwarded.
 */
class PodioConversation {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Gets the conversation including participants and messages with the 
   * given id. Only participants in the conversation is allowed to view 
   * the conversation.
   */
  public function get($conversation_id) {
    if ($response = $this->podio->get('/conversation/'.$conversation_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Creates a new conversation with a list of users. Once a conversation is 
   * started, the participants cannot (yet) be changed.
   */
  public function create($attributes = array()) {
    if ($response = $this->podio->post('/conversation/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Creates a reply to the conversation.
   */
  public function createReply($conversation_id, $attributes = array()) {
    if ($response = $this->podio->post('/conversation/'.$conversation_id.'/reply', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Adds a new user to the conversation.
   */
  public function addParticipant($conversation_id, $attributes = array()) {
    if ($response = $this->podio->post('/conversation/'.$conversation_id.'/participant/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

