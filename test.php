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

  // $my_hooks = PodioHook::get('app', 233463);
  // $hook_id = PodioHook::create('app', 233463, array('url' => 'http://example.com/', 'type' => 'item.create'));
  // print "Created {$hook_id}\n";
  // PodioHook::delete(14303);

  $task = PodioTask::get(4001230);

  // var_dump($task->attributes['due_date']);
  // var_dump($task->due_date);
  // var_dump($task->attributes['due_time']);
  // var_dump($task->due_time);

  // print $task->as_json();

  $task->text = 'updated: '.gmmktime();

  $task->save();

}
catch (PodioError $e) {
  print "There was an error. The API responded with the error type <b>{$e->body['error']}</b> and the message <b>{$e->body['error_description']}</b><br>";
}
