<?php
/**
 * @see https://developers.podio.com/doc/items
 */
class PodioItemRevision extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
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
    public static function get($item_id, $revision_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/item/{$item_id}/revision/{$revision_id}"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/items/get-item-revision-22373
     */
    public static function get_for($item_id, PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/item/{$item_id}/revision/"), $podio_client);
    }
}
