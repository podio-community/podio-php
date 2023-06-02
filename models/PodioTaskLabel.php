<?php
/**
 * @see https://developers.podio.com/doc/tasks
 */
class PodioTaskLabel extends PodioObject
{
    public const DEFAULT_COLOR = 'E9E9E9';

    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('label_id', 'integer', array('id' => true));
        $this->property('text', 'string');
        $this->property('color', 'string');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/tasks/create-label-151265
     */
    public static function create($attributes = array(), PodioClient $podio_client)
    {
        if (!isset($attributes['color'])) {
            $attributes['color'] = PodioTaskLabel::DEFAULT_COLOR;
        }
        $body = $podio_client->post("/task/label/", $attributes)->json_body();
        return $body['label_id'];
    }

    /**
     * @see https://developers.podio.com/doc/tasks/get-labels-151534
     */
    public static function get_all(PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/task/label"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/tasks/delete-label-151302
     */
    public static function delete($label_id, PodioClient $podio_client)
    {
        return $podio_client->delete("/task/label/{$label_id}");
    }

    /**
     * @see https://developers.podio.com/doc/tasks/update-task-labels-151769
     */
    public static function update($label_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->put("/task/label/{$label_id}", $attributes);
    }
}
