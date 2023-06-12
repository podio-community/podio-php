<?php
/**
 * @see https://developers.podio.com/doc/filters
 */
class PodioView extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('view_id', 'integer', array('id' => true));
        $this->property('name', 'string');
        $this->property('created_on', 'datetime');
        $this->property('items', 'integer');
        $this->property('sort_by', 'string');
        $this->property('sort_desc', 'string');
        $this->property('filters', 'hash');
        $this->property('layout', 'string');
        $this->property('fields', 'hash');

        $this->has_one('created_by', 'ByLine');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/views/create-view-27453
     */
    public static function create($app_id, $attributes = array(), PodioClient $podio_client)
    {
        $body = $podio_client->post("/view/app/{$app_id}/", $attributes)->json_body();
        return $body['view_id'];
    }

    /**
     * @see https://developers.podio.com/doc/views/get-view-27450
     */
    public static function get($view_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/view/{$view_id}"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/views/get-views-27460
     */
    public static function get_for_app($app_id, PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/view/app/{$app_id}/"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/views/get-last-view-27663
     */
    public static function get_last($app_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/view/app/{$app_id}/last"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/views/update-last-view-5988251
     */
    public static function update_last($app_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->put("/view/app/{$app_id}/last", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/views/delete-view-27454
     */
    public static function delete($view_id, PodioClient $podio_client)
    {
        return $podio_client->delete("/view/{$view_id}");
    }
}
