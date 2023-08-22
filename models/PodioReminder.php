<?php
/**
 * @see https://developers.podio.com/doc/reminders
 */
class PodioReminder extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('reminder_id', 'integer', array('id' => true));
        $this->property('remind_delta', 'integer');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/reminders/get-reminder-3415569
     */
    public static function get_for(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return self::member($podio_client->get("/reminder/{$ref_type}/{$ref_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/reminders/create-or-update-reminder-3315055
     */
    public static function create(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array())
    {
        return $podio_client->put("/reminder/{$ref_type}/{$ref_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/reminders/create-or-update-reminder-3315055
     */
    public static function update(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array())
    {
        return $podio_client->put("/reminder/{$ref_type}/{$ref_id}", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/reminders/snooze-reminder-3321049
     */
    public static function snooze(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return $podio_client->post("/reminder/{$ref_type}/{$ref_id}/snooze");
    }

    /**
     * @see https://developers.podio.com/doc/reminders/delete-reminder-3315117
     */
    public static function delete(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return $podio_client->delete("/reminder/{$ref_type}/{$ref_id}");
    }
}
