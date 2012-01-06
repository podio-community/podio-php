<?php

/**
 * Tasks are used to track what work has to be done.
 */
class PodioTask {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Creates a new task without reference to an object
   */
  public function create($attributes = array()) {
    if ($response = $this->podio->post('/task/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Creates a new task with reference to an object
   */
  public function createWithReference($ref_type, $ref_id, $attributes = array()) {
    if ($response = $this->podio->post('/task/'.$ref_type.'/'.$ref_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the task with the given id.
   */
  public function get($task_id) {
    if ($response = $this->podio->get('/task/'.$task_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Get a list of tasks.
   */
  public function getTasks($attributes = array()) {
    if ($response = $this->podio->get('/task/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Gets a list of tasks with a reference to the given object. This will 
   * return both active and completed tasks. The reference will not be 
   * set on the individual tasks.
   */
  public function getByRef($ref_type, $ref_id) {
    if ($response = $this->podio->get('/task/'.$ref_type.'/'.$ref_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the task summary for the active user
   */
  public function getSummary() {
    if ($response = $this->podio->get('/task/summary')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the task summary for the organization for the active user
   */
  public function getOrgSummary($org_id) {
    if ($response = $this->podio->get('/task/org/'.$org_id.'/summary')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the task summary for the space for the active user
   */
  public function getSpaceSummary($space_id) {
    if ($response = $this->podio->get('/task/space/'.$space_id.'/summary')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the task summary for the space for the reference
   */
  public function getRefSummary($ref_type, $ref_id) {
    if ($response = $this->podio->get('/task/'.$ref_type.'/'.$ref_id.'/summary')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the tasks summary for personal tasks and tasks on 
   * personal spaces and sub-orgs.
   */
  public function getPersonalSummary() {
    if ($response = $this->podio->get('/task/personal/summary')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Update the private flag on the given task.
   */
  public function updatePrivacy($task_id, $attributes = array()) {
    if ($response = $this->podio->put('/task/'.$task_id.'/private', $attributes)) {
      return TRUE;
    }
  }

  /**
   * Updates the text of the task.
   */
  public function updateText($task_id, $attributes = array()) {
    if ($response = $this->podio->put('/task/'.$task_id.'/text', $attributes)) {
      return TRUE;
    }
  }
  
  /**
   * Updates the due date of the task to the given value
   */
  public function updateDue($task_id, $attributes = array()) {
    if ($response = $this->podio->put('/task/'.$task_id.'/due_date', $attributes)) {
      return TRUE;
    }
  }

  /**
   * Updates the description of the task to the given value
   */
  public function updateDescription($task_id, $attributes = array()) {
    if ($response = $this->podio->put('/task/'.$task_id.'/description', $attributes)) {
      return TRUE;
    }
  }

  /**
   * Attached this task to an object. This is only valid for tasks that 
   * are not currently attached to any object.
   */
  public function updateReference($task_id, $attributes = array()) {
    if ($response = $this->podio->put('/task/'.$task_id.'/ref', $attributes)) {
      return TRUE;
    }
  }
  
  /**
   * Updates the task with new labels
   */
  public function updateLabels($task_id, $attributes = array()) {
    if ($response = $this->podio->put('/task/'.$task_id.'/label/', $attributes)) {
      return TRUE;
    }
  }
  
  /**
   * Assigns the task to another user. This makes the user responsible for 
   * the task and its completion.
   */
  public function assign($task_id, $attributes = array()) {
    if ($response = $this->podio->post('/task/'.$task_id.'/assign', $attributes)) {
      return TRUE;
    }
  }
  
  /**
   * Returns the active tasks of the user. This is the tasks where the user 
   * is responsible. The tasks will be sorted by due date and creation time, 
   * and grouped by their due date status.
   */
  public function getActive() {
    if ($response = $this->podio->get('/task/active/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the total task count for the active user.
   */
  public function getTotal($attributes = array()) {
    if ($response = $this->podio->get('/task/total/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the tasks that are started and where the active user 
   * is the responsible.
   */
  public function getStarted() {
    if ($response = $this->podio->get('/task/started/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the tasks that is completed and where the active user 
   * is responsible.
   */
  public function getCompleted() {
    if ($response = $this->podio->get('/task/completed/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the active tasks that the user has assigned to another user.
   */
  public function getAssignedActive() {
    if ($response = $this->podio->get('/task/assigned/active/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the completed tasks the active user has assigned to other users.
   */
  public function getAssignedCompleted() {
    if ($response = $this->podio->get('/task/assigned/completed/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the tasks that are related to the space. It includes tasks 
   * with a direct reference to the space, and tasks with an indirect 
   * reference to the space (like items and status updates).
   */
  public function getBySpace($space_id, $attributes = array()) {
    if ($response = $this->podio->get('/task/in_space/'.$space_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Mark the given task as completed.
   */
  public function complete($task_id) {
    if ($response = $this->podio->post('/task/'.$task_id.'/complete')) {
      return TRUE;
    }
  }

  /**
   * Mark the completed task as no longer being completed.
   */
  public function incomplete($task_id) {
    if ($response = $this->podio->post('/task/'.$task_id.'/incomplete')) {
      return TRUE;
    }
  }
  
  /**
   * This is used to delete a task.
   */
  public function delete($task_id) {
    if ($response = $this->podio->delete('/task/'.$task_id)) {
      return TRUE;
    }
  }
  
  /**
   * Creates a new personal label for the user.
   */
  public function createLabel($attributes = array()) {
    if ($response = $this->podio->post('/task/label/', $attributes)) {
      return TRUE;
    }
  }

  /**
   * This is used to delete a task label.
   */
  public function deleteLabel($label_id) {
    if ($response = $this->podio->delete('/task/label/'.$label_id)) {
      return TRUE;
    }
  }
  
  /**
   * Returns the users task labels.
   */
  public function getLabels() {
    if ($response = $this->podio->get('/task/label/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the label of the task to the given value
   */
  public function updateLabel($label_id, $attributes = array()) {
    if ($response = $this->podio->put('/task/label/'.$label_id, $attributes)) {
      return TRUE;
    }
  }
  
  /**
   * Rank tasks.
   */
  public function rank($task_id, $attributes = array()) {
    $this->podio->post('/task/'.$task_id.'/rank', $attributes);
  }
}
