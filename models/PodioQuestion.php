<?php
/**
 * @see https://developers.podio.com/doc/questions
 */
class PodioQuestion extends PodioObject {
  public function __construct($attributes = array()) {
    $this->property('question_id', 'integer', array('id' => true));
    $this->property('text', 'string');

    $this->has_one('ref', 'Reference');
    $this->has_many('answers', 'QuestionAnswer');
    $this->has_many('options', 'QuestionOption');

    $this->init($attributes);
  }

  /**
   * @see https://developers.podio.com/doc/questions/create-question-887166
   */
  public static function create($ref_type, $ref_id, $attributes = array()) {
    $body = Podio::post("/question/{$ref_type}/{$ref_id}/", $attributes)->json_body();
    return $body['question_id'];
  }

  /**
   * @see https://developers.podio.com/doc/questions/answer-question-887232
   */
  public static function answer($question_id, $ref_type, $ref_id, $attributes = array()) {
    return Podio::post("/question/{$question_id}/{$ref_type}/{$ref_id}/", $attributes);
  }

  /**
   * @see https://developers.podio.com/doc/questions/get-question-945740
   */
  public static function get($question_id) {
    return self::member(Podio::get("/question/{$question_id}"));
  }

  /**
   * @see https://developers.podio.com/doc/questions/get-questions-on-object-945736
   */
  public static function get_for($ref_type, $ref_id) {
    return self::listing(Podio::get("/question/{$ref_type}/{$ref_id}/"));
  }

}
