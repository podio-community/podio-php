<?php

/**
 * Handles logging of errors and debug information to file system
 */
class PodioLogger {

  protected $enabled = true;
  public $file, $maxsize;

  public function __construct() {
    $this->file = dirname(__FILE__).'/../log/podio.log';
    $this->maxsize = 1024*1024;
  }

  public function disable() {
    $this->enabled = false;
  }

  public function enable() {
    $this->enabled = true;
  }

  public function log_request($method, $url, $encoded_attributes, $response, $curl_info) {
    @mkdir(dirname($this->file));

    if ($fp = fopen($this->file, 'ab')) {
      $timestamp = gmdate('Y-m-d H:i:s');
      fwrite($fp, "{$timestamp} {$response->status} {$method} {$url}\n");
      if (!empty($encoded_attributes)) {
        fwrite($fp, "{$timestamp} Request body: ".$encoded_attributes."\n");
      }
      fwrite($fp, "{$timestamp} Reponse: {$response->body}\n\n");
      fclose($fp);

      // Trim log file by removing the first 50 lines
      if (filesize($this->file) > $this->maxsize) {
        $file = file($this->file);
        $file = array_splice($file, 0, 50);
        file_put_contents($this->file, join('', $file));
      }
    }
  }

}
