<?php
/**
 * @see https://developers.podio.com/doc/tasks
 */
class PodioTask extends PodioObject {
  public function __construct($attributes = array()) {
    $this->property('task_id', 'integer', array('id' => true));
    $this->property('status', 'string');
    $this->property('group', 'string');
    $this->property('text', 'string');
    $this->property('description', 'string');
    $this->property('private', 'boolean');
    $this->property('due_on', 'datetime');
    $this->property('due_date', 'string');
    $this->property('due_time', 'string');
    $this->property('space_id', 'integer');
    $this->property('link', 'string');
    $this->property('created_on', 'datetime');
    $this->property('completed_on', 'datetime');
    $this->property('external_id', 'string');

    // For creating tasks
    $this->property('file_ids', 'array');
    $this->property('label_ids', 'array');
    $this->property('labels', 'array');

    $this->has_one('ref', 'Reference');
    $this->has_one('created_by', 'ByLine');
    $this->has_one('completed_by', 'ByLine');
    $this->has_one('created_via', 'Via');
    $this->has_one('deleted_via', 'Via');
    $this->has_one('completed_via', 'Via');
    $this->has_one('responsible', 'User');
    $this->has_one('reminder', 'Reminder');
    $this->has_one('recurrence', 'Recurrence');
    $this->has_many('labels', 'TaskLabel');
    $this->has_many('files', 'File');
    $this->has_many('comments', 'Comment');

    $this->init($attributes);
  }

  /**
   * @see https://developers.podio.com/doc/tasks/create-task-22419
   */
  public static function create($attributes = array()) {
    $url = "/task/";
    if (isset($attributes['silent']) && $attributes['silent'] == 1) {
      $url .= '?silent=1';
      unset($attributes['silent']);
    }
    return self::member(Podio::post($url, $attributes));
  }

  /**
   * @see https://developers.podio.com/doc/tasks/create-task-with-reference-22420
   */
  public static function create_for($ref_type, $ref_id, $attributes = array()) {
    $url = "/task/{$ref_type}/{$ref_id}/";
    if (isset($attributes['silent']) && $attributes['silent'] == 1) {
      $url .= '?silent=1';
      unset($attributes['silent']);
    }
    return self::member(Podio::post($url, $attributes));
  }

  /**
   * @see https://developers.podio.com/doc/tasks/get-task-22413
   */
  public static function get($task_id) {
    return self::member(Podio::get("/task/{$task_id}"));
  }

  /**
   * @see https://developers.podio.com/doc/tasks/get-tasks-77949
   */
  public static function get_all($attributes = array()) {
    return self::listing(Podio::get("/task/", $attributes));
  }

  /**
   * @see https://developers.podio.com/doc/tasks/delete-task-77179
   */
  public static function delete($task_id) {
    return Podio::delete("/task/{$task_id}");
  }

  /**
   * @see https://developers.podio.com/doc/tasks/remove-task-reference-6146114
   */
  public static function delete_ref($task_id) {
    return Podio::delete("/task/{$task_id}/ref");
  }

  /**
   * @see https://developers.podio.com/doc/tasks/update-task-10583674
   */
  public static function update($task_id, $attributes = array()) {
    return self::member(Podio::put("/task/{$task_id}", $attributes));
  }

  /**
   * @see https://developers.podio.com/doc/tasks/assign-task-22412
   */
  public static function assign($task_id, $attributes = array()) {
    return Podio::post("/task/{$task_id}/assign", $attributes);
  }

  /**
   * @see https://developers.podio.com/doc/tasks/complete-task-22432
   */
  public static function complete($task_id) {
    return Podio::post("/task/{$task_id}/complete");
  }

  /**
   * @see https://developers.podio.com/doc/tasks/incomplete-task-22433
   */
  public static function incomplete($task_id) {
    return Podio::post("/task/{$task_id}/incomplete");
  }

  /**
   * @see https://developers.podio.com/doc/tasks/rank-task-81015
   */
  public static function rank($task_id, $attributes = array()) {
    return Podio::post("/task/{$task_id}/rank", $attributes);
  }

  /**
   * @see https://developers.podio.com/doc/calendar/get-task-calendar-as-ical-10195650
   */
  public static function ical($task_id) {
    return Podio::get("/calendar/task/{$task_id}/ics/")->body;
  }

  /**
   * @see https://developers.podio.com/doc/tasks/get-task-summary-1612017
   */
  public static function get_summary($attributes = array()) {
    $result = Podio::get("/task/summary", $attributes)->json_body();
    $result['overdue']['tasks'] = self::listing($result['overdue']['tasks']);
    $result['today']['tasks'] = self::listing($result['today']['tasks']);
    $result['other']['tasks'] = self::listing($result['other']['tasks']);
    return $result;
  }

  /**
   * @see https://developers.podio.com/doc/tasks/get-task-summary-for-personal-1657217
   */
  public static function get_summary_personal($attributes = array()) {
    $result = Podio::get("/task/personal/summary", $attributes)->json_body();
    $result['overdue']['tasks'] = self::listing($result['overdue']['tasks']);
    $result['today']['tasks'] = self::listing($result['today']['tasks']);
    $result['other']['tasks'] = self::listing($result['other']['tasks']);
    return $result;
  }

  /**
   * @see https://developers.podio.com/doc/tasks/get-task-summary-for-organization-1612063
   */
  public static function get_summary_for_org($org_id, $attributes = array()) {
    $result = Podio::get("/task/org/{$org_id}/summary", $attributes)->json_body();
    $result['overdue']['tasks'] = self::listing($result['overdue']['tasks']);
    $result['today']['tasks'] = self::listing($result['today']['tasks']);
    $result['other']['tasks'] = self::listing($result['other']['tasks']);
    return $result;
  }

  /**
   * @see https://developers.podio.com/doc/tasks/get-task-summary-for-space-1612130
   */
  public static function get_summary_for_space($space_id, $attributes = array()) {
    $result = Podio::get("/task/space/{$space_id}/summary", $attributes)->json_body();
    $result['overdue']['tasks'] = self::listing($result['overdue']['tasks']);
    $result['today']['tasks'] = self::listing($result['today']['tasks']);
    $result['other']['tasks'] = self::listing($result['other']['tasks']);
    return $result;
  }

  /**
   * @see https://developers.podio.com/doc/tasks/get-task-summary-for-reference-1657980
   */
  public static function get_summary_for($ref_type, $ref_id, $attributes = array()) {
    $result = Podio::get("/task/{$ref_type}/{$ref_id}/summary", $attributes)->json_body();
    $result['overdue']['tasks'] = self::listing($result['overdue']['tasks']);
    $result['today']['tasks'] = self::listing($result['today']['tasks']);
    $result['other']['tasks'] = self::listing($result['other']['tasks']);
    return $result;
  }

}
