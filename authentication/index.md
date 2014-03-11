---
layout: default
active: auth
---
# Making your first API call
Making API calls is a three step process:

1. Setup the API client
2. Authenticate
3. Make your API calls

## Setting up the API client
Before you can do anything you must setup the API client using your Podio API key. [Head over to Podio to generate a client_id and client_secret](https://podio.com/settings/api) before continuing.

Podio-php exposes a bunch of static methods on its classes. You'll be using more of these later, but for now you just need to use the main `Podio` class. It has a `setup` method you need to call before doing anything else. You only have to call it once before making any API calls and then you can forget about it. Call it with the client_id and client_secret from your [API key]((https://podio.com/settings/api).

{% highlight php startinline %}
Podio::setup($client_id, $client_secret);
{% endhighlight %}

Now you're ready to authenticate.

## Authentication
Podio supports multiple forms of authentication depending on what you want to do. Use the server-side flow for web apps where you need Podio users to access your app, app authentication when you just need access to a single app without user interaction and use password authentication for testing or if you have no other way out. [Read more about authentication in general at the Podio developer site](https://developers.podio.com/authentication).

### Server-side flow
The server-side flow requires you to redirect your users to a page on podio.com to authenticate. After they authenticate on podio.com they will be redirected back to your site. [Read about the flow on the developer site](https://developers.podio.com/authentication/server_side).

The example below handles three cases:

* The user has not authenticated and has not been redirected back to our page after authenticating.
* The user has already authenticated and they have a session stored using the [session manager]({{site.baseurl}}/sessions).
* The user is being redirected back to our page after authenticating.

{% highlight php startinline %}
// Set up the REDIRECT_URI -- which is just the URL for this file.
define("REDIRECT_URI", 'http://example.com/path/to/your/script.php');
Podio::setup($client_id, $client_secret);

if (!isset($_GET['code']) && Podio::is_authenticated()) {

  // User is not being reidrected and does not have an active session
  // We just display a link to the authentication page on podio.com
  $auth_url = htmlentities(Podio::authorize_url(REDIRECT_URI));
  print "<a href='{$auth_url}'>Start authenticating</a>";

} elseif (Podio::is_authenticated()) {

  // User already has an active session. You can make API calls here:
  print "You were already authenticated and no authentication is needed.";

}
elseif (isset($_GET['code'])) {

  // User is being redirected back from podio.com after authenticating.
  // The authorization code is available in $_GET['code']
  // We use it to finalize the authentication

  // If there was a problem $_GET['error'] is set:
  if (isset($_GET['error'])) {
    print "There was a problem. The server said: {$_GET['error_description']}";
  }
  else {
    // Finalize authentication. Note that we must pass the REDIRECT_URI again.
    Podio::authenticate_with_authorization_code($_GET['code'] REDIRECT_URI);
    print "You have been authenticated. Wee!";
  }

}
{% endhighlight %}

### App authentication
App authentication doesn't require any direct user authentication and is thus much simpler. You can simply pass the app id and app token directly to the authentication function:

{% highlight php startinline %}
Podio::setup($client_id, $client_secret);
Podio::authenticate_with_app($app_id, $app_token);
// You can now make API calls.
{% endhighlight %}

### Password authentication
Password authentication works the same way as app authentication, but you have full access to any data the user has access to. As it's bad practice to store your Podio password like this you should only use password-based authentication for testing or if you cannot use any of the other options.

{% highlight php startinline %}
Podio::setup($client_id, $client_secret);
Podio::authenticate_with_password($username, $password);
// You can now make API calls.
{% endhighlight %}

## Refreshing access tokens
Under the hood you receive two tokens upon authenticating. An access token is used to make API calls and a refresh token is used to get a new access/refresh token pair once the access token expires.

You should **avoid authenticating every time your script runs**. It's highly inefficient and you risk running into rate limits quickly. Instead [use a session manager to store access/refresh tokens between script runs]({{site.baseurl}}/sessions) to re-use your tokens.

Podio-php will automatically refresh tokens for you, but it's your responsibility to store the updated tokens after you're done making API calls. Otherwise you may be left with expired tokens. [Use a session manager to automate this process]({{site.baseurl}}/sessions).

## Managing multiple authentications
You can end up in a situation where you need to switch between multiple authentications. This usually happens if you are using app authentication and need to switch between multiple apps.

To switch from one authentication to another simply call another authentication function:

{% highlight php startinline %}
Podio::setup($client_id, $client_secret);
Podio::authenticate_with_app($first_app_id, $first_app_token);
// Here you can make API calls against the first app

Podio::authenticate_with_app($second_app_id, $second_app_token);
// Now you can make API calls against the second app
{% endhighlight %}

However, this simple approach will break the automatic refresh of access tokens. In the above example your API calls may have resulted in a refresh of your access/refresh tokens, but you no longer have a reference to those tokens. You will need to manually store your tokens before switching to a different authentication. This usually involves a [session manager]({{site.baseurl}}/sessions).
