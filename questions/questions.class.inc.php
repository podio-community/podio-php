<?php

/**
 * The question area makes it possible to attach questions to other objects in Podio.
 * Questions are simply a text with a list of single-choice options.
 */
class PodioQuestionsAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Answers the question for the given object. The object type can be either "status" or "comment".
   *
   * @param $ref_type             Type of reference to attach to.
   *                              Can be "status", or "comment"
   * @param $ref_id               Status id, item id or comment id   *
   * @param $question_id          Question id *
   * @param $question_option_id   The id of the option that is the answer
   *
   * @return The question id
   */
  public function answer($question_id, $ref_type, $ref_id, $question_option_id) {
    $data['question_option_id'] = $question_option_id;
    $response = $this->podio->request('/question/' . $question_id . '/' . $ref_type .'/' . $ref_id .'/', $data, HTTP_Request2::METHOD_POST);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
    return FALSE;
  }

  /**
   * Creates a new question on the given object. Supported object types are "status" and "comment".
   *
   * @param $ref_type Type of reference to attach to.
   *                  Can be "status", or "comment"
   * @param $ref_id Status id, item id or comment id   *
   * @param $text The text of the question
   * @param $options The list of options
   *
   * @return The question id
   */
  public function create($ref_type, $ref_id, $text, $options) {
    $data['text'] = $text;
    $data['options'] = $options;
    $response = $this->podio->request('/question/' . $ref_type .'/' . $ref_id .'/', $data, HTTP_Request2::METHOD_POST);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
    return FALSE;
  }


}

