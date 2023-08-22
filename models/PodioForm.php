<?php
/**
 * @see https://developers.podio.com/doc/forms
 */
class PodioForm extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
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
    public static function activate(PodioClient $podio_client, $form_id)
    {
        return $podio_client->post("/form/{$form_id}/activate");
    }

    /**
     * @see https://developers.podio.com/doc/forms/deactivate-form-1107378
     */
    public static function deactivate(PodioClient $podio_client, $form_id)
    {
        return $podio_client->post("/form/{$form_id}/deactivate");
    }

    /**
     * @see https://developers.podio.com/doc/forms/create-form-53803
     */
    public static function create(PodioClient $podio_client, $app_id, $attributes = array())
    {
        return self::member($podio_client->post("/form/app/{$app_id}/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/forms/delete-from-53810
     */
    public static function delete(PodioClient $podio_client, $form_id)
    {
        return $podio_client->delete("/form/{$form_id}");
    }

    /**
     * @see https://developers.podio.com/doc/forms/get-form-53754
     */
    public static function get(PodioClient $podio_client, $form_id)
    {
        return self::member($podio_client->get("/form/{$form_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/forms/get-forms-53771
     */
    public static function get_for_app(PodioClient $podio_client, $app_id)
    {
        return self::listing($podio_client->get("/form/app/{$app_id}/"));
    }

    /**
     * @see https://developers.podio.com/doc/forms/update-form-53808
     */
    public static function update(PodioClient $podio_client, $form_id, $attributes = array())
    {
        return $podio_client->put("/form/{$form_id}", $attributes);
    }
}
