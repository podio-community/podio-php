<?php

/**
 * Widgets are small components that can be installed on an organization, 
 * space or an app. Every widget has a title and is of a certain type.
 */
class PodioWidget {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Create a new widget on the given reference. Supported references are 
   * organizations, spaces and apps.
   */
  public function create($ref_type, $ref_id, $attributes = array()) {
    if ($response = $this->podio->post('/widget/'.$ref_type.'/'.$ref_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates a widget with a new title and configuration.
   */
  public function update($widget_id, $attributes = array()) {
    if ($response = $this->podio->put('/widget/'.$widget_id, $attributes)) {
      return TRUE;
    }
  }
  
  /**
   * Returns the widget with the given id.
   */
  public function get($widget_id) {
    if ($response = $this->podio->get('/widget/'.$widget_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the widgets on the given reference for display. This includes the 
   * current data to be shown in the widgets.
   */
  public function getForDisplay($ref_type, $ref_id) {
    if ($response = $this->podio->get('/widget/'.$ref_type.'/'.$ref_id.'/display/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the given widget.
   */
  public function delete($widget_id) {
    if ($response = $this->podio->delete('/widget/'.$widget_id)) {
      return TRUE;
    }
  }
  
  /**
   * Updates the order of the widgets on a reference. The ids of the widgets 
   * should be put in the new requested order.
   */
  public function updateOrder($ref_type, $ref_id, $list) {
    if ($response = $this->podio->put('/widget/'.$ref_type.'/'.$ref_id.'/order', $list)) {
      return TRUE;
    }
  }
}
