---
layout: default
active: examples
---

# Examples

## Share item on public website

Often it can be beneficial to publish the contents of a single Podio item on a public website. For example if you use Podio to maintain an FAQ document that you want the public to read.

This example will show you how to fetch an item from Podio and print the contents. We will rely on [Redis](http://redis.io/) as a caching mechanism because you will quickly run into [rate limit](https://developers.podio.com/index/limits) issues otherwise. The example code below uses our PHP client library.

### Prerequisites
You must download a copy of [podio-php](http://podio.github.io/podio-php/), install [Redis](http://redis.io/) and the PHP library [Predis](https://github.com/nrk/predis) (a PHP library for interacting with Redis).

In addition the example code below it's assumed that you have a Podio app with two text fields. One text field has the external_id `title` and the other `body`.

To get started you will need to [generate an API key](https://podio.com/settings/api) and insert the client_id and client_secret. You will also need to locate the app_id for your app in the developer section for your app and the item_id for a sample item in that app in the developer info for that item.

### Example code

{% highlight php startinline %}
require 'podio-php/PodioAPI.php';
require 'Predis/Autoloader.php';

// Insert your API key client_id and client_secret below
$client_id     = '';
$client_secret = '';

// Replace with your app_id and app_token
$app_id = '';
$app_token = '';

// Replace with your item_id
$item_id = '';

// Setup Redis
Predis\Autoloader::register();
$redis = new Predis\Client();
$my_cache_key = "podio_cache_item_data";

// Get data from Redis cache or from Podio API
if ($redis->exists($my_cache_key)) {

  // We have a cached copy, use that
  $item_data = $redis->hgetall($my_cache_key);

}
else {

  // No cache or cache expired, get data from Podio

  // Setup Podio Client
  Podio::setup($client_id, $client_secret);

  // Authenticate as an app
  Podio::authenticate('app', array(
    'app_id' => $app_id,
    'app_token' => $app_token
  ));

  // Get a single item
  $item = PodioItem::get_basic($item_id);

  // Find the value of the 'title' and 'body' fields.
  // These are the two fields we want to display.
  // If your fields have different external_ids
  // modify the code below
  $item_data = array(
    'title' => $item->fields['title']->values,
    'body' => $item->fields['body']->values,
  );

  // Store in cache
  $redis->hmset($my_cache_key, $item_data);
  $redis->expire($my_cache_key, 60*60*12); // Expire in 12 hours

}

// Print the smallest of HTML pages. You will want to use
// your preferred templating library to generate this page.
print "<!DOCTYPE html>
  <html>
    <head><title>podio-php sample</title></head>
    <body>
      <h1>".htmlspecialchars($item_data['title'])."</h1>
      <div>".strip_tags($item_data['body'], '<p><a><b>')."</div>
    </body>
  </html>\n";
{% endhighlight %}


## Expose app as RSS/Atom feed

## Collect data from custom webform
