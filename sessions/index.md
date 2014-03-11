---
layout: default
active: sessions
---
# Sessions management
An important part of any Podio API integration is managing your authentication tokens. You can avoid hitting rate limits and make your integration faster by storing authentication tokens and thus avoid having to re-authenticate every time your script runs.

## What is a session manager
When you setup the API client you can optionally pass in the name of a session manager. This should be the class name of your session manager class. This class handles storing and retrieving access tokens through a unified interface. For example if your class is called `PodioSession` you would setup your client as:

{% highlight php startinline %}
Podio::setup($client_id, $client_secret, array(
  "session_manager" => "PodioSession"
));

if (Podio::is_authenticated()) {
  // The session manager found an existing authentication.
  // No need to re-authenticate
}
else {
  // No authentication found in session manager.
  // You must re-authenticate here.
}
{% endhighlight %}

If you use a session manager your authentication tokens will automatically be stored at the end of your script run and automatically retrieved when you create your client at the beginning of the following script run.

## Writing your own session manager
Writing a session manager is straight-forward. You need to create a new class that has two methods: `get` and `set`.

### The `get` method
The `get` method should retrieve an existing authentication when called. It has one parameter, `$auth_type`, which contains information about the current authentication method when using app or password authentication. This makes it easier to switch between multiple forms of authentication (see below).

It should return a `PodioOAuth` object in all cases. Return an empty `PodioOAuth` object if no existing authentication could be found.

### The `set` method
The `set` method should store a `PodioOAuth` object when called. It has two parameters, `$oauth`, which holds the current `PodioOAuth` objecy and `$auth_type`, which contains information about the current authentication method when using app or password authentication. This makes it easier to switch between multiple forms of authentication (see below).

## Example: Store access tokens in browser session cookie
This is a simple example meant for a web application that uses the server-side authentication flow. It stores the authentication data in a session cookie.

{% highlight php startinline %}
class PodioBrowserSession {

  /**
   * For sessions to work they must be started. We make sure to start
   * sessions whenever a new object is created.
   */
  public function __construct() {
    if(!session_id()) {
      session_start();
    }
  }

  /**
   * Get oauth object from session, if present. We ignore $auth_type since
   * it doesn't work with server-side authentication.
   */
  public function get($auth_type = null) {

    // Check if we have a stored session
    if (!empty($_SESSION['podio-php-session'])) {

      // We have a session, create new PodioOauth object and return it
      return new PodioOAuth(
        $_SESSION['podio-php-session']['access_token'],
        $_SESSION['podio-php-session']['refresh_token'],
        $_SESSION['podio-php-session']['expires_in'],
        $_SESSION['podio-php-session']['ref']
      );
    }

    // Else return an empty object
    return new PodioOAuth();
  }

  /**
   * Store the oauth object in the session. We ignore $auth_type since
   * it doesn't work with server-side authentication.
   */
  public function set($oauth, $auth_type = null) {

    // Save all properties of the oauth object in a session
    $_SESSION['podio-php-session'] = array(
      'access_token' => $oauth->access_token,
      'refresh_token' => $oauth->refresh_token,
      'expires_in' => $oauth->expires_in,
      'ref' => $oauth->ref,
    );

  }
}
{% endhighlight %}

Save the above class and include it in your project. You can now use this session manager:

{% highlight php startinline %}
Podio::setup($client_id, $client_secret, array(
  "session_manager" => "PodioBrowserSession"
));
{% endhighlight %}

## Example: Store access tokens in Redis
This is a simple example of how you could store authentication data in Redis. It uses `$auth_type` to juggle between multiple app authentications.

{% highlight php startinline %}
class PodioRedisSession {

  /**
   * Create a pointer to Redis when constructing a new object
   */
  public function __construct() {
    $this->redis = new Predis\Client();
  }

  /**
   * Get oauth object from session, if present. We use $auth_type as
   * basis for the cache key.
   */
  public function get($auth_type = null) {

    // If no $auth_type is set, just return empty
    // since we won't be able to find anything.
    if (!$auth_type) {
      return new PodioOauth();
    }

    $cache_key = "podio_cache_".$auth_type['type']."_".$auth_type['identifier'];

    // Check if we have a stored session
    if ($this->redis->exists($cache_key)) {

      // We have a session, create new PodioOauth object and return it
      $cached_value = $this->redis->hgetall($cache_key);
      return new PodioOAuth(
        $cached_value['access_token'],
        $cached_value['refresh_token'],
        $cached_value['expires_in'],
        array("type"=>$cached_value['ref_type'], "id"=>$cached_value['ref_id'])
      );
    }

    // Else return an empty object
    return new PodioOAuth();
  }

  /**
   * Store the oauth object in the session. We ignore $auth_type since
   * it doesn't work with server-side authentication.
   */
  public function set($oauth, $auth_type = null) {
    $cache_key = "podio_cache_".$auth_type['type']."_".$auth_type['identifier'];

    // Save all properties of the oauth object in redis
    $this->redis->hmset = array(
      'access_token' => $oauth->access_token,
      'refresh_token' => $oauth->refresh_token,
      'expires_in' => $oauth->expires_in,
      'ref_type' => $oauth->ref["type"],
      'ref_id' => $oauth->ref["id"],
    );

  }
}
{% endhighlight %}

Save the above class and include it in your project. You can now use this session manager:

{% highlight php startinline %}
Podio::setup($client_id, $client_secret, array(
  "session_manager" => "PodioRedisSession"
));

// podio-php will attempt to find a session automatically, but will fail because
// it doesn't know which $auth_type to use.
// So we must attempt to locate a session manually.
Podio::$auth_type = array(
  "type" => "app",
  "identifier" => $app_id
);
Podio::$oauth = self::$session_manager->get(Podio::$auth_type);

// Now we can check if anything could be found in the cache and
// authenticate if it couldn't
if (!Podio::is_authenticated()) {
  // No authentication found in session manager.
  // You must re-authenticate here.

  Podio::authenticate_with_app($app_id, $app_token);
}

// We can safely switch to another app now
// First attempt to get authentication from cache
// If that fails re-authenticate
Podio::$auth_type = array(
  "type" => "app",
  "identifier" => $another_app_id
);
Podio::$oauth = self::$session_manager->get(Podio::$auth_type);

if (!Podio::is_authenticated()) {
  // No authentication found in session manager.
  // You must re-authenticate here.

  Podio::authenticate_with_app($another_app_id, $another_app_token);
}
{% endhighlight %}

