<?php
/**
 * @see https://developers.podio.com/doc/stream
 */
class PodioStreamMute extends PodioObject {
  public function __construct($attributes = array()) {
    $this->property('id', 'integer');
    $this->property('type', 'string');
    $this->property('title', 'string');
    $this->property('data', 'hash');
    $this->property('item', 'boolean');
    $this->property('status', 'boolean');
    $this->property('task', 'boolean');

    $this->init($attributes);
  }

  /**
   * @see https://developers.podio.com/doc/stream/get-mutes-in-global-stream-62742
   */
  public static function get_all($attributes = array()) {
    return self::listing(Podio::get("/stream/mute/v2/", $attributes));
  }

  /**
   * @see https://developers.podio.com/doc/stream/mute-object-from-global-stream-79424
   */
  public static function create($scope_type, $scope_id, $object_type = null) {
    $url = "/stream/mute/{$scope_type}/{$scope_id}";
    if ($object_type) {
      $url += "/{$object_type}";
    }
    return Podio::post($url);
  }

  /**
   * @see https://developers.podio.com/doc/stream/unmute-objects-from-the-global-stream-79426
   */
  public static function delete($scope_type, $scope_id, $object_type = null) {
    $url = "/stream/mute/{$scope_type}/{$scope_id}";
    if ($object_type) {
      $url += "/{$object_type}";
    }
    return Podio::delete($url);
  }

}
