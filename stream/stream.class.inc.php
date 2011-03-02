<?php

/**
 * The stream API will supply the different streams. Currently supported is 
 * the global stream, the organization stream and the space stream. 
 */
class PodioStreamAPI {
  /**
   * Reference to the PodioBaseAPI instance
   */
  protected $podio;
  public function __construct() {
    $this->podio = PodioBaseAPI::instance();
  }
  
  /**
   * Returns the global stream. The types of objects in the stream can 
   * be either "item", "status" or "task". See API documentation 
   * for details.
   *
   * @param $limit The number of stream objects to get. Defaults to 20
   * @param $offset How far should the objects be offset, defaults to 0
   * @param $latest The date and time that all events should be before, 
   *                defaults to no limit
   *
   * @return An array of stream objects
   */
  public function getGlobal($limit = 20, $offset = 0, $latest = NULL) {
    $data = array('limit' => $limit, 'offset' => $offset);
    if ($latest) {
      $data['latest'] = $latest;
    }
    if ($response = $this->podio->request('/stream/v2/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns an organisation stream. The types of objects in the stream can 
   * be either "item", "status" or "task". See API documentation 
   * for details.
   *
   * @param $org_id The id of the organisation to get a stream for
   * @param $limit The number of stream objects to get. Defaults to 20
   * @param $offset How far should the objects be offset, defaults to 0
   * @param $latest The date and time that all events should be before, 
   *                defaults to no limit
   *
   * @return An array of stream objects
   */
  public function getOrg($org_id, $limit = 20, $offset = 0, $latest = NULL) {
    $data = array('limit' => $limit, 'offset' => $offset);
    if ($latest) {
      $data['latest'] = $latest;
    }
    if ($response = $this->podio->request('/stream/org/'.$org_id.'/v2/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns a space stream. The types of objects in the stream can 
   * be either "item", "status" or "task". See API documentation 
   * for details.
   *
   * @param $space_id The id of the space to get a stream for
   * @param $limit The number of stream objects to get. Defaults to 20
   * @param $offset How far should the objects be offset, defaults to 0
   * @param $latest The date and time that all events should be before, 
   *                defaults to no limit
   *
   * @return An array of stream objects
   */
  public function getSpace($space_id, $limit = 20, $offset = 0, $latest = NULL) {
    $data = array('limit' => $limit, 'offset' => $offset);
    if ($latest) {
      $data['latest'] = $latest;
    }
    if ($response = $this->podio->request('/stream/space/'.$space_id.'/v2/', $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Get objects for a stream. This includes items and statuses with comments, 
   * ratings, files and edits.
   *
   * @param $type The type of stream to get. May be:
   * - global: Stream for all spaces and organizations
   * - org: Stream for a single organization
   * - space: Stream for a single space
   * 
   * Defaults to 'global'
   * @param $ref_id The space id or org id when getting stream for org or space
   * @param $limit The number of stream objects to get. Defaults to 20
   * @param $offset How far should the objects be offset, defaults to 0
   * @param $latest The date and time that all events should be before, 
   *                defaults to no limit
   *
   * @return An array of stream objects
   */
  public function get($type = 'global', $ref_id = NULL, $limit = 20, $offset = 0, $latest = NULL) {
    $url = '/stream/';
    if ($type != 'global') {
      $url = '/stream/'.$type.'/'.$ref_id.'/';
    }

    $data = array('limit' => $limit, 'offset' => $offset);
    if ($latest) {
      $data['latest'] = $latest;
    }

    if ($response = $this->podio->request($url, $data)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns an object (item or status) as a stream object. This is useful 
   * when a new status has been posted and should be rendered directly in the 
   * stream without reloading the entire stream.
   *
   * @param $ref_type The type of object. "status" or "item"
   * @param $ref_id Either the status id or the item id
   *
   * @return A single stream object
   */
  public function getObject($ref_type = 'status', $ref_id = NULL) {
    $url = '/stream/'.$ref_type.'/'.$ref_id ;

    if ($response = $this->podio->request($url)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Returns an object (item or status) as a stream object. This is useful 
   * when a new status has been posted and should be rendered directly in the 
   * stream without reloading the entire stream.
   *
   * @param $ref_type The type of object. "status" or "item"
   * @param $ref_id Either the status id or the item id
   *
   * @return A single stream object
   */
  public function getObjectV2($ref_type = 'status', $ref_id = NULL) {
    $url = '/stream/'.$ref_type.'/'.$ref_id.'/v2';

    if ($response = $this->podio->request($url)) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Returns the apps and spaces the user has muted in the global stream.
   */
  public function getMutes() {
    if ($response = $this->podio->request('/stream/mute/')) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  
  /**
   * Mutes the app from the users global stream
   *
   * @param $app_id The id of the app to mute
   */
  public function muteApp($app_id) {
    if ($response = $this->podio->request('/stream/mute/app/'.$app_id, array(), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Unmutes the app from the users global stream
   *
   * @param $app_id The id of the app to unmute
   */
  public function unmuteApp($app_id) {
    if ($response = $this->podio->request('/stream/mute/app/'.$app_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
  
  /**
   * Mutes the space from the users global stream.
   *
   * @param $space_id The id of the space to mute
   */
  public function muteSpace($space_id) {
    if ($response = $this->podio->request('/stream/mute/space/'.$space_id, array(), HTTP_Request2::METHOD_POST)) {
      return json_decode($response->getBody(), TRUE);
    }
  }

  /**
   * Unmutes the space from the users global stream.
   *
   * @param $space_id The id of the space to unmute
   */
  public function unmuteSpace($space_id) {
    if ($response = $this->podio->request('/stream/mute/space/'.$space_id, array(), HTTP_Request2::METHOD_DELETE)) {
      if ($response->getStatus() == '204') {
        return TRUE;
      }
      return FALSE;
    }
  }
}

