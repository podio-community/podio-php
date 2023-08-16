<?php
/**
 * @see https://developers.podio.com/doc/widgets
 */
class PodioWidget extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('widget_id', 'integer', array('id' => true));
        $this->property('type', 'string');
        $this->property('title', 'string');
        $this->property('config', 'hash');
        $this->property('rights', 'array');
        $this->property('data', 'hash'); // Only for get_for() method

        $this->has_one('created_by', 'ByLine');
        $this->property('created_on', 'datetime');
        $this->has_one('ref', 'Reference');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/widgets/create-widget-22491
     */
    public static function create(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array())
    {
        return self::member($podio_client->post("/widget/{$ref_type}/{$ref_id}/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/widgets/delete-widget-22492
     */
    public static function delete(PodioClient $podio_client, $widget_id)
    {
        return $podio_client->delete("/widget/{$widget_id}");
    }

    /**
     * @see https://developers.podio.com/doc/widgets/get-widget-22489
     */
    public static function get(PodioClient $podio_client, $widget_id)
    {
        return self::member($podio_client->get("/widget/{$widget_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/widgets/get-widgets-22494
     */
    public static function get_for(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return self::listing($podio_client->get("/widget/{$ref_type}/{$ref_id}/"));
    }

    /**
     * @see https://developers.podio.com/doc/widgets/update-widget-22490
     */
    public static function update(PodioClient $podio_client, $widget_id, $attributes = array())
    {
        return $podio_client->put("/widget/{$widget_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/widgets/update-widget-order-22495
     */
    public static function update_order(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array())
    {
        return $podio_client->put("/widget/{$ref_type}/{$ref_id}/order", $attributes);
    }
}
