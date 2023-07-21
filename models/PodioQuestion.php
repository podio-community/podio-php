<?php
/**
 * @see https://developers.podio.com/doc/questions
 */
class PodioQuestion extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
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
    public static function create(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array())
    {
        $body = $podio_client->post("/question/{$ref_type}/{$ref_id}/", $attributes)->json_body();
        return $body['question_id'];
    }

    /**
     * @see https://developers.podio.com/doc/questions/answer-question-887232
     */
    public static function answer(PodioClient $podio_client, $question_id, $attributes = array())
    {
        return $podio_client->post("/question/{$question_id}/", $attributes);
    }
}
