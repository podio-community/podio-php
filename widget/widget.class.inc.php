<?php

/**
 * Widgets are small components that can be installed on an organization, 
 * space or an app. Every widget has a title and is of a certain type.
 */
class PodioWidgetAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Create a new widget on the given reference. Supported references are 
   * organizations, spaces and apps.
   *
   * @param $ref_type What to attach the widget to. "app" or "space"
   * @param $ref_id The app id or space id
   * @param $type Widget type:
   * - text
   * - image
   * - link
   * - tag_cloud
   * - state_count
   * - member_count
   * - contact_count
   * - app_count
   * - item_count
   * @param $title The title of the widget
   * @param $config Configuration options. Depends on widget type
   */
  public function create($ref_type, $ref_id, $type, $title, $config) {
    $data = array('type' => $type, 'title' => $title, 'config' => $config);
    if ($response = $this->podio->request('/widget/'.$ref_type.'/'.$ref_id.'/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates a widget with a new title and configuration.
   *
   * @param $widget_id The id of the widget to update
   * @param $title The title of the widget
   * @param $config Configuration options. Depends on widget type
   */
  public function update($widget_id, $title, $config) {
    $data = array('title' => $title, 'config' => $config);
    if ($response = $this->podio->request('/widget/'.$widget_id, $data, HTTP_Request2::METHOD_PUT)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Returns the widget with the given id.
   *
   * @param $widget_id The id of the widget to retrieve
   *
   * @return A widget object
   */
  public function get($widget_id) {
    if ($response = $this->podio->request('/widget/'.$widget_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the widgets on the given reference for display. This includes the 
   * current data to be shown in the widgets.
   *
   * @param $ref_type What context to get widgets for. "app" or "space"
   * @param $ref_id The app id or space id
   *
   * @return An array of widgets with data for display
   */
  public function getForDisplay($ref_type, $ref_id) {
    if ($response = $this->podio->request('/widget/'.$ref_type.'/'.$ref_id.'/display/')) {
      if ($result = json_decode($response->getBody(), TRUE)) {
        return $result;
      }
      else {
        $this->podio->log('Widget get for display failed or empty: '.$ref_type.' : '.$ref_id);
        $this->podio->log(print_r($response, true));
      }
    }
  }
  
  /**
   * Deletes the given widget.
   *
   * @param $widget_id The id of the widget to delete
   */
  public function delete($widget_id) {
    if ($response = $this->podio->request('/widget/'.$widget_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Updates the order of the widgets on a reference. The ids of the widgets 
   * should be put in the new requested order.
   *
   * @param $ref_type What context to update order for. "app" or "space"
   * @param $ref_id The app id or space id
   * @param $list An array of widget ids in the new order
   */
  public function updateOrder($ref_type, $ref_id, $list) {
    if ($response = $this->podio->request('/widget/'.$ref_type.'/'.$ref_id.'/order', $list, HTTP_Request2::METHOD_PUT)) {
      return TRUE;
    }
    return FALSE;
  }
}

