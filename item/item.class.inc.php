<?php

/**
 * Items are entries in an app. If you think of app as a table, items will be 
 * the rows in the table. Items consists of some basic information as well 
 * values for each of the fields in the app. For each field there can be 
 * multiple values (F.ex. there can be multiple links to another app) and 
 * multiple types of values (F.ex. a field of type date field consists of both 
 * a start date and an optional end date).
 */
class PodioItemAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Used to get the distinct values for all items in an app. Will return 
   * a list of the distinct item creators, as well as a list of the 
   * possible values for fields of type "state", "member", "app", 
   * "number" and "progress". The return values for fields depends on the 
   * type of field
   */
  public function getValues($app_id) {
    if ($response = $this->podio->request('/item/app/'.$app_id.'/values')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the recent activity on the app divided into activity today and 
   * activity the last week. The activity events are ordered descending by 
   * the time the events occurred.
   *
   * @param $app_id The id of the app to get activity for
   *
   * @return Array with activities, grouped by "today" and "last_week"
   */
  public function getActivity($app_id) {
    if ($response = $this->podio->request('/item/app/'.$app_id.'/activity')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Used to find possible items for a given application field. It searches 
   * the relevant items for the title given.
   * 
   * @param $field_id Id of the app field that is being searched
   * @param $text The text to search for. The search will be lower case, 
   *              and with a wildcard in each end.
   *
   * @return Array of items
   */
  public function searchField($field_id, $text) {
    if ($response = $this->podio->request('/item/field/'.$field_id.'/find', array('text' => $text))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the item with the specified id.
   *
   * @param $item_id The id of the item to retrieve
   *
   * @return An item
   */
  public function get($item_id) {
    if (!$item_id) {
      return FALSE;
    }
    if ($response = $this->podio->request('/item/'.$item_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the previous item relative to the given item. This takes into 
   * consideration the last used filter on the app.
   *
   * @param $item_id The id of the current item
   *
   * @return Array with item id and title
   */
  public function getPrevious($item_id) {
    if ($response = $this->podio->request('/item/'.$item_id.'/previous')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the next item after the given item id. This takes into 
   * consideration the last used filter on the app.
   *
   * @param $item_id The id of the current item
   *
   * @return Array with item id and title
   */
  public function getNext($item_id) {
    if ($response = $this->podio->request('/item/'.$item_id.'/next')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the items on app matching the given filters.
   *
   * @param $app_id The id of the app to get items from
   * @param $limit The maximum number of items to receive
   * @param $offset The offset from the start of the items returned
   * @param $sort_by How the items should be sorted. For the possible options, 
   *                 see the filter area.
   * @param $sort_desc Use 1 or leave out to sort descending, 
   *                   use 0 to sort ascending
   * @param $filters Array of key/value pairs to use for filtering. For the 
   *                 valid keys and values see the filter area. For list 
   *                 filtering, the values are given as a comma-separated 
   *                 list, for range filtering the values are given as "x-y".
   *
   * @return Array with the total count and filtered count for the results and 
   *         an array of items
   */
  public function getItems($app_id, $limit, $offset, $sort_by, $sort_desc, $filters = array()) {

    // Change filter structure for GET request.
    $data = array('limit' => $limit, 'offset' => $offset, 'sort_by' => $sort_by, 'sort_desc' => $sort_desc);
    foreach ($filters as $filter) {
      if (empty($filter['values'])) {
        $data[$filter['key']] = '';
      } else if (is_array($filter['values'])) {
        if (isset($filter['values']['from'])) {
          $data[$filter['key']] = $filter['values']['from'].'-'.$filter['values']['to'];
        }
        else {
          $data[$filter['key']] = implode(';', $filter['values']);
        }
      }
    }

    if ($response = $this->podio->request('/item/app/'.$app_id.'/v2/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the revisions that have been made to an item
   *
   * @param $item_id The item to get revisions for
   *
   * @return Array of revisions
   */
  public function getRevisions($item_id) {
    if (!$item_id) {
      return FALSE;
    }
    if ($response = $this->podio->request('/item/'.$item_id.'/revision')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the difference in fields values between the two revisions.
   *
   * @param $item_id Item id to compare
   * @param $from Revision id to compare from
   * @param $to Revision id to compare to
   *
   * @return Array of fields with old and new values
   */
  public function getRevisionDiff($item_id, $from, $to) {
    if ($response = $this->podio->request('/item/'.$item_id.'/revision/'.$from.'/'.$to)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Adds a new item to the given app.
   *
   * @param $app_id The id of the app to create item in
   * @param $fields Array of values for each field. Each item has two keys:
   * - "field_id" : The id of the field (field_id or external_id must
   *                be specified)
   * - "external_id" : The external id of the field (field_id or external_id 
   *                   must be specified)
   * - "values" : Array. The values for the field
   * @param $file_ids Array. Temporary files that have been uploaded and 
   *                  should be attached to this item
   * @param $tags Array of tags to put on the item
   * @param $external_id The external id of the item. This can be used to 
   *                     hold a reference to the item in an external system
   *
   * @return Array with the new item id
   */
  public function create($app_id, $fields, $file_ids = array(), $tags = array(), $external_id = NULL) {
    $data = array('fields' => $fields, 'file_ids' => $file_ids, 'tags' => $tags, 'external_id' => $external_id);
    if ($response = $this->podio->request('/item/app/'.$app_id.'/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Update an already existing item. Values will only be updated for fields 
   * included. To delete all values for a field supply an empty array as 
   * values for that field.
   *
   * @param $item_id The id of the item that's being updated
   * @param $fields Array of values for each field. Each item has two keys:
   * - "field_id" : The id of the field (field_id or external_id must
   *                be specified)
   * - "external_id" : The external id of the field (field_id or external_id 
   *                   must be specified)
   * - "values" : Array. The values for the field
   * @param $revision The revision of the item that is being updated. 
   *                  This is optional.
   */
  public function update($item_id, $fields, $revision = NULL) {
    $data = array('fields' => $fields);
    if ($revision) {
      $data['revision'] = $revision;
    }
    if ($response = $this->podio->request('/item/'.$item_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes an item and removes it from all views. 
   * The data can no longer be retrieved.
   *
   * @param $item_id Id of the item to delete
   */
  public function delete($item_id) {
    if ($response = $this->podio->request('/item/'.$item_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Update the item values for a specific field.
   *
   * @param $item_id The id of the item to update
   * @param $field_id The id of the field to update
   * @param $data Array of new values
   */
  public function updateFieldValue($item_id, $field_id, $data) {
    if ($response = $this->podio->request('/item/'.$item_id.'/value/'.$field_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns a CSV file with the following options:
   * 
   * - Header row
   * - UTF-8 encoding
   * - "," delimiter
   * - carriage return and line-feed line terminator
   * - Double quoting with quoting only used when needed
   * First two columns are "Created on" and "Created by". 
   * The remaining columns are the fields on the app.
   * 
   * @param $app_id The id of the app to get items from
   * @param $format Use "windows" to get the windows format, 
   *                something else for any other format.
   * @param $limit The maximum number of items to receive
   * @param $offset The offset from the start of the items returned
   * @param $sort_by How the items should be sorted. For the possible options, 
   *                 see the filter area.
   * @param $sort_desc Use 1 or leave out to sort descending, 
   *                   use 0 to sort ascending
   * @param $filters Array of key/value pairs to use for filtering. For the 
   *                 valid keys and values see the filter area. For list 
   *                 filtering, the values are given as a comma-separated 
   *                 list, for range filtering the values are given as "x-y".
   *
   * @return Array with three keys:
   * - "body": The CSV content
   * - "filename": Filename to use in any downloads
   * - "content_type"
   */
  function csv($app_id, $format, $limit, $offset, $sort_by, $sort_desc, $filters = array()) {
    // Change filter structure for GET request.
    $data = array('format' => $format, 'offset' => $offset, 'sort_by' => $sort_by, 'sort_desc' => $sort_desc);
    if ($limit) {
      $data['limit'] = $limit;
    }
    foreach ($filters as $filter) {
      if (empty($filter['values'])) {
        $data[$filter['key']] = '';
      } else if (is_array($filter['values'])) {
        if (isset($filter['values']['from'])) {
          $data[$filter['key']] = $filter['values']['from'].'-'.$filter['values']['to'];
        }
        else {
          $data[$filter['key']] = implode(';', $filter['values']);
        }
      }
    }

    if ($response = $this->podio->request('/item/app/'.$app_id.'/csv/', $data)) {
      $header = $response->getHeader('content-disposition');
      preg_match('/filename="(.+)"/', $header, $matches);
      $filename = $matches[1];
      return array('body' => $response->getBody(), 'filename' => $filename, 'content_type' => $response->getHeader('content-type'));
    }
    
  }
  
}

