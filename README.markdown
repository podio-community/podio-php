# About
This is a PHP Client for interacting with the Podio API. Almost all parts of the Podio API is covered in this client. To get started include PodioAPI.php in your script:

    require_once('/path/to/podio-php/PodioAPI.php');

# Dependencies
The Podio PHP client depends on two PEAR Packages:

* HTTP\_Request2: [http://pear.php.net/package/HTTP_Request2/](http://pear.php.net/package/HTTP_Request2/)
* Log: [http://pear.php.net/package/Log/](http://pear.php.net/package/Log/)

Both must be present in your PHP include path. If you have PEAR installed you can install these packages using the "pear install" command.

# Constructing API client instance and authentication
You will working with three classes:

* **PodioOAuth:** Handles authentication with the API server and holds your OAuth tokens.
* **PodioAPI:** Handles all communication with the API server. This is where you will spend most of your time.
* **PodioBaseAPI:** A base class, you need to create an instance of this to hold your API credentials.

Before you can make any API calls you need to obtain an OAuth access token. You do this by creating instances of `PodioBaseAPI` and `PodioOAuth` and call the `getAccessToken` method:

    require_once('/path/to/podio-php/PodioAPI.php');
    $oauth = PodioOAuth::instance();
    $baseAPI = PodioBaseAPI::instance($server, $client_id, $client_secret, $upload_end_point);
    
    // Obtain access token
    $oauth->getAccessToken('password', array('username' => $username, 'password' => $password));
    
    print $oauth->access_token; // Your access token
    
    // Woohoo! Now it's time to make API calls!

# Making API calls
To make API calls you use the `PodioAPI` class. This is contains references to all areas of the Podio API. See each area to see individual methods and their arguments.

For example: If I want to post a new status message _'Posted from the PHP Client'_ to a space I would call the `create` method in the `status` area like so:

    $api = new PodioAPI();
    
    // $space_id is the id for the space I want to post the status message on
    $response = $api->status->create($space_id, 'Posted from the PHP Client');
    
    if ($response) {
      print 'The id for the status message is: '.$response['status_id'];
    }

The Podio API always returns data in JSON and the PHP client automatically decodes this and places responses into associative arrays for easy traversal.

# Handling file uploads
If you wish to upload a file, for example to status messages, comments, items, widgets etc., you will use the `upload` method in the `api` area:

    $response = $api->api->upload($path_to_file, $filename_to_display);
    
    if ($response) {
      print 'File uploaded. The file id is: '.$response['result']['file_id'];
    }

# Logging
By default all logging happens in the PHP error log. You can overwrite this behaviour with the `setLogHandler` method on the `PodioBaseAPI` class. For example, you can log to a specific file:

    $baseAPI->setLogHandler('file', '/path/to/log/file/podio_log.log');

You can see which log handlers are available in the [PEAR Log documentation](http://www.indelible.org/php/Log/guide.html).

# API reference
The PHP Client is documented using Doxygen. For your convenience a Doxygen configuration file has been included in the repository. To generate an API reference:

* Install Doxygen if you haven't: [http://doxygen.org/](http://doxygen.org/)
* Navigate to the podio-php folder and run `doxygen .doxygen`
* The API reference will now be available in the `docs` folder

# Full example: Posting status message with an image
    require_once('/path/to/podio-php/PodioAPI.php');
    
    $server = 'https://api.podio.com:443';
    $client_id = 'MY_OAUTH_CLIENT_ID';
    $client_secret = 'MY_OAUTH_CLIENT_SECRET';
    $upload_end_point = 'https://upload.podio.com/upload.php';

    $oauth = PodioOAuth::instance();
    $baseAPI = PodioBaseAPI::instance($server, $client_id, $client_secret, $upload_end_point);
    
    // Obtain access token
    $username = 'MY_USERNAME';
    $password = 'MY_PASSWORD';
    $oauth->getAccessToken('password', array('username' => $username, 'password' => $password));
    
    // Upload file
    $file = $api->api->upload('/path/to/myimage.png', 'myimage.png');
    
    // Post status message
    $space_id = MY_SPACE_ID;
    $api->status->create($space_id, 'This has an image attached', array((int)$file['result']['file_id']));

