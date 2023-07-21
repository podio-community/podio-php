<?php
/**
 * @see https://developers.podio.com/doc/flows
 */
class PodioFlow extends PodioObject
{
    public function __construct(PodioClient $podio_clint, $attributes = array())
    {
        parent::__construct($podio_clint);
        $this->property('flow_id', 'integer', array('id' => true));
        $this->property('name', 'string');
        $this->property('type', 'string');
        $this->property('config', 'hash');
        $this->property('effects', 'hash');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/flows/get-flow-by-id-26312313
     */
    public static function get(PodioClient $podio_client, $flow_id)
    {
        return $podio_client->get("/flow/{$flow_id}")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/flows/add-new-flow-26309928
     */
    public static function create(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array())
    {
        return $podio_client->post("/flow/{$ref_type}/{$ref_id}/", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/flows/update-flow-26310901
     */
    public static function update(PodioClient $podio_client, $flow_id, $attributes = array())
    {
        return $podio_client->put("/flow/{$flow_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/flows/delete-flow-32929229
     */
    public static function delete(PodioClient $podio_client, $flow_id)
    {
        return $podio_client->delete("/flow/{$flow_id}");
    }

    /**
     * @see https://developers.podio.com/doc/flows/get-flows-26312304
     */
    public static function get_flows(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return $podio_client->get("/flow/{$ref_type}/{$ref_id}/")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/flows/get-effect-attributes-239234961
     */
    public static function get_effect_attributes(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return $podio_client->post("/flow/{$ref_type}/{$ref_id}/effect/attributes")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/flows/get-flow-context-26313659
     */
    public static function get_flow_context(PodioClient $podio_client, $flow_id)
    {
        return $podio_client->get("/flow/{$flow_id}/context/")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/flows/get-possible-attributes-33060379
     */
    public static function get_possible_attributes(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return $podio_client->post("/flow/{$ref_type}/{$ref_id}/attributes/")->json_body();
    }
}
