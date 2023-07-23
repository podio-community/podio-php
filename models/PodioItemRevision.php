<?php
/**
 * @see https://developers.podio.com/doc/items
 */
class PodioItemRevision extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('revision', 'integer', array('id' => true));
        $this->property('app_revision', 'integer');
        $this->property('created_on', 'datetime');

        $this->has_one('created_by', 'ByLine');
        $this->has_one('created_via', 'Via');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/items/get-item-revision-22373
     */
    public static function get(PodioClient $podio_client, $item_id, $revision_id)
    {
        return self::member($podio_client, $podio_client->get("/item/{$item_id}/revision/{$revision_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/items/get-item-revision-22373
     */
    public static function get_for(PodioClient $podio_client, $item_id)
    {
        return self::listing($podio_client, $podio_client->get("/item/{$item_id}/revision/"));
    }
}
