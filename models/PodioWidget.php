<?php
/**
 * @see https://developers.podio.com/doc/widgets
 */
class PodioWidget extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
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
    public static function create($ref_type, $ref_id, $attributes = array(), PodioClient $podio_client)
    {
        return self::member($podio_client->post("/widget/{$ref_type}/{$ref_id}/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/widgets/delete-widget-22492
     */
    public static function delete($widget_id, PodioClient $podio_client)
    {
        return $podio_client->delete("/widget/{$widget_id}");
    }

    /**
     * @see https://developers.podio.com/doc/widgets/get-widget-22489
     */
    public static function get($widget_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/widget/{$widget_id}"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/widgets/get-widgets-22494
     */
    public static function get_for($ref_type, $ref_id, PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/widget/{$ref_type}/{$ref_id}/"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/widgets/update-widget-22490
     */
    public static function update($widget_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->put("/widget/{$widget_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/widgets/update-widget-order-22495
     */
    public static function update_order($ref_type, $ref_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->put("/widget/{$ref_type}/{$ref_id}/order", $attributes);
    }
}
