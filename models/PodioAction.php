<?php
/**
 * @see https://developers.podio.com/doc/actions
 */
class PodioAction extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('action_id', 'integer', array('id' => true));
        $this->property('type', 'string');
        $this->property('data', 'hash');
        $this->property('text', 'string');

        $this->has_many('comments', 'Comment');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/actions/get-action-1701120
     */
    public static function get(PodioClient $podio_client, $action_id)
    {
        return self::member($podio_client, $podio_client->get("/action/{$action_id}"));
    }
}
