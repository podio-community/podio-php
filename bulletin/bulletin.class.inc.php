<?php
class BulletinAPI {
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Returns all the bulletins
   *
   * @return An array of bulletin objects
   */
  public function getAll() {
    if ($response = $this->podio->request('/bulletin/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns the bulletin with the given id
   *
   * @param $bulletin_id The id of the bulletin to retrieve
   *
   * @return A single bulletin object
   */
  public function get($bulletin_id) {
    if ($response = $this->podio->request('/bulletin/'.$bulletin_id)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Creates a new bulletin with the given title, summary and text. When the 
   * bulletin is created, all users will be notified of the new bulletin.
   *
   * @param $title The title of the new bulletin
   * @param $summary The summary of the new bulletin
   * @param $text The main body of the new bulletin
   */
  public function create($title, $summary, $text) {
    $data = array('title' => $title, 'summary' => $summary, 'text' => $text);
    if ($response = $this->podio->request('/bulletin/', $data, HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Updates a bulletin to correct mistakes.
   *
   * @param $bulletin_id The id of the bulletin to retrieve
   * @param $title The title of the new bulletin
   * @param $summary The summary of the new bulletin
   * @param $text The main body of the new bulletin
   */
  public function update($bulletin_id, $title, $summary, $text) {
    $data = array('title' => $title, 'summary' => $summary, 'text' => $text);
    if ($response = $this->podio->request('/bulletin/'.$bulletin_id, $data, HTTP_Request2::METHOD_PUT)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
}