<?php
/**
 * @see https://developers.podio.com/doc/files
 */
class PodioFile extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('file_id', 'integer', array('id' => true));
        $this->property('link', 'string');
        $this->property('perma_link', 'string');
        $this->property('thumbnail_link', 'string');
        $this->property('hosted_by', 'string');
        $this->property('name', 'string');
        $this->property('description', 'string');
        $this->property('mimetype', 'string');
        $this->property('size', 'integer');
        $this->property('context', 'hash');
        $this->property('created_on', 'datetime');
        $this->property('rights', 'array');

        $this->has_one('created_by', 'ByLine');
        $this->has_one('created_via', 'Via');
        $this->has_many('replaces', 'File');

        $this->init($attributes);
    }

    private function get_download_link($size = null)
    {
        return $size ? ($this->link . '/' . $size) : $this->link;
    }

    /**
     * Returns the raw bytes of a file. Beware: This is not a static method.
     * It can only be used after you have a PodioFile object.
     */
    public function get_raw($size = null, PodioClient $podio_client)
    {
        return $podio_client->get($this->get_download_link($size), array(), array('file_download' => true))->body;
    }

    /**
     * Returns the raw bytes of a file. Beware: This is not a static method.
     * It can only be used after you have a PodioFile object.
     *
     * In contrast to get_raw this method does use minimal memory (the result is stored in php://temp)
     * @return resource pointing at start of body (use fseek($resource, 0) to get headers as well)
     */
    public function get_raw_as_resource($size = null, PodioClient $podio_client)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $podio_client->get($this->get_download_link($size), array(), array('file_download' => true, 'return_raw_as_resource_only' => true));
    }

    /**
     * @see https://developers.podio.com/doc/files/upload-file-1004361
     */
    public static function upload($file_path, $file_name, PodioClient $podio_client)
    {
        return self::member($podio_client->post("/file/", array('filename' => $file_name, 'filepath' => $file_path), array('upload' => true, 'filesize' => filesize($file_path))), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/files/get-file-22451
     */
    public static function get($file_id, PodioClient $podio_client)
    {
        return self::member($podio_client->get("/file/{$file_id}"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/files/get-files-on-app-22472
     */
    public static function get_for_app($app_id, $attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/file/app/{$app_id}/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/files/get-files-on-space-22471
     */
    public static function get_for_space($space_id, $attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/file/space/{$space_id}/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/files/attach-file-22518
     */
    public static function attach($file_id, $attributes = array(), $options = array(), PodioClient $podio_client)
    {
        $url = $podio_client->url_with_options("/file/{$file_id}/attach", $options);
        return $podio_client->post($url, $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/files/replace-file-22450
     */
    public static function replace($file_id, $attributes = array(), PodioClient $podio_client)
    {
        return $podio_client->post("/file/{$file_id}/replace", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/files/copy-file-89977
     */
    public static function copy($file_id, PodioClient $podio_client)
    {
        return self::member($podio_client->post("/file/{$file_id}/copy"), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/files/get-files-4497983
     */
    public static function get_all($attributes = array(), PodioClient $podio_client)
    {
        return self::listing($podio_client->get("/file/", $attributes), $podio_client);
    }

    /**
     * @see https://developers.podio.com/doc/files/delete-file-22453
     */
    public static function delete($file_id, PodioClient $podio_client)
    {
        return $podio_client->delete("/file/{$file_id}");
    }
}
