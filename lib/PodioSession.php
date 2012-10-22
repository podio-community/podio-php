<?php

/**
 * Very simple session manager to manage access and refresh token
 */
class PodioSession {

  public function __construct() {
    session_start();
  }

  /**
   * Get oauth object from session, if present
   */
  public function get() {
    if (!empty($_SESSION['podio-php-session'])) {
      return new PodioOAuth($_SESSION['podio-php-session']['access_token'], $_SESSION['podio-php-session']['refresh_token'], $_SESSION['podio-php-session']['expires_in'], $_SESSION['podio-php-session']['ref']);
    }
    return null;
  }

  /**
   * Store the oauth object in the session
   */
  public function set($oauth) {
    $_SESSION['podio-php-session'] = array(
      'access_token' => $oauth->access_token,
      'refresh_token' => $oauth->refresh_token,
      'expires_in' => $oauth->expires_in,
      'ref' => $oauth->ref,
    );
  }

  /**
   * Destroy the session
   */
  public function destroy() {
    unset($_SESSION['podio-php-session']);
  }

}
