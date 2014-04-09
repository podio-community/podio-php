---
layout: default
active: examples
---

# Examples
Below are some simple examples on how to use podio-php.

## Share item on public website
{% highlight php startinline %}
require 'podio-php/PodioAPI.php';
require 'Predis/Autoloader.php';

// Client id and secret etc.
$client_id     = '';
$client_secret = '';

$app_id = 123;
$app_token = '';

$item_id = 456;

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
