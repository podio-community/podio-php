<?php
/**
 * @see https://developers.podio.com/doc/files
 */
class PodioFile extends PodioObject {
  public function __construct($attributes = array()) {
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

  /**
   * Returns the raw bytes of a file. Beware: This is not a static method.
   * It can only be used after you have a PodioFile object.
   */
  public function get_raw() {
    return Podio::get($this->link, array(), array('file_download' => true))->body;
  }

  /**
   * @see https://developers.podio.com/doc/files/upload-file-1004361
   */
  public static function upload($file_path, $file_name) {
    return self::member(Podio::post("/file/v2/", array('source' => '@'.realpath($file_path), 'filename' => $file_name), array('upload' => TRUE, 'filesize' => filesize($file_path))));
  }

  /**
   * @see https://developers.podio.com/doc/files/get-file-22451
   */
  public static function get($file_id) {
    return self::member(Podio::get("/file/{$file_id}"));
  }

  /**
   * @see https://developers.podio.com/doc/files/get-files-on-app-22472
   */
  public static function get_for_app($app_id, $attributes = array()) {
    return self::listing(Podio::get("/file/app/{$app_id}/", $attributes));
  }

  /**
   * @see https://developers.podio.com/doc/files/get-files-on-space-22471
   */
  public static function get_for_space($space_id, $attributes = array()) {
    return self::listing(Podio::get("/file/space/{$space_id}/", $attributes));
  }

  /**
   * @see https://developers.podio.com/doc/files/attach-file-22518
   */
  public static function attach($file_id, $attributes = array(), $options = array()) {
    $url = "/file/{$file_id}/attach";
    if (isset($options['silent']) && $options['silent'] == 1) {
      $url .= '?silent=1';
    }
    return Podio::post($url, $attributes);
  }

  /**
   * @see https://developers.podio.com/doc/files/replace-file-22450
   */
  public static function replace($file_id, $attributes = array()) {
    return Podio::post("/file/{$file_id}/replace", $attributes);
  }

  /**
   * @see https://developers.podio.com/doc/files/copy-file-89977
   */
  public static function copy($file_id) {
    return self::member(Podio::post("/file/{$file_id}/copy"));
  }

  /**
   * @see https://developers.podio.com/doc/files/get-files-4497983
   */
  public static function get_all($attributes = array()) {
    return self::listing(Podio::get("/file/", $attributes));
  }

  /**
   * @see https://developers.podio.com/doc/files/delete-file-22453
   */
  public static function delete($file_id) {
    return Podio::delete("/file/{$file_id}");
  }

}
