<?php
/*

This example shows you how to authenticate using the server flow.
You are stringly encouraged to read about the different authentication methods
at https://developers.podio.com/authentication to determine which is best for
your use.

To run this example:
* Follow the guidelines in the README to setup your configuration
* Specify your REDIRECT_URI (the URL of this file) below
* Run this file in your browser.

 */

// Set up the REDIRECT_URI -- which is just the URL for this file.
define("REDIRECT_URI", 'http://localhost:8888/~andreas/podio/podio-php/examples/server-auth.php');

?><html>
<head>
  <title>Server authentication example</title>
</head>
<body>
<?php

  // Include the config file and the Podio library
  require_once 'config.php';
  require_once '../PodioAPI.php';

  // Setup the API client reference. Client ID and Client Secrets are defined
  // as constants in config.php
  $api = Podio::instance(CLIENT_ID, CLIENT_SECRET);

  // If $_GET['code'] is not set it means we are not trying to authenticate.
  // In that case just display a link to start the serv flow
  if (!isset($_GET['code'])) {
    $auth_url = htmlentities('https://podio.com/oauth/authorize?response_type=code&client_id='.CLIENT_ID.'&redirect_uri='.rawurlencode(REDIRECT_URI));
    print "<a href='{$auth_url}'>Start authenticating</a>";
  }
  else {
    // Otherwise try to authenticate using the code provided.

    // We wrap the authentication attempt in a try...catch block to catch any problems
    try {

      // $_GET['error'] is set if there was a problem
      if (!isset($_GET['error'])) {
        $api->authenticate('authorization_code', array('code' => $_GET['code'], 'redirect_uri' => REDIRECT_URI));
        print "You have been authenticated. Wee!<br>";
        print "Your access token is {$api->oauth->access_token}<br><br>";
        print "Hang onto this access token along with the refresh token (store them in a session or similar) so you don't have to re-authenticate for every request.<br><br>";

        // Now you can start making API calls. E.g. get your user status
        $status = $api->user->getStatus();

        print "Your user id is <b>{$status['user']['user_id']}</b> and you have <b>{$status['inbox_new']}</b> unread messages in your inbox.<br><br>";

      }
      else {
        print "There was a problem. The server said: {$_GET['error_description']}<br>";
      }
    }
    catch (PodioError $e) {
      print "There was an error. The API responded with the error type <b>{$e->body['error']}</b> and the message <b>{$e->body['error_description']}</b><br>";
    }
  }
?>
</body>
</html>
