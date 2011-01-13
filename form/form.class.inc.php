<?php

/**
 * Forms can be used to enable direct submission into apps from websites. 
 * Forms can be configured through the API, but can only be used through 
 * the JS script.
 */
class PodioFormAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Creates a new form on an app.
   *
   * @param $app_id The id of the app to base form on
   * @param $settings The settings for the form. An array with these keys:
   * - "captcha": True if captcha is enabled, false otherwise
   * - "text": The texts used for the form. Array with two keys: 
   *   - "submit": The text for the submit button
   *   - "success": The text when the form was successfully submitted
   * - "theme": The theme to use. Options:
   *   - clean
   *   - glossy
   *   - classic
   *   - dark
   * - Arial
   * - Georgia
   * - Lucida Sans Unicode
   * @param $domains Array of domains where the form can be used
   * @param $field_ids Array of ids of the fields that should be 
   *                   active for the form
   * @param $attachments True if attachments are allowed, false otherwise
   *
   * @return Array with the new form id
   */
  public function create($app_id, $settings, $domains, $field_ids, $attachments) {
    $data = array('settings' => $settings, 'domains' => $domains, 'field_ids' => $field_ids, 'attachments' => $attachments);
    if ($response = $this->podio->request('/form/app/'.$app_id.'/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates the form with new settings, domains, fields, etc.
   *
   * @param $form_id The id of the form to update
   * @param $settings The settings for the form. An array with these keys:
   * - "captcha": True if captcha is enabled, false otherwise
   * - "text": The texts used for the form. Array with two keys: 
   *   - "submit": The text for the submit button
   *   - "success": The text when the form was successfully submitted
   * - "theme": The theme to use. Options:
   *   - clean
   *   - glossy
   *   - classic
   *   - dark
   * @param $domains Array of domains where the form can be used
   * @param $field_ids Array of ids of the fields that should be 
   *                   active for the form
   * @param $attachments True if attachments are allowed, false otherwise
   */
  public function update($form_id, $settings, $domains, $field_ids, $attachments) {
    $data = array('settings' => $settings, 'domains' => $domains, 'field_ids' => $field_ids, 'attachments' => $attachments);
    if ($response = $this->podio->request('/form/'.$form_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the form with the given id.
   *
   * @param $form_id The id of the form to retrieve
   */
  public function get($form_id) {
    if ($response = $this->podio->request('/form/'.$form_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the active forms on the given app.
   *
   * @param $app_id The id of the app to get forms for
   *
   * @return Array of forms
   */
  public function getForms($app_id) {
    if ($response = $this->podio->request('/form/app/'.$app_id.'/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the form with the given id.
   *
   * @param $form_id The id of the form to delete
   */
  public function delete($form_id) {
    if ($response = $this->podio->request('/form/'.$form_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
    }
  }

}

