<?php

class PodioResponse {
  public $body;
  public $status;
  public function getBody() {
    return $this->body;
  }
  public function getStatus() {
    return $this->status;
  }
}
