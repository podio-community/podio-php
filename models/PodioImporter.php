<?php
/**
 * @see https://developers.podio.com/doc/importer
 */
class PodioImporter extends PodioObject
{
    /**
     * @see https://developers.podio.com/doc/importer/get-info-5929504
     */
    public static function info(PodioClient $podio_client, $file_id)
    {
        return $podio_client->get("/importer/{$file_id}/info")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/importer/get-preview-5936702
     */
    public static function preview(PodioClient $podio_client, $file_id, $row, $attributes = array())
    {
        return $podio_client->post("/importer/{$file_id}/preview/{$row}", $attributes)->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/importer/import-app-items-212899
     */
    public static function process_app(PodioClient $podio_client, $file_id, $app_id, $attributes = array())
    {
        $body = $podio_client->post("/importer/{$file_id}/item/app/{$app_id}", $attributes)->json_body();
        return $body['batch_id'];
    }

    /**
     * @see https://developers.podio.com/doc/importer/import-space-contacts-4261072
     */
    public static function process_contacts(PodioClient $podio_client, $file_id, $space_id, $attributes = array())
    {
        return $podio_client->post("/importer/{$file_id}/contact/space/{$space_id}", $attributes)->json_body();
    }
}
