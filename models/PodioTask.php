<?php
/**
 * @see https://developers.podio.com/doc/tasks
 */
class PodioTask extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
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

        $this->has_one('ref', 'Reference');
        $this->has_one('created_by', 'ByLine');
        $this->has_one('completed_by', 'ByLine');
        $this->has_one('created_via', 'Via');
        $this->has_one('deleted_via', 'Via');
        $this->has_one('completed_via', 'Via');
        $this->has_one('responsible', 'User', array('json_value' => 'user_id'));
        $this->has_one('reminder', 'Reminder');
        $this->has_one('recurrence', 'Recurrence');
        $this->has_many('labels', 'TaskLabel', array('json_value' => 'label_id', 'json_target' => 'label_ids'));
        $this->has_many('files', 'File', array('json_value' => 'file_id', 'json_target' => 'file_ids'));
        $this->has_many('comments', 'Comment');

        $this->init($attributes);
    }

    /**
     * Creates or updates a task
     */
    public static function save(PodioClient $podio_client, PodioTask $task)
    {
        if ($task->id) {
            return self::update($podio_client, $task->id, $task);
        } else {
            $new = self::create($podio_client, $task);
            $task->task_id = $new->task_id;
            return $task;
        }
    }

    /**
     * @see https://developers.podio.com/doc/tasks/create-task-22419
     */
    public static function create(PodioClient $podio_client, $attributes = array(), $options = array())
    {
        $url = $podio_client->url_with_options("/task/", $options);
        return self::member($podio_client, $podio_client->post($url, $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/tasks/create-task-with-reference-22420
     */
    public static function create_for(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array(), $options = array())
    {
        $url = $podio_client->url_with_options("/task/{$ref_type}/{$ref_id}/", $options);
        return self::member($podio_client, $podio_client->post($url, $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/tasks/get-task-22413
     */
    public static function get(PodioClient $podio_client, $task_id)
    {
        return self::member($podio_client, $podio_client->get("/task/{$task_id}"));
    }

    /**
     * @see https://developers.podio.com/doc/tasks/get-tasks-77949
     */
    public static function get_all(PodioClient $podio_client, $attributes = array())
    {
        return self::listing($podio_client, $podio_client->get("/task/", $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/tasks/delete-task-77179
     */
    public static function delete(PodioClient $podio_client, $task_id)
    {
        return $podio_client->delete("/task/{$task_id}");
    }

    /**
     * @see https://developers.podio.com/doc/tasks/remove-task-reference-6146114
     */
    public static function delete_ref(PodioClient $podio_client, $task_id)
    {
        return $podio_client->delete("/task/{$task_id}/ref");
    }

    /**
     * @see https://developers.podio.com/doc/tasks/update-task-10583674
     */
    public static function update(PodioClient $podio_client, $task_id, $attributes = array(), $options = array())
    {
        $url = $podio_client->url_with_options("/task/{$task_id}", $options);
        return self::member($podio_client, $podio_client->put($url, $attributes));
    }

    /**
     * @see https://developers.podio.com/doc/tasks/assign-task-22412
     */
    public static function assign(PodioClient $podio_client, $task_id, $attributes = array())
    {
        return $podio_client->post("/task/{$task_id}/assign", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/tasks/complete-task-22432
     */
    public static function complete(PodioClient $podio_client, $task_id)
    {
        return $podio_client->post("/task/{$task_id}/complete");
    }

    /**
     * @see https://developers.podio.com/doc/tasks/incomplete-task-22433
     */
    public static function incomplete(PodioClient $podio_client, $task_id)
    {
        return $podio_client->post("/task/{$task_id}/incomplete");
    }

    /**
     * @see https://developers.podio.com/doc/tasks/rank-task-81015
     */
    public static function rank(PodioClient $podio_client, $task_id, $attributes = array())
    {
        return $podio_client->post("/task/{$task_id}/rank", $attributes);
    }

    /**
     * @see https://developers.podio.com/doc/calendar/get-task-calendar-as-ical-10195650
     */
    public static function ical(PodioClient $podio_client, $task_id)
    {
        return $podio_client->get("/calendar/task/{$task_id}/ics/")->body;
    }

    /**
     * @see https://developers.podio.com/doc/tasks/get-task-summary-1612017
     */
    public static function get_summary(PodioClient $podio_client, $attributes = array())
    {
        $result = $podio_client->get("/task/summary", $attributes)->json_body();
        $result['overdue']['tasks'] = self::listing($podio_client, $result['overdue']['tasks']);
        $result['today']['tasks'] = self::listing($podio_client, $result['today']['tasks']);
        $result['other']['tasks'] = self::listing($podio_client, $result['other']['tasks']);
        return $result;
    }

    /**
     * @see https://developers.podio.com/doc/tasks/get-task-summary-for-personal-1657217
     */
    public static function get_summary_personal(PodioClient $podio_client, $attributes = array())
    {
        $result = $podio_client->get("/task/personal/summary", $attributes)->json_body();
        $result['overdue']['tasks'] = self::listing($podio_client, $result['overdue']['tasks']);
        $result['today']['tasks'] = self::listing($podio_client, $result['today']['tasks']);
        $result['other']['tasks'] = self::listing($podio_client, $result['other']['tasks']);
        return $result;
    }

    /**
     * @see https://developers.podio.com/doc/tasks/get-task-summary-for-organization-1612063
     */
    public static function get_summary_for_org(PodioClient $podio_client, $org_id, $attributes = array())
    {
        $result = $podio_client->get("/task/org/{$org_id}/summary", $attributes)->json_body();
        $result['overdue']['tasks'] = self::listing($podio_client, $result['overdue']['tasks']);
        $result['today']['tasks'] = self::listing($podio_client, $result['today']['tasks']);
        $result['other']['tasks'] = self::listing($podio_client, $result['other']['tasks']);
        return $result;
    }

    /**
     * @see https://developers.podio.com/doc/tasks/get-task-summary-for-space-1612130
     */
    public static function get_summary_for_space(PodioClient $podio_client, $space_id, $attributes = array())
    {
        $result = $podio_client->get("/task/space/{$space_id}/summary", $attributes)->json_body();
        $result['overdue']['tasks'] = self::listing($podio_client, $result['overdue']['tasks']);
        $result['today']['tasks'] = self::listing($podio_client, $result['today']['tasks']);
        $result['other']['tasks'] = self::listing($podio_client, $result['other']['tasks']);
        return $result;
    }

    /**
     * @see https://developers.podio.com/doc/tasks/get-task-summary-for-reference-1657980
     */
    public static function get_summary_for(PodioClient $podio_client, $ref_type, $ref_id, $attributes = array())
    {
        $result = $podio_client->get("/task/{$ref_type}/{$ref_id}/summary", $attributes)->json_body();
        $result['overdue']['tasks'] = self::listing($podio_client, $result['overdue']['tasks']);
        $result['today']['tasks'] = self::listing($podio_client, $result['today']['tasks']);
        $result['other']['tasks'] = self::listing($podio_client, $result['other']['tasks']);
        return $result;
    }

    /**
     * @see https://developers.podio.com/doc/tasks/update-task-reference-170733
     */
    public static function update_reference(PodioClient $podio_client, $task_id, $attributes = array())
    {
        return $podio_client->put("/task/{$task_id}/ref", $attributes)->body;
    }

    /**
     * @see https://developers.podio.com/doc/tasks/get-task-count-38316458
     */
    public static function count(PodioClient $podio_client, $ref_type, $ref_id)
    {
        return $podio_client->get("/task/{$ref_type}/{$ref_id}/count")->json_body();
    }

    /**
     * @see https://developers.podio.com/doc/tasks/update-task-private-22434
     */
    public static function update_private(PodioClient $podio_client, PodioTask $task, $private_flag, $options = array())
    {
        $url = $podio_client->url_with_options("/task/{$task->id}/private", $options);
        return $podio_client->put($url, array('private' => $private_flag))->body;
    }
}
