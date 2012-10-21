<?php
/*

This example shows you how to authenticate using the username and password flow.
You are stringly encouraged to read about the different authentication methods
at https://developers.podio.com/authentication to determine which is best for
your use.

To run this example follow the guidelines in the README to setup your
configuration and then run this file in your browser.

 */
?>
<html>
<head>
  <title>Username and Password authentication example</title>
</head>
<body>
<?php
  // Include the config file and the Podio library
  require_once 'config.php';
  require_once '../PodioAPI.php';

  // Setup the API client reference. Client ID and Client Secrets are defined
  // as constants in config.php
  Podio::setup(CLIENT_ID, CLIENT_SECRET);

  // Authenticate using your username and password. Both are defined as constants
  // in config.php

  // We wrap the authentication attempt in a try...catch block to catch any problems
  try {
    Podio::authenticate('password', array('username' => USERNAME, 'password' => PASSWORD));
    print "You have been authenticated. Wee!<br>";
    $access_token = Podio::$oauth->access_token;
    print "Your access token is {$access_token}<br><br>";
    print "Hang onto this access token along with the refresh token (store them in a session or similar) so you don't have to re-authenticate for every request.<br><br>";

    // Now you can start making API calls. E.g. get your user status
    $status = PodioUserStatus::get();
    print "Your user id is <b>{$status->user->id}</b> and you have <b>{$status->inbox_new}</b> unread messages in your inbox.<br><br>";

  }
  catch (PodioError $e) {
    print "There was an error. The API responded with the error type <b>{$e->body['error']}</b> and the message <b>{$e->body['error_description']}</b><br>";
  }
?>
</body>
</html>
