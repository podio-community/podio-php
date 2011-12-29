<?php

/**
 * Area for importing files into an app, f.ex. Excel files.
 */
class PodioImporter {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }

  /**
   * Returns the columns available from the file. The file must be uploaded 
   * and the user must have access to it.
   */
  public function getColumns($file_id) {
    if ($response = $this->podio->get('/importer/'.$file_id.'/column/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Imports the file into the given app. The mapping value for a field 
   * depends on the type of field.
   */
  public function process($file_id, $attributes = array()) {
    if ($response = $this->podio->post('/importer/'.$file_id.'/process', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

