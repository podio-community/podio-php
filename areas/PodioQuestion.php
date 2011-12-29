<?php

/**
 * The question area makes it possible to attach questions to other objects in Podio.
 * Questions are simply a text with a list of single-choice options.
 */
class PodioQuestion {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Answers the question for the given object. The object type can be either "status" or "comment".
   */
  public function answer($question_id, $ref_type, $ref_id, $attributes) {
    if ($response = $this->podio->post('/question/'.$question_id.'/'.$ref_type.'/'.$ref_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Creates a new question on the given object. Supported object types are "status" and "comment".
   */
  public function create($ref_type, $ref_id, $attributes) {
    if ($response = $this->podio->post('/question/'.$ref_type.'/'.$ref_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}
