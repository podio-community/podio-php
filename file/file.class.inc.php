<?php

class FileAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
	public function getFilesOnSpace($space_id, $limit, $offset = 0) {
	  if ($response = $this->podio->request('/file/space/'.$space_id.'/', array('limit' => $limit, 'offset' => $offset))) {
      return json_decode($response->getBody(), TRUE);
    }
	}

	public function getRecentOnSpace($space_id, $limit) {
	  if ($response = $this->podio->request('/file/space/'.$space_id.'/latest/', array('limit' => $limit))) {
      return json_decode($response->getBody(), TRUE);
    }
	}
	
  public function getLocation($file_id) {
    if ($response = $this->podio->request('/file/'.$file_id.'/location')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  public function delete($id) {
    if ($response = $this->podio->request('/file/'.$id, array(), HTTP_Request2::METHOD_DELETE)) {
      return true;
    }
    return false;
  }

	public function attach($file_id, $ref_type, $ref_id) {	
    $data['ref_type'] = $ref_type;
    $data['ref_id'] = $ref_id;
    $response = $this->podio->request('/file/'.$file_id.'/attach', $data, HTTP_Request2::METHOD_POST);
    if( $response ) {
      return true;
    }
    return false;
	}
	
	public function create($name, $mimetype) {
		$data['name'] = $name;
		$data['mimetype'] = $mimetype; 
		$response = $this->podio->request('/file/', $data, HTTP_Request2::METHOD_POST);
		if( $response ) {
			$result = json_decode($response->getBody(), TRUE);
			return $result;
		}
		return false;
	}
	
	public function announceAvailable($file_id) {
		$response = $this->podio->request('/file/'.$file_id.'/available', array(), HTTP_Request2::METHOD_POST);
    if( $response ) {
      return true;
    }
    return false;
	}
	
	public function replace($old_id, $new_id) {
		$data['old_file_id'] = (int)$old_id;
		$new_id = (int)$new_id;
		$response = $this->podio->request('/file/'.$new_id.'/replace', $data, HTTP_Request2::METHOD_POST);
		if( $response ) {
			return true;
		}
		return false;
	}
	

}

