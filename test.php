<?php
// Include the config file and the Podio library
require_once 'examples/config.php';
require_once 'PodioAPI.php';

// Setup the API client reference. Client ID and Client Secrets are defined
// as constants in config.php
Podio::setup(CLIENT_ID, CLIENT_SECRET);
Podio::$debug = true;

// Authenticate using your username and password. Both are defined as constants
// in config.php

// We wrap the authentication attempt in a try...catch block to catch any problems
try {
  Podio::authenticate('password', array('username' => USERNAME, 'password' => PASSWORD));
  print "You have been authenticated. Wee!\n";

  $item = PodioItem::get(20109397);
  print get_class($item->field('belob'));
  print "\n";


  // $app = PodioApp::get(2395065);
  // print_r($app->fields_of_type('progress'));

  // $item = PodioItem::get(20109397);
  // $item->field('titel')->set_value('My new title')->save();

  print "\n\n";

}
catch (PodioError $e) {
  print "There was an error. The API responded with the error type <b>{$e->body['error']}</b> and the message <b>{$e->body['error_description']}</b><br>";
}
