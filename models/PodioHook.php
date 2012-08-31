<?php
namespace Podio;

/**
 * @see https://developers.podio.com/doc/hooks
 */
class Hook extends Object {
  public function __construct($attributes = array()) {
    $this->property('hook_id', 'integer');
    $this->property('status', 'string');
    $this->property('type', 'string');
    $this->property('url', 'string');
    $this->property('created_on', 'datetime');

    $this->has_one('created_by', 'ByLine');
    $this->has_one('created_via', 'Via');

    $this->init($attributes);
  }

  /**
   * @see https://developers.podio.com/doc/hooks/get-hooks-215285
   */
  public static function get($ref_type, $ref_id) {
    if ($response = self::podio()->get('/hook/'.$ref_type.'/'.$ref_id.'/')) {
      return self::listing(json_decode($response->getBody(), TRUE));
    }
  }

}
