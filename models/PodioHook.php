<?php

class PodioHook extends PodioObject {
  public function __construct($attributes = array()) {
    $this->property('hook_id', 'integer');
    $this->property('status', 'string');
    $this->property('type', 'string');
    $this->property('url', 'string');

    $this->has_one('created_by', 'PodioByLine');

    $this->init($attributes);
  }

  public static function get($ref_type, $ref_id) {
    if ($response = self::podio()->get('/hook/'.$ref_type.'/'.$ref_id.'/')) {
      return self::listing(json_decode($response->getBody(), TRUE));
    }
  }

}
