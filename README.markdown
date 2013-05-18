# About
This is a PHP Client for interacting with the Podio API. All parts of the Podio API are covered in this client.

# Installation
The client library requires PHP 5.3 with [curl](http://php.net/manual/en/book.curl.php) and [openssl](http://php.net/manual/en/book.openssl.php) extensions enabled.

If you are using [Composer](http://getcomposer.org/) you can simply require podio-php:

    "require": {
      "podio/podio-php": "dev-master"
    }

There is an autoloader so you don't have to do anything other than make sure to use Composer's autolaoder.

If you are not using Composer you must download a copy of the PHP client from GitHub: [https://github.com/podio/podio-php](https://github.com/podio/podio-php) and then include it manually:

    require_once '/path/to/podio-php/PodioAPI.php';

# Constructing API client instance and authentication
Before you can make any API calls you must setup the client with your client ID and secret and authenticate with the Podio API. The exact process depends on which type of authentication you are using:

    require_once('/path/to/podio-php/PodioAPI.php');
    // Setup client
    Podio::setup($client_id, $client_secret);

    // Obtain access token using authorization code from the first step of the authentication flow
    Podio::authenticate('authorization_code', array('code' => $_GET['code'], 'redirect_uri' => $redirect_uri));

    // Alternatively you can supply a username and password directly. E.g.:
    // Podio::authenticate('password', array('username' => $username, 'password' => $password));

    print Podio::$oauth->access_token; // Your access token

    // Woohoo! Now it's time to make API calls!

You can view full authentication examples in the `examples´ folder.

# Making API calls
To make API calls you use static methods on the different classes in the `models` folder. Each class corresponds to an area of the Podio API. Open each file in your text editor to see the available methods and their arguments.

For example: If I want to post a new status message _'Posted from the PHP Client'_ to a space I would call the `create` method on the `PodioStatus` class like so:

    // $space_id is the id for the space I want to post the status message on
    $status = PodioStatus::create($space_id, array('value' => 'Posted from the PHP Client'));

    print 'The id for the new status message is: '.$status->id;

The PHP client automatically converts incoming data to instances of the relevant class. E.g. when you get an app item from the API it is automatically converted into an instance of the `PodioItem` class. File objects are instances of `PodioFile` and so on.

# Handling file uploads
If you wish to upload a file, for example to status messages, comments, items, widgets etc., you will use the `upload` method on the `PodioFile` class:

    $file = PodioFile::upload($path_to_file, $filename_to_display);

    print 'File uploaded. The file id is: '.$file->id;

# Debugging & error handling
It can be useful to log all HTTP requests made to the Podio API. To do so you can turn on debugging mode:

    Podio::$debug = true;

All API calls made with debug mode turned on will be logged to `log/podio.log` Monitor that file to follow along with what's happening behind the scenes.

All unsuccessful responses returned by the API (everything that has a 4xx or 5xx HTTP status code) will throw an exception. All exceptions inherit from `PodioError` and have three additional properties which give you more information about the error:

    try {
      // This is missing the mandatory $attributes parameter
      PodioStatus::create(45605);
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

    Podio::setup($client_id, $client_secret);

    // Obtain access token
    $username = 'MY_USERNAME';
    $password = 'MY_PASSWORD';
    Podio::authenticate('password', array('username' => $username, 'password' => $password));

    // Upload file
    $file = PodioFile::upload('/path/to/myimage.png', 'myimage.png');

    // Post status message
    $space_id = MY_SPACE_ID;
    $file_ids = array((int)$file->id);
    $status = PodioStatus::create($space_id, array('value' => 'This has an image attached', 'file_ids' => $file_ids));

# A note on versions
This is the third revision of the Podio PHP Client and it is very different than previous versions. If you are familiar with older versions almost everything have changed. If you need the older version it is available as a download at [https://github.com/podio/podio-php/zipball/v2](https://github.com/podio/podio-php/zipball/v2). It will not be updated and bugs will not be fixed.

