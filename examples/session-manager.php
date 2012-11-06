<?php

// This is a list of examples on the session manager.
// The script is not meant to be run (you'll get errors),
// it is simply an illustration of the syntax you must use.

// The built-in session manager gives you a quick and dirty
// way to persist the Podio API authentication between page
// loads. The goal to avoid having to re-authenticate each time
// a page is refreshed. It is enabled by default.

// If you open lib/PodioSession.php you can see how simple the
// session manager is. It just has get, set and destroy methods.

// You can use the Podio::is_authenticated() method to check if
// there is a stored access token already present:

Podio::setup(CLIENT_ID, CLIENT_SECRET);
if (Podio::is_authenticated()) {
  // There is already authentication present. We can make API
  // calls without authenticating again:
  $item = PodioItem::get( YOUR_ITEM_ID );
}
else {
  // No authentication present. We have to authentication
  // before making API calls:
  Podio::authenticate('app', array('app_id' => YOUR_APP_ID, 'app_token' => YOUR_APP_TOKEN));

  $item = PodioItem::get( YOUR_ITEM_ID );
}

// The downside of the built-in session manager is that it just
// stores the access tokens in the $_SESSION. This means that you
// will have to re-authenticate a user each time their close their browser

// Often you will want to persist the access tokens for a longer period.
// To do so you can implement your own session manager. All you need
// is to create a class that implements the same get, set and destroy methods.
// Then you can store the access tokens in your database or whereever.

// When doing the client setup all you need to pass in the name of your session manager
// class as an option and it will be used instead of the built-in one.
// For example if your session manager class is called 'MySessionManager'

Podio::setup(CLIENT_ID, CLIENT_SECRET, array('session_manager' => 'MySessionManager'));

// If the built-in session manager is causing you trouble you can disable it.
// This can be useful during development to make sure no stale access tokens are stored.
Podio::setup(CLIENT_ID, CLIENT_SECRET, array());
