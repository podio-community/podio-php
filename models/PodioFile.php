<?php
/**
 * @see https://developers.podio.com/doc/files
 */
class PodioFile extends PodioObject {
  public function __construct($attributes = array()) {
    $this->property('file_id', 'integer');
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
   * @see https://developers.podio.com/doc/files/upload-file-1004361
   */
  public static function upload($file_path, $file_name) {
    $body = Podio::post("/file/", array('source' => '@'.realpath($filepath), 'filename' => $filename), array('upload' => TRUE, 'filesize' => filesize($filepath)));
    return $body['file_id'];
  }

  /**
   * @see https://developers.podio.com/doc/files/get-file-22451
   */
  public static function get($file_id) {
    return self::member(Podio::get("/file/{$file_id}"));
  }

  /**
   * @see https://developers.podio.com/doc/files/download-file-1004147
   */
  public static function get_raw($file_id) {
    return Podio::get("/file/{$file_id}/raw")->body;
  }

  /**
   * @see https://developers.podio.com/doc/files/get-files-on-app-22472
   */
  public static function get_for_app($app_id, $attributes) {
    return self::listing(Podio::get("/file/app/{$app_id}/", $attributes));
  }

  /**
   * @see https://developers.podio.com/doc/files/get-files-on-space-22471
   */
  public static function get_for_space($space_id, $attributes) {
    return self::listing(Podio::get("/file/space/{$space_id}/", $attributes));
  }

  /**
   * @see https://developers.podio.com/doc/files/attach-file-22518
   */
  public static function attach($file_id, $attributes) {
    return Podio::post("/file/{$file_id}/attach", $attributes);
  }

  /**
   * @see https://developers.podio.com/doc/files/replace-file-22450
   */
  public static function replace($file_id, $attributes) {
    return Podio::post("/file/{$file_id}/replace", $attributes);
  }

  /**
   * @see https://developers.podio.com/doc/files/attach-file-22518
   */
  public static function copy($file_id) {
    $body = Podio::post("/file/{$file_id}/copy");
    return $body['file_id'];
  }

  /**
   * @see https://developers.podio.com/doc/files/get-files-4497983
   */
  public static function get_all($attributes) {
    return self::listing(Podio::get("/file/", $attributes));
  }

  /**
   * @see https://developers.podio.com/doc/files/delete-file-22453
   */
  public static function delete($file_id) {
    return Podio::delete("/file/{$file_id}");
  }

}
