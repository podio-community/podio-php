<?php
/**
 * @see https://developers.podio.com/doc/grants
 */
class PodioGrant extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('grant_id', 'integer', array('id' => true));
        $this->property('ref_type', 'string');
        $this->property('ref_id', 'integer');
        $this->property('people', 'hash');
        $this->property('action', 'string');
        $this->property('message', 'string');
        $this->property('created_on', 'datetime');

        $this->has_one('created_by', 'ByLine');
        $this->has_one('user', 'User');
        $this->has_one('ref', 'Reference');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/grants/get-grants-on-object-16491464
     */
    public static function get_for($ref_type, $ref_id, PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/grant/{$ref_type}/{$ref_id}/"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/grants/get-own-grant-information-16490748
     */
    public static function get_own($ref_type, $ref_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/grant/{$ref_type}/{$ref_id}/own"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/grants/get-own-grants-on-org-22330891
     */
    public static function get_own_on_org($org_id, PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/grant/org/{$org_id}/own/"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/grants/get-grants-to-user-on-space-19389786
     */
    public static function get_for_user_on_space($space_id, $user_id, PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/grant/space/{$space_id}/user/{$user_id}/"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/grants/create-grant-16168841
     */
    public static function create($ref_type, $ref_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->post("/grant/{$ref_type}/{$ref_id}", $attributes)->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/grants/remove-grant-16496711
     */
    public static function delete($ref_type, $ref_id, $user_id, PodioClient $podio_client)
    {
        return $podio_client->delete("/grant/{$ref_type}/{$ref_id}/{$user_id}");
    }
}
