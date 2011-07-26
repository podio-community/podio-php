<?php

/**
 * Files have a name, a mime-type, a size and a location. When adding files, 
 * the file should first be uploaded through the file gateway, and the 
 * location of the file should then be passed to the API. Files can be 
 * replaced by newer revisions.
 */
class PodioFileAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Returns all the files on the space order by the file name.
   *
   * @param $space_id Space id of the space wanted
   * @param $limit The maximum number of files to be returned. 
   *               Defaults to 50 and cannot be higher than 100
   * @param $offset The offset to use when returning files to be used 
   *                for pagination. Defaults to 0 (no offset)
   *
   * @return Array of file objects
   */
  public function getFilesOnSpace($space_id, $limit, $offset = 0) {
    if ($response = $this->podio->request('/file/space/'.$space_id.'/', array('limit' => $limit, 'offset' => $offset))) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the latest files on the space order descending by the date the 
   * file was uploaded.
   *
   * @param $space_id Space id of the space wanted
   * @param $limit The maximum number of files to be returned. 
   *               Defaults to 10 and cannot be higher than 50
   *
   * @return Array of file objects
   */
  public function getRecentOnSpace($space_id, $limit) {
    if ($response = $this->podio->request('/file/space/'.$space_id.'/latest/', array('limit' => $limit))) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the name, mimetype and location of the file. 
   *
   * @param $file_id Id for the file
   */
  public function get($file_id) {
    if ($response = $this->podio->request('/file/'.$file_id, array(), HTTP_Request2::METHOD_GET)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the raw file.
   *
   * @param $file_id Id for the file
   */
  public function get_raw($file_id) {
    if ($response = $this->podio->request('/file/'.$file_id.'/raw', array(), HTTP_Request2::METHOD_GET)) {
      return $response->getBody();
    }
  }

  /**
   * Returns the name, mimetype and location of the file. 
   * This is only used for the download script.
   *
   * @param $file_id Id for the file
   */
  public function getLocation($file_id) {
    if ($response = $this->podio->request('/file/'.$file_id.'/location', array(), HTTP_Request2::METHOD_GET)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Deletes the file with the given id.
   *
   * @param $file_id Id of the file to be deleted
   */
  public function delete($file_id) {
    if ($response = $this->podio->request('/file/'.$file_id, array(), HTTP_Request2::METHOD_DELETE)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Attaches the uploaded file to the given object. 
   * Valid objects are "status", "item" and "comment".
   *
   * @param $file_id Id of the file to attach
   * @param $ref_type Type of reference to attach to. 
   *                  Can be "status", "item" or "comment"
   * @param $ref_id Status id, item id or comment id
   */
  public function attach($file_id, $ref_type, $ref_id) {	
    $data['ref_type'] = $ref_type;
    $data['ref_id'] = (int)$ref_id;
    $response = $this->podio->request('/file/'.$file_id.'/attach', $data, HTTP_Request2::METHOD_POST);
    if ($response) {
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Upload a new temporary file. After upload the file can either be attached 
   * directly to a file using the attach operation, used to replace an 
   * existing file using the replace operation or used as file id when 
   * posting a new object.
   *
   * @param $name The name of the file
   * @param $mimetype The type of the file, see the area for allowed types
   *
   * @return Array with the file id and file location
   */
  public function create($name, $mimetype) {
    $data['name'] = $name;
    $data['mimetype'] = $mimetype; 
    $response = $this->podio->request('/file/', $data, HTTP_Request2::METHOD_POST);
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
    return FALSE;
  }
  
  /**
   * Marks the file as available on the location given when the file was 
   * registered. This will cause the thumbnails to be generated 
   * and available.
   *
   * @param $file_id The id of the file to announce
   */
  public function announceAvailable($file_id) {
    $response = $this->podio->request('/file/'.$file_id.'/available', array(), HTTP_Request2::METHOD_POST);
    if ($response) {
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Marks the current file as an replacement for the old file. Only files 
   * with type of "attachment" can be replaced.
   *
   * @param $old_file_id The id of the old file that should be 
   *                replacd with the new file
   * @param $new_file_id The id of the current file
   */
  public function replace($old_file_id, $new_file_id) {
    $data['old_file_id'] = (int)$old_file_id;
    $new_file_id = (int)$new_file_id;
    $response = $this->podio->request('/file/'.$new_file_id.'/replace', $data, HTTP_Request2::METHOD_POST);
    if ($response) {
      return TRUE;
    }
    return FALSE;
  }

}

