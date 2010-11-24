<?php

class NotificationAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }

	public function get($id) {
		$response = $this->podio->request('/notification/'.$id);
	  if ($response) {
      // $notification = $this->normalizeJSONNotification(json_decode($response->getBody(), TRUE)); 
	  	$notification = json_decode($response->getBody(), TRUE);
      return $notification;
    }
	}
	
	public function getNew($amount = 20, $offset = 0) {
		$data['limit'] = $amount;
		$data['offset'] = $offset;
  	$response = $this->podio->request('/notification/inbox/new', $data);
  	if ($response) {
  		return json_decode($response->getBody(), TRUE);
    }
  }
  
  public function getViewed($amount = 20, $offset = 0, $sent = 0, $filter_values = NULL) {
  	$data['limit'] = $amount;
    $data['offset'] = $offset;
    $data['sent'] = $sent;
    if( $sent == 1 ) {
    	$data['types'] = 'message';
    	$data['date_type'] = 'created';
    }
    
    if( isset($filter_values) ) {
    	if( isset($filter_values['contacts']) ) {
    		$data['users'] = implode(',', $filter_values['contacts']);
    	}
    	if( isset($filter_values['month']) && isset($filter_values['year']) ) {
    		$start_date = mktime(0, 0, 0, $filter_values['month'], 1, $filter_values['year']);
    		$end_date = mktime(0, 0, 0, $filter_values['month']+1, 1, $filter_values['year']);
    		$data['date_from'] = date('Y-m-d h:i:s', $start_date);
    		$data['date_to'] = date('Y-m-d h:i:s', $end_date);
    	}
    }
    
    $response = $this->podio->request('/notification/inbox/viewed', $data);
    if ($response) {
  		return json_decode($response->getBody(), TRUE);
    }
  }  
  
  public function getFiltersAndCounts() {
    $response = $this->podio->request('/notification/inbox/viewed/filters');
    if ($response) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  public function markViewed($notification_id, $viewed) {
    if( $viewed == 1) {
    	$method = HTTP_Request2::METHOD_POST;
    }	
    else {
      $method = HTTP_Request2::METHOD_DELETE;
    }  
  	$response = $this->podio->request('/notification/'.$notification_id.'/viewed', array(), $method);
  }
  
  public function getSettings() {
    $response = $this->podio->request('/notification/settings', array());
    if ($response) {
    	return json_decode($response->getBody(), TRUE);
    }
  }
  
  public function setSetting($send_notifications) {
  	$data['direct'] = $send_notifications;
  	$response = $this->podio->request('/notification/settings', $data, HTTP_Request2::METHOD_PUT);
  }
  
  public function setDigestSetting($send_digest) {
    $data['digest'] = $send_digest;
    $response = $this->podio->request('/notification/settings', $data, HTTP_Request2::METHOD_PUT);
  }
}

