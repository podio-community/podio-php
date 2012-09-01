<?php
// Include the config file and the Podio library
require_once 'examples/config.php';
require_once 'PodioAPI.php';

// Setup the API client reference. Client ID and Client Secrets are defined
// as constants in config.php
Podio::setup(CLIENT_ID, CLIENT_SECRET);

// Authenticate using your username and password. Both are defined as constants
// in config.php

// We wrap the authentication attempt in a try...catch block to catch any problems
try {
  Podio::authenticate('password', array('username' => USERNAME, 'password' => PASSWORD));
  print "You have been authenticated. Wee!\n";

  $list = Podio\Hook::get('app', 233463);

  var_dump($list[0]);

}
catch (PodioError $e) {
  print "There was an error. The API responded with the error type <b>{$e->body['error']}</b> and the message <b>{$e->body['error_description']}</b><br>";
}
