<?php
/**
 * @see https://developers.podio.com/doc/filters
 */
class PodioView extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
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
    public static function create(PodioClient $podio_client, $app_id, $attributes = array())
    {
        $body = $podio_client->post("/view/app/{$app_id}/", $attributes)->json_body();
        return $body['view_id'];
    }

    /**
     * @see https://developers.podio.com/doc/views/get-view-27450
     */
    public static function get(PodioClient $podio_client, $view_id)
    {
        return self::member($podio_client, $podio_client->get("/view/{$view_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/views/get-views-27460
     */
    public static function get_for_app(PodioClient $podio_client, $app_id)
    {
        return self::listing($podio_client, $podio_client->get("/view/app/{$app_id}/"));
    }

    /**
     * @see https://developers.podio.com/doc/views/get-last-view-27663
     */
    public static function get_last(PodioClient $podio_client, $app_id)
    {
        return self::member($podio_client, $podio_client->get("/view/app/{$app_id}/last"));
    }

    /**
     * @see https://developers.podio.com/doc/views/update-last-view-5988251
     */
    public static function update_last(PodioClient $podio_client, $app_id, $attributes = array())
    {
        return $podio_client->put("/view/app/{$app_id}/last", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/views/delete-view-27454
     */
    public static function delete(PodioClient $podio_client, $view_id)
    {
        return $podio_client->delete("/view/{$view_id}");
    }
}
