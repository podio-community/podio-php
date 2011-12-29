<?php

/**
 * Items are entries in an app. If you think of app as a table, items will be 
 * the rows in the table. Items consists of some basic information as well 
 * values for each of the fields in the app. For each field there can be 
 * multiple values (F.ex. there can be multiple links to another app) and 
 * multiple types of values (F.ex. a field of type date field consists of both 
 * a start date and an optional end date).
 */
class PodioItem {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Used to get the distinct values for all items in an app. Will return 
   * a list of the distinct item creators, as well as a list of the 
   * possible values for fields of type "state", "member", "app", 
   * "number" and "progress". The return values for fields depends on the 
   * type of field
   */
  public function getValues($app_id) {
    if ($response = $this->podio->get('/item/app/'.$app_id.'/values')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the recent activity on the app divided into activity today and 
   * activity the last week. The activity events are ordered descending by 
   * the time the events occurred.
   */
  public function getActivity($app_id) {
    if ($response = $this->podio->get('/item/app/'.$app_id.'/activity')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Used to find possible items for a given application field. It searches 
   * the relevant items for the title given.
   */
  public function searchField($field_id, $attributes) {
    if ($response = $this->podio->get('/item/field/'.$field_id.'/find', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the top possible values for the given field. 
   * This is currently only valid for fields of type "app".
   */
  public function getFieldTop($field_id, $attributes) {
    if ($response = $this->podio->get('/item/field/'.$field_id.'/top/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the item with the specified id.
   */
  public function get($item_id, $attributes) {
    if ($response = $this->podio->get('/item/'.$item_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Gets the basic details about the given item. Similar to the full get 
   * item method, but only returns data for the item itself.
   */
  public function getBasic($item_id, $attributes) {
    if ($response = $this->podio->get('/item/'.$item_id.'/basic', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the previous item relative to the given item. This takes into 
   * consideration the last used filter on the app.
   */
  public function getPrevious($item_id, $attributes) {
    if ($response = $this->podio->get('/item/'.$item_id.'/previous', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the next item after the given item id. This takes into 
   * consideration the last used filter on the app.
   */
  public function getNext($item_id, $attributes) {
    if ($response = $this->podio->get('/item/'.$item_id.'/next', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the items on app matching the given filters.
   */
  public function getItems($app_id, $attributes) {
    if ($response = $this->podio->get('/item/app/'.$app_id.'/v2/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the revisions that have been made to an item
   */
  public function getRevisions($item_id) {
    if ($response = $this->podio->get('/item/'.$item_id.'/revision')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the difference in fields values between the two revisions.
   */
  public function getRevisionDiff($item_id, $from, $to) {
    if ($response = $this->podio->get('/item/'.$item_id.'/revision/'.$from.'/'.$to)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Adds a new item to the given app.
   */
  public function create($app_id, $attributes) {
    $url = '/item/app/'.$app_id.'/';
    if (isset($attributes['silent']) && $attributes['silent'] == 1) {
      $url .= '?silent=1';
      unset($attributes['silent']);
    }
    if ($response = $this->podio->post($url, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Update an already existing item. Values will only be updated for fields 
   * included. To delete all values for a field supply an empty array as 
   * values for that field.
   */
  public function update($item_id, $attributes) {
    $url = '/item/'.$item_id;
    if (isset($attributes['silent']) && $attributes['silent'] == 1) {
      $url .= '?silent=1';
      unset($attributes['silent']);
    }
    if ($response = $this->podio->put($url, $attributes)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      elseif ($response->getStatus() == '200') {
        return json_decode($response->getBody(), TRUE);
      }
    }
  }
  
  /**
   * Deletes an item and removes it from all views. 
   * The data can no longer be retrieved.
   */
  public function delete($item_id) {
    if ($response = $this->podio->delete('/item/'.$item_id)) {
      return TRUE;
    }
  }
  
  /**
   * Returns the values for a specified field on an item
   */
  public function getFieldValue($item_id, $field_id) {
    if ($response = $this->podio->get('/item/'.$item_id.'/value/'.$field_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Update the item values for a specific field.
   */
  public function updateFieldValue($item_id, $field_id, $attributes) {
    $url = '/item/'.$item_id.'/value/'.$field_id;
    if (isset($attributes['silent']) && $attributes['silent'] == 1) {
      $url .= '?silent=1';
      unset($attributes['silent']);
    }
    if ($response = $this->podio->put($url, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Performs a calculation on the given app. The calculation is made up 
   * of 4 parts; aggreation, formula, grouping and filtering. See the API 
   * documentation for detals.
   */
  public function calculate($app_id, $attributes) {
    if ($response = $this->podio->post('/item/app/'.$app_id.'/calculate', $attributes)) {
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
   */
  function csv($app_id, $attributes) {
    if ($response = $this->podio->get('/item/app/'.$app_id.'/csv/', $attributes)) {
      return $response->getBody();
    }
  }

  /**
   * Returns a XLSX file of items
   */
  function xlsx($app_id, $attributes) {
    if ($response = $this->podio->get('/item/app/'.$app_id.'/xlsx/', $attributes)) {
      return $response->getBody();
    }
  }
}
