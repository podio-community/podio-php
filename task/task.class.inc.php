<?php

/**
 * Tasks are used to track what work has to be done.
 */
class PodioTaskAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Creates a new task, optionally with a reference to another object
   *
   * @param $text The text of the task
   * @param $private True if the task should be private, false otherwise
   * @param $due_date The due date of the task, leave blank for no due date
   * @param $responsible The user id of the person responsible, 
   *                     leave blank for self
   * @param $ref_type "item" or "status" or "space" to create task on these
   * @param $ref_id The item id or status id or space id
   *
   * @return Array with the task id of the newly created task
   */
  public function create($text, $private = FALSE, $due_date = '', $responsible = NULL, $ref_type = NULL, $ref_id = NULL) {
    $url = '/task/';
    if ($ref_id && $ref_type) {
      $url = '/task/'.$ref_type.'/'.$ref_id.'/';
    }
    $data = array('text' => $text, 'private' => $private);
    if ($due_date) {
      $data['due_date'] = $due_date;
    }
    if ($responsible) {
      $data['responsible'] = (int)$responsible;
    }
    
    if ($response = $this->podio->request($url, $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the task with the given id.
   *
   * @param $task_id The id of the task to retrieve
   *
   * @return A task object
   */
  public function get($task_id) {
    if ($response = $this->podio->request('/task/'.$task_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Gets a list of tasks with a reference to the given object. This will 
   * return both active and completed tasks. The reference will not be 
   * set on the individual tasks.
   *
   * @param $ref_type "item" or "status"
   * @param $ref_id The status id or item id
   *
   * @return An array of task objects
   */
  public function getByRef($ref_type, $ref_id) {
    if ($response = $this->podio->request('/task/'.$ref_type.'/'.$ref_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Update the private flag on the given task.
   *
   * @param $task_id The id of the task to act on
   * @param $private True if the task should be private, false otherwise
   */
  public function updatePrivacy($task_id, $private) {
    if ($response = $this->podio->request('/task/'.$task_id.'/private', array('private' => $private), HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
  }

  /**
   * Updates the text of the task.
   *
   * @param $task_id The id of the task to act on
   * @param $text The new text of the task
   */
  public function updateText($task_id, $text) {
    if ($response = $this->podio->request('/task/'.$task_id.'/text', array('text' => $text), HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
  }
  
  /**
   * Updates the due date of the task to the given value
   *
   * @param $task_id The id of the task to act on
   * @param $due The new due date, leave blank if the task should have no due date
   */
  public function updateDue($task_id, $due) {
    if ($response = $this->podio->request('/task/'.$task_id.'/due_date', array('due_date' => $due), HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
  }
  
  /**
   * Assigns the task to another user. This makes the user responsible for 
   * the task and its completion.
   *
   * @param $task_id The id of the task to act on
   * @param $responsible The user id of the user to assign to
   */
  public function updateAssign($task_id, $responsible) {
    if ($response = $this->podio->request('/task/'.$task_id.'/assign', array('responsible' => (int)$responsible), HTTP_Request2::METHOD_POST)) {
      return TRUE;
    }
  }
  
  /**
   * Returns the active tasks of the user. This is the tasks where the user 
   * is responsible. The tasks will be sorted by due date and creation time, 
   * and grouped by their due date status.
   *
   * @return An array of task objects, collected by due date categories
   */
  public function getActive() {
    if ($response = $this->podio->request('/task/active/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the total task count for the active user.
   *
   * @param $space_id The id of the space to get totals for
   *                  Leave blank to get global totals
   *
   * @return An array of task totals
   */
  public function getTotal($space_id = NULL) {
    $url = '/task/total';
    $data = array();
    if ($space_id) {
      $data['space_id'] = $space_id;
    }
    if ($response = $this->podio->request($url, $data)) {
      $list = json_decode($response->getBody(), TRUE);
    }
    return $list;
  }

  /**
   * Returns the total task count for the active user.
   *
   * @param $space_id The id of the space to get totals for
   *                  Leave blank to get global totals
   *
   * @return An array of task totals
   */
  public function getTotalV2($space_id = NULL) {
    $data = array();
    if ($space_id) {
      $data['space'] = $space_id;
    }
    if ($response = $this->podio->request('/task/total/', $data)) {
      $list = json_decode($response->getBody(), TRUE);
    }
    return $list;
  }
  
  /**
   * Returns the tasks that are started and where the active user 
   * is the responsible.
   *
   * @return An array of task objects, collected by due date categories
   */
  public function getStarted() {
    if ($response = $this->podio->request('/task/started/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the tasks that is completed and where the active user 
   * is responsible.
   *
   * @return An array of task objects, collected by due date categories
   */
  public function getCompleted() {
    if ($response = $this->podio->request('/task/completed/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the active tasks that the user has assigned to another user.
   *
   * @return An array of task objects, collected by due date categories
   */
  public function getAssignedActive() {
    if ($response = $this->podio->request('/task/assigned/active/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the completed tasks the active user has assigned to other users.
   *
   * @return An array of task objects, collected by due date categories
   */
  public function getAssignedCompleted() {
    if ($response = $this->podio->request('/task/assigned/completed/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the tasks that are related to the space. It includes tasks 
   * with a direct reference to the space, and tasks with an indirect 
   * reference to the space (like items and status updates).
   *
   * @param $space_id The id of the space to get tasks for
   * @param $sort_by The sort order, can be either "responsible" or "due_date"
   *
   * @return An array of task objects, collected by due date or responsible
   */
  public function getBySpace($space_id, $sort_by = 'due_date') {
    if ($response = $this->podio->request('/task/in_space/'.$space_id.'/', array('sort_by' => $sort_by))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Mark the given task as completed.
   *
   * @param $task_id The id of the task to act on
   */
  public function complete($task_id) {
    $this->podio->request('/task/'.$task_id.'/complete', array(), HTTP_Request2::METHOD_POST);
  }

  /**
   * Mark the completed task as no longer being completed.
   *
   * @param $task_id The id of the task to act on
   */
  public function incomplete($task_id) {
    $this->podio->request('/task/'.$task_id.'/incomplete', array(), HTTP_Request2::METHOD_POST);
  }
  
  /**
   * Indicate that work has started on the given task.
   *
   * @param $task_id The id of the task to act on
   */
  public function start($task_id) {
    $this->podio->request('/task/'.$task_id.'/start', array(), HTTP_Request2::METHOD_POST);
  }
  
  /**
   * Indicate that worked has stopped on the given task.
   *
   * @param $task_id The id of the task to act on
   */
  public function stop($task_id) {
    $this->podio->request('/task/'.$task_id.'/stop', array(), HTTP_Request2::METHOD_POST);
  }
}

