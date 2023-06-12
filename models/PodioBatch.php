<?php
/**
 * @see https://developers.podio.com/doc/batch
 */
class PodioBatch extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('batch_id', 'integer', array('id' => true));
        $this->property('name', 'string');
        $this->property('plugin', 'string');
        $this->property('status', 'string');
        $this->property('completed', 'integer');
        $this->property('skipped', 'integer');
        $this->property('failed', 'integer');
        $this->property('created_on', 'datetime');
        $this->property('started_on', 'datetime');
        $this->property('ended_on', 'datetime');

        $this->has_one('file', 'File');
        $this->has_one('app', 'App');
        $this->has_one('space', 'Space');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/batch/get-batch-6144225
     */
    public static function get($batch_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/batch/{$batch_id}"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/batch/get-running-batches-15856178
     */
    public static function get_for($ref_type, $ref_id, $plugin, PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/batch/{$ref_type}/{$ref_id}/{$plugin}/running/"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/batch/get-batches-6078877
     */
    public static function get_all($attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/batch/", $attributes), $podio_client);
    }
}
