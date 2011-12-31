<?php

/**
 * Files have a name, a mime-type, a size and a location. When adding files, 
 * the file should first be uploaded through the file gateway, and the 
 * location of the file should then be passed to the API. Files can be 
 * replaced by newer revisions.
 */
class PodioFile {
  /**
   * Reference to the Podio instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = Podio::instance();
  }
  
  /**
   * Upload a file
   */
  public function upload($filepath, $filename) {
    if ($response = $this->podio->post('/file/v2/', array('source' => '@'.realpath($filepath), 'filename' => $filename), array('upload' => TRUE, 'filesize' => filesize($filepath)))) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns all the files on the space, order by the file name.
   */
  public function getFilesOnSpace($space_id, $attributes = array()) {
    if ($response = $this->podio->get('/file/space/'.$space_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns all the files on the app, order by the file name.
   */
  public function getFilesOnApp($app_id, $attributes = array()) {
    if ($response = $this->podio->get('/file/app/'.$app_id.'/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the latest files on the space order descending by the date the 
   * file was uploaded.
   */
  public function getRecentOnSpace($space_id, $attributes = array()) {
    if ($response = $this->podio->get('/file/space/'.$space_id.'/latest/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the latest files added to the app sorted descending by 
   * the time the file was uploaded.
   */
  public function getRecentOnApp($app_id, $attributes = array()) {
    if ($response = $this->podio->get('/file/app/'.$app_id.'/latest/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the name, mimetype and location of the file. 
   */
  public function get($file_id) {
    if ($response = $this->podio->get('/file/'.$file_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the raw file.
   */
  public function get_raw($file_id) {
    if ($response = $this->podio->get('/file/'.$file_id.'/raw')) {
      return $response->getBody();
    }
  }

  /**
   * Deletes the file with the given id.
   */
  public function delete($file_id) {
    if ($response = $this->podio->delete('/file/'.$file_id)) {
      return TRUE;
    }
  }

  /**
   * Attaches the uploaded file to the given object. 
   * Valid objects are "status", "item" and "comment".
   */
  public function attach($file_id, $attributes = array()) {	
    if ($response = $this->podio->post('/file/'.$file_id.'/attach', $attributes)) {
      return TRUE;
    }
  }

  /**
   * Copies the file, which makes it available for attaching to another object.
   */
  public function copy($file_id) {	
    if ($response = $this->podio->post('/file/'.$file_id.'/copy')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Used to update the description of the file.
   */
  public function update($file_id, $attributes = array()) {	
    if ($response = $this->podio->put('/file/'.$file_id, $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Upload a new temporary file. After upload the file can either be attached 
   * directly to a file using the attach operation, used to replace an 
   * existing file using the replace operation or used as file id when 
   * posting a new object.
   */
  public function create($attributes = array()) {
    if ($response = $this->podio->post('/file/', $attributes)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Marks the current file as an replacement for the old file. Only files 
   * with type of "attachment" can be replaced.
   */
  public function replace($new_file_id, $attributes = array()) {
    if ($response = $this->podio->post('/file/'.$new_file_id.'/replace', $attributes)) {
      return TRUE;
    }
  }
}
