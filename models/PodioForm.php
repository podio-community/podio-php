<?php
/**
 * @see https://developers.podio.com/doc/forms
 */
class PodioForm extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('form_id', 'integer', array('id' => true));
        $this->property('app_id', 'integer');
        $this->property('space_id', 'integer');
        $this->property('status', 'string');
        $this->property('settings', 'hash');
        $this->property('domains', 'array');
        $this->property('fields', 'array');
        $this->property('attachments', 'boolean');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/forms/activate-form-1107439
     */
    public static function activate($form_id, PodioClient $podio_client)
    {
        return $podio_client->post("/form/{$form_id}/activate");
    }

    /**
     * @see https://developers.podio.com/doc/forms/deactivate-form-1107378
     */
    public static function deactivate($form_id, PodioClient $podio_client)
    {
        return $podio_client->post("/form/{$form_id}/deactivate");
    }

    /**
     * @see https://developers.podio.com/doc/forms/create-form-53803
     */
    public static function create($app_id, $attributes = array(), PodioClient $podio_client)
    {
        return self::member($podio_client->post("/form/app/{$app_id}/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/forms/delete-from-53810
     */
    public static function delete($form_id, PodioClient $podio_client)
    {
        return $podio_client->delete("/form/{$form_id}");
    }

    /**
     * @see https://developers.podio.com/doc/forms/get-form-53754
     */
    public static function get($form_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/form/{$form_id}"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/forms/get-forms-53771
     */
    public static function get_for_app($app_id, PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/form/app/{$app_id}/"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/forms/update-form-53808
     */
    public static function update($form_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->put("/form/{$form_id}", $attributes);
    }
}
