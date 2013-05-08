<?php
/**
 * @see https://developers.podio.com/doc/forms
 */
class PodioForm extends PodioObject {
  public function __construct($attributes = array()) {
    $this->property('form_id', 'integer');
    $this->property('app_id', 'integer');
    $this->property('space_id', 'integer');
    $this->property('status', 'string');
    $this->property('settings', 'array');
    $this->property('domains', 'array');
    $this->property('fields', 'array');
    $this->has_many('attachments', 'File');

    $this->init($attributes);
  }

  /**
   * @see https://developers.podio.com/doc/forms/activate-form-1107439
   */
  public static function activate($form_id) {
    $body = Podio::post("/form/{$form_id}/activate")->json_body();
    return $body['form_id'];
  }

  /**
   * @see https://developers.podio.com/doc/forms/deactivate-form-1107378
   */
  public static function deacivate($form_id) {
    $body = Podio::post("/form/{$form_id}/deactivate")->json_body();
    return $body['form_id'];
  }

  /**
   * @see https://developers.podio.com/doc/forms/create-form-53803
   */
  public static function create($app_id, $attributes = array()) {
    $body = Podio::post("/form/app/{$app_id}/", $attributes)->json_body();
    return $body['form_id'];
  }

  /**
   * @see https://developers.podio.com/doc/forms/delete-from-53810
   */
  public static function delete($form_ids) {
    return Podio::delete("/form/{$form_ids}");
  }

  /**
   * @see https://developers.podio.com/doc/forms/get-form-53754
   */
  public static function get($form_id) {
    $result = Podio::get("/form/{$form_ids}");
    return self::member($result);
  }

  /**
   * @see https://developers.podio.com/doc/forms/get-forms-53771
   */
  public static function get_all_for_app($app_id) {
    return self::listing(Podio::get("/form/app/{$app_id}/"));
  }

  /**
   * @see https://developers.podio.com/doc/forms/update-form-53808
   */
  public static function update($form_id, $attributes = array()) {
    return Podio::put("/form/{$form_id}", $attributes);
  }
}
