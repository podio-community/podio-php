<?php
/**
 * @see https://developers.podio.com/doc/comments
 */
class PodioComment extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('comment_id', 'integer', array('id' => true));
        $this->property('value', 'string');
        $this->property('rich_value', 'string');
        $this->property('external_id', 'string');
        $this->property('space_id', 'integer');
        $this->property('created_on', 'datetime');
        $this->property('like_count', 'integer');
        $this->property('is_liked', 'boolean');

        $this->has_one('created_by', 'ByLine');
        $this->has_one('created_via', 'Via');
        $this->has_one('ref', 'Reference');

        $this->has_one('embed', 'Embed', array('json_value' => 'embed_id', 'json_target' => 'embed_id'));
        $this->has_one('embed_file', 'File', array('json_value' => 'file_id', 'json_target' => 'embed_file_id'));
        $this->has_many('files', 'File', array('json_value' => 'file_id', 'json_target' => 'file_ids'));
        $this->has_many('questions', 'Question');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/comments/get-a-comment-22345
     */
    public static function get(PodioClient $podio_client, $comment_id)
    {
        return self::member($podio_client, $podio_client->get("/comment/{$comment_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/comments/get-comments-on-object-22371
     */
    public static function get_for(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array())
    {
        return self::listing($podio_client, $podio_client->get("/comment/{$ref_type}/{$ref_id}/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/comments/delete-a-comment-22347
     */
    public static function delete(PodioClient $podio_client, $comment_id)
    {
        return $podio_client->delete("/comment/{$comment_id}");
    }

    /**
     * @see https://developers.podio.com/doc/comments/add-comment-to-object-22340
     */
    public static function create(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array(), $options = array())
    {
        $url = $podio_client->url_with_options("/comment/{$ref_type}/{$ref_id}", $options);
        $body = $podio_client->post($url, $attributes)->json_body();
        return $body['comment_id'];
    }

    /**
     * @see https://developers.podio.com/doc/comments/update-a-comment-22346
     */
    public static function update(PodioClient $podio_client, $comment_id, $attributes = array())
    {
        return $podio_client->put("/comment/{$comment_id}", $attributes);
    }
}
