<?php
/**
 * @see https://developers.podio.com/doc/items
 */
class PodioItemDiff extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('field_id', 'integer');
        $this->property('type', 'string');
        $this->property('external_id', 'string');
        $this->property('label', 'string');
        $this->property('from', 'array');
        $this->property('to', 'array');
        $this->property('config', 'hash');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/items/revert-item-revision-953195
     */
    public static function revert(PodioClient $podio_client, $item_id, $revision_id)
    {
        $response = $podio_client->delete("/item/{$item_id}/revision/{$revision_id}");
        if ($response->body) {
            $json_body = $response->json_body();
            return $json_body['revision'];
        }
        return null;
    }

    /**
     * @see https://developers.podio.com/doc/items/get-item-revision-difference-22374
     */
    public static function get_for(PodioClient $podio_client, $item_id, $revision_from_id, $revision_to_id)
    {
        return self::listing($podio_client->get("/item/{$item_id}/revision/{$revision_from_id}/{$revision_to_id}"));
    }
}
