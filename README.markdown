# About
This is a PHP Client for interacting with the Podio API. Almost all parts of the Podio API is covered in this client.

# Getting the client and dependencies
The first step is to download a copy of the PHP client from GitHub: [https://github.com/podio/podio-php](https://github.com/podio/podio-php). It requires PHP5 with the [curl](http://php.net/manual/en/book.curl.php) and [openssl](http://php.net/manual/en/book.openssl.php) extensions. Place it inside your new PHP project.

# Including the client in your application
All you need to get started is to include PodioAPI.php like so:

    require_once '/path/to/podio-php/PodioAPI.php';

# Constructing API client instance and authentication
Before you can make any API calls you need to create an instance of the `Podio` class and authenticate with the Podio API. The exact process depends on which type of authentication you are using:

    require_once('/path/to/podio-php/PodioAPI.php');
    $api = Podio::instance($client_id, $client_secret);

    // Obtain access token using authorization code from the first step of the authentication flow
    $api->authenticate('authorization_code', array('code' => $_GET['code'], 'redirect_uri' => $redirect_uri));

    // Alternatively you can supply a username and password directly. E.g.:
    // $api->authenticate('password', array('username' => $username, 'password' => $password));

    print $api->oauth->access_token; // Your access token

    // Woohoo! Now it's time to make API calls!

You can view full authentication examples in the `examples´ folder. Consult the README in that folder for instructions on how to get the examples running.

# Making API calls
To make API calls you use the `Podio` class. This is contains references to all areas of the Podio API. See each area to see individual methods and their arguments.

For example: If I want to post a new status message _'Posted from the PHP Client'_ to a space I would call the `create` method in the `status` area like so:

    $api = Podio::instance();

    // $space_id is the id for the space I want to post the status message on
    $response = $api->status->create($space_id, array('value' => 'Posted from the PHP Client'));

    if ($response) {
      print 'The id for the new status message is: '.$response['status_id'];
    }

The Podio API always returns data in JSON and the PHP client automatically decodes this and places responses into associative arrays for easy traversal.

# Handling file uploads
If you wish to upload a file, for example to status messages, comments, items, widgets etc., you will use the `upload` method in the `file` area:

    $response = $api->file->upload($path_to_file, $filename_to_display);

    if ($response) {
      print 'File uploaded. The file id is: '.$response['result']['file_id'];
    }

# Error handling
All unsuccessful responses returned by the API (everything that has a 4xx or 5xx HTTP status code) will throw an exception. All exceptions inherit from `PodioError` and have three additional properties which give you more information about the error:

    try {
      // This is missing the mandatory $attributes parameter
      $api->status->create(45605);
    }
    catch (PodioBadRequestError $e) {
      print $e->body;   # Parsed JSON response from the API
      print $e->status; # Status code of the response
      print $e->url;    # URI of the API request

      // You normally want this one, a human readable error description
      print $e->body['error_description'];
    }


# Full example: Posting status message with an image
    require_once('/path/to/podio-php/PodioAPI.php');

    $client_id = 'MY_OAUTH_CLIENT_ID';
    $client_secret = 'MY_OAUTH_CLIENT_SECRET';

    $api = Podio::instance($client_id, $client_secret);

    // Obtain access token
    $username = 'MY_USERNAME';
    $password = 'MY_PASSWORD';
    $api->authenticate('password', array('username' => $username, 'password' => $password));

    // Upload file
    $file = $api->file->upload('/path/to/myimage.png', 'myimage.png');

    // Post status message
    $space_id = MY_SPACE_ID;
    $file_ids = array((int)$file['result']['file_id']);
    $api->status->create($space_id, array('value' => 'This has an image attached', 'file_ids' => $file_ids));

