<?php
/**
 * @see https://developers.podio.com/doc/calendar
 */
class PodioCalendarMute extends PodioObject {
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
   * @see https://developers.podio.com/doc/calendar/mute-objects-from-global-calendar-79418
   */
  public static function create($scope_type, $scope_id, $object_type) {
    return Podio::post("/calendar/mute/{$scope_type}/{$scope_id}/{$object_type}");
  }

  /**
   * @see https://developers.podio.com/doc/calendar/get-mutes-in-global-calendar-62730
   */
  public static function get_all() {
    return self::listing(Podio::get("/calendar/mute/"));
  }

  /**
   * @see https://developers.podio.com/doc/calendar/unmute-objects-from-the-global-calendar-79420
   */
  public static function delete($scope_type, $scope_id, $object_type) {
    return Podio::delete("/calendar/mute/{$scope_type}/{$scope_id}/{$object_type}");
  }

}
