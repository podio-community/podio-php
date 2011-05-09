<?php

/**
 * Area for importing files into an app, f.ex. Excel files.
 */
class PodioImporterAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

  /**
   * Returns the columns available from the file. The file must be uploaded 
   * and the user must have access to it.
   *
   * @param $file_id The id of the file to use for the import
   */
  public function getColumns($file_id) {
    if ($response = $this->podio->request('/importer/'.$file_id.'/column/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Imports the file into the given app. The mapping value for a field 
   * depends on the type of field.
   *
   * @param $app_id The id of the app the values should be imported into
   * @param $mappings The mappings between fields and columns
   * @param $tags_column_id The id of the column to read tags from, if any
   */
  public function process($file_id, $app_id, $mappings, $tags_column_id) {
    if ($response = $this->podio->request('/importer/'.$file_id.'/process', array('app_id' => $app_id, 'mappings' => $mappings, 'tags_column_id' => $tags_column_id), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}

