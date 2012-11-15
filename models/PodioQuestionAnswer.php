<?php
/**
 * @see https://developers.podio.com/doc/questions
 */
class PodioQuestionAnswer extends PodioObject {
  public function __construct($attributes = array()) {
    $this->property('question_option_id', 'integer', array('id' => true));

    $this->has_one('user', 'Contact');

    $this->init($attributes);
  }

}
