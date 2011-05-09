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
   * Creates a new task without reference to an object
   *
   * @param $text The text of the task
   * @param $description Description of task, if any
   * @param $due_date The due date of the task, leave blank for no due date
   * @param $responsible The user id of the person responsible
   * @param $file_ids The list of files to attach to this task
   * @param $label_ids The list labels to put on the task
   * @param $external_id Any external id for the task, if from another system
   */
  public function create($text, $description = '', $due_date = '', $responsible = NULL, $file_ids = array(), $label_ids = array()) {
    $data = array('text' => $text, 'responsible' => $responsible);
    if ($description) {
      $data['description'] = $description;
    }
    if ($due_date) {
      $data['due_date'] = $due_date;
    }
    if ($file_ids) {
      $data['file_ids'] = $file_ids;
    }
    if ($label_ids) {
      $data['label_ids'] = $label_ids;
    }
    
    if ($response = $this->podio->request('/task/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Creates a new task with reference to an object
   *
   * @param $ref_type "item" or "status" or "space" to create task on these
   * @param $ref_id The item id or status id or space id
   * @param $text The text of the task
   * @param $description Description of task, if any
   * @param $private True if the task should be private, false otherwise
   * @param $due_date The due date of the task, leave blank for no due date
   * @param $responsible The user id of the person responsible
   * @param $file_ids The list of files to attach to this task
   * @param $label_ids The list labels to put on the task
   * @param $external_id Any external id for the task, if from another system
   *
   * @return Array with the task id of the newly created task
   */
  public function createWithReference($ref_type, $ref_id, $text, $description, $private = FALSE, $due_date = '', $responsible = NULL, $file_ids = array(), $label_ids = array()) {
    $data = array('text' => $text, 'responsible' => $responsible, 'private' => $private);
    if ($description) {
      $data['description'] = $description;
    }
    if ($due_date) {
      $data['due_date'] = $due_date;
    }
    if ($file_ids) {
      $data['file_ids'] = $file_ids;
    }
    if ($label_ids) {
      $data['label_ids'] = $label_ids;
    }

    if ($response = $this->podio->request('/task/'.$ref_type.'/'.$ref_id.'/', $data, HTTP_Request2::METHOD_POST)) {
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
   * @param $due_date: The from and to date the task should be due between.
   * @param $created_on: The from and to date the task should be 
   *                     created between.
   * @param $created_by: Array. The entities that created the task.
   * @param $responsible: Array. The user ids that are responsible 
   *                             for the task.
   * @param $reassigned: 1 to only return tasks the active user has assigned 
   *                     to someone else, 0 to only return tasks that the 
   *                     active user has not assigned to someone else.
   * @param $reference: Array. The list of references on the form "type:id"
   * @param $org: Array. The ids of the orgs the tasks are related to.
   * @param $space: Array. The ids of the spaces the tasks are related to.
   * @param $completed: 1 to only return completed tasks, 
   *                    0 to return open tasks.
   * @param $completed_on: The from and to date the task should 
   *                       be completed between.
   * @param $completed_by: Array. The entities that completed the task.
   * @param $files: 1 to only return tasks with files, 
   *                0 to only return tasks with no files
   * @param $created_via: The id of the client the task was created via.
   * @param $label: The id of the a required label on the tasks.
   * @param $external_id: The external id of the task
   * @param $grouping: The grouping to use. Valid options are "due_date", 
   *                   "created_by", "responsible", "app",  "space" and "org".
   * @param $limit: The maximum number of tasks to return
   * @param $offset: The offset into the tasks to return
   * @param $view: The level of information to return.
   */
  public function getTasks($limit = 20, $offset = 0, $grouping = 'due_date', $due_date = NULL, $created_on = NULL, $created_by = NULL, $responsible = NULL, $reassigned = NULL, $reference = NULL, $org = NULL, $space = NULL, $completed = NULL, $completed_on = NULL, $completed_by = NULL, $files = NULL, $created_via = NULL, $label = NULL, $external_id = NULL, $view = 'full') {
    $data = array('limit' => $limit, 'offset' => $offset, 'grouping' => $grouping);
    if ($due_date) {
      $data['due_date'] = $due_date;
    }
    if ($created_on) {
      $data['created_on'] = $created_on;
    }
    if ($created_by) {
      $data['created_by'] = implode(';', $created_by);
    }
    if ($responsible) {
      $data['responsible'] = implode(';', $responsible);
    }
    if ($reassigned) {
      $data['reassigned'] = $reassigned;
    }
    if ($reference) {
      $data['reference'] = implode(';', $reference);
    }
    if ($org) {
      $data['org'] = implode(',', $org);
    }
    if ($space) {
      $data['space'] = implode(',', $space);
    }
    if ($completed) {
      $data['completed'] = $completed;
    }
    if ($completed_on) {
      $data['completed_on'] = $completed_on;
    }
    if ($completed_by) {
      $data['completed_by'] = implode(';', $completed_by);
    }
    if ($files) {
      $data['files'] = $files;
    }
    if ($created_via) {
      $data['created_via'] = $created_via;
    }
    if ($label) {
      $data['label'] = $label;
    }
    if ($external_id) {
      $data['external_id'] = $external_id;
    }
    if ($view) {
      $data['view'] = $view;
    }
    if ($response = $this->podio->request('/task/', $data)) {
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
   * Updates the description of the task to the given value
   *
   * @param $task_id The id of the task to act on
   * @param $description The new description
   */
  public function updateDescription($task_id, $description) {
    if ($response = $this->podio->request('/task/'.$task_id.'/description', array('description' => $description), HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
  }

  /**
   * Attached this task to an object. This is only valid for tasks that 
   * are not currently attached to any object.
   *
   * @param $task_id The id of the task to act on
   * @param $ref_type The object type to attach task to
   * @param $ref_id The id of the reference
   */
  public function updateReference($task_id, $ref_type, $ref_id) {
    if ($response = $this->podio->request('/task/'.$task_id.'/ref', array('ref_type' => $ref_type, 'ref_id' => $ref_id), HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
  }
  
  /**
   * Updates the task with new labels
   *
   * @param $task_id The id of the task to act on
   * @param $label_ids List of label ids to use for task
   */
  public function updateLabels($task_id, $label_ids = array()) {
    if ($response = $this->podio->request('/task/'.$task_id.'/label/', $label_ids, HTTP_Request2::METHOD_PUT)) {
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
  public function assign($task_id, $responsible = NULL) {
    if ($response = $this->podio->request('/task/'.$task_id.'/assign', array('responsible' => $responsible), HTTP_Request2::METHOD_POST)) {
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
   * This is used to delete a task.
   *
   * @param $task_id Id of the task to delete
   */
  public function delete($task_id) {
    if ($response = $this->podio->request('/task/'.$task_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Creates a new personal label for the user.
   *
   * @param $text The name of the new label
   * @param $color The color of the label in hex format (xxxxxx)
   */
  public function createLabel($text, $color) {
    $this->podio->request('/task/label/', array('text' => $text, 'color' => $color), HTTP_Request2::METHOD_POST);
  }

  /**
   * This is used to delete a task label.
   *
   * @param $label_id Id of the label to delete
   */
  public function deleteLabel($label_id) {
    if ($response = $this->podio->request('/task/label/'.$label_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Returns the users task labels.
   */
  public function getLabels() {
    if ($response = $this->podio->request('/task/label/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the due date of the task to the given value
   *
   * @param $label_id Id of the label to update
   * @param $text The name of the new label
   * @param $color The color of the label in hex format (xxxxxx)
   */
  public function updateLabel($label_id, $text, $color) {
    if ($response = $this->podio->request('/task/label/'.$label_id, array('text' => $text, 'color' => $color), HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
  }
  
  /**
   * Mark the completed task as no longer being completed.
   *
   * @param $task_id The id of the task to act on
   * @param $after The task the the updated task has to be after
   * @param $before The task the the updated task has to be before
   */
  public function rank($task_id, $after, $before) {
    $this->podio->request('/task/'.$task_id.'/rank', array('after' => $after, 'before' => $before), HTTP_Request2::METHOD_POST);
  }
}

