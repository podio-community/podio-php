<?php
/**
 * @see https://developers.podio.com/doc/conversations
 */
class PodioConversation extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('conversation_id', 'integer', array('id' => true));
        $this->property('subject', 'string');

        // Creating conversations
        $this->property('text', 'string');
        $this->property('participants', 'array');

        // Getting conversations
        $this->property('created_on', 'datetime');

        $this->has_one('ref', 'Reference');
        $this->has_one('embed', 'Embed', array('json_value' => 'embed_id', 'json_target' => 'embed_id'));
        $this->has_one('embed_file', 'File', array('json_value' => 'file_id', 'json_target' => 'embed_file_id'));
        $this->has_one('created_by', 'ByLine');
        $this->has_many('files', 'File', array('json_value' => 'file_id', 'json_target' => 'file_ids'));
        $this->has_many('messages', 'ConversationMessage');
        $this->has_many('participants_full', 'ConversationParticipant');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/conversations/get-conversation-22369
     */
    public static function get(PodioClient $podio_client, $conversation_id)
    {
        return self::member($podio_client->get("/conversation/{$conversation_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/conversations/get-conversations-34822801
     */
    public static function get_all(PodioClient $podio_client, $attributes = array())
    {
        return self::listing($podio_client->get("/conversation/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/conversations/get-conversations-on-object-22443
     */
    public static function get_for(PodioClient $podio_client, $ref_type, $ref_id, $plugin)
    {
        return self::listing($podio_client->get("/batch/{$ref_type}/{$ref_id}/"));
    }

    /**
     * @see https://developers.podio.com/doc/conversations/create-conversation-22441
     */
    public static function create(PodioClient $podio_client, $attributes = array())
    {
        return $podio_client->post("/conversation/", $attributes)->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/conversations/create-conversation-on-object-22442
     */
    public static function create_for(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array())
    {
        return $podio_client->post("/conversation/{$ref_type}/{$ref_id}/", $attributes)->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/conversations/reply-to-conversation-22444
     */
    public static function create_reply(PodioClient $podio_client, $conversation_id, $attributes = array())
    {
        $body = $podio_client->post("/conversation/{$conversation_id}/reply", $attributes)->json_body();
        return $body['message_id'];
    }

    /**
     * @see https://developers.podio.com/doc/conversations/add-participants-384261
     */
    public static function add_participant(PodioClient $podio_client, $conversation_id, $attributes = array())
    {
        return $podio_client->post("/conversation/{$conversation_id}/participant/", $attributes)->json_body();
    }
}
