---
layout: default
active: webhooks
---
# Webhooks
Webhooks provide realtime notifications when changes occur on your apps and spaces. Before continuing you should read the [general introduction to Podio webhooks](https://developers.podio.com/examples/webhooks) and [review the list of webhook events](https://developers.podio.com/doc/hooks).

## Creating webhooks
The easiest way to create a new webhook is to do it manually in the developer section for the app. There are you can create most webhooks through a point and click interface. Not all webhooks can be created here. The rest you most create programmatically. E.g. if you only want to receive updates about a single field in an app:

{% highlight php startinline %}
$app_field_id = 123; // Only act on changes on the field with field_id=123
$event_type = "item.update"; // Only act when field values are updated

$hook = PodioHook::create("app_field", $app_field_id, array(
  "url" => "http://example.com/my/hook/url",
  "type" => $event_type
));
{% endhighlight %}

Immediately after you create a webhook you must verify it. Verifying just means the URL you provided must respond to a special type of webhook event called `hook.verify`. See the full example below for how to verify a webhook.

If you create your webhook ahead of the URL being available you must manually request a webhook verification:

{% highlight php startinline %}
PodioHook::verify( $hook_id );
{% endhighlight %}

## Checking webhooks status
If you are unsure of the status of your hooks you can get a list of all hooks for a reference:

{% highlight php startinline %}
$hooks = PodioHook::get_for( $ref_type, $ref_id );

foreach ($hooks as $hook) {
  print "Hook id: ".$hook->hook_id;
  print "Hook type: ".$hook->type;
  print "Hook URL: ".$hook->url;
}
{% endhighlight %}

## Troubleshooting webhooks
When webhooks fail to show up it's typically for one of the following reasons:
* Not a public URL. Webhooks must be available on the public internet. You can test them locally using tools like [localtunnel](http://progrium.com/localtunnel/) or [ProxyLocal](http://proxylocal.com/). For the same reason they cannot reside behind your corporate firewall.
* Not on a standard port. Webhooks must be served on port 80 for http and 443 for https.
* Incoming requests blocked by firewall/hosting provider. Your IT department or hosting provider may be blocking webhooks.
* Query string parameters will be converted to POST parameters. Because webhooks are POST requests any query string parameters will be converted to a POST parameter. If your URL is `http://example.com/hook?foo=bar` you will not be able to use `$_GET['foo']` - use `$_POST['foo']` instead.

## Full webhooks example
This is a standalone script that will verify all webhook verification requests and log all webhooks it receives to a text file. It uses app authentication. Upload this to a URL and register webhooks pointing to that URL to get started with webhooks. It only logs item creation, deletion and updates, but [there are more events you can add yourself](https://developers.podio.com/doc/hooks).

{% highlight php startinline %}
// File setup
$file = "./webhook.log";

// API key setup
$client_id = "YOUR_CLIENT_ID";
$client_secret = "YOUR_CLIENT_SECRET";

// Authentication setup
$app_id = "YOUR_APP_ID";
$app_token = "YOUR_APP_TOKEN";

// Setup client and authenticate
Podio::setup($client_id, $client_secret);
Podio::authenticate_with_app($app_id, $app_token);

// Big switch statement to handle the different events

switch ($_POST['type']) {

  // Validate the webhook. This is a special case where we verify newly created webhooks.
  case 'hook.verify':
    PodioHook::validate($_POST['hook_id'], array('code' => $_POST['code']));

  // An item was created
  case 'item.create':
    $string = gmdate('Y-m-d H:i:s') . " item.create webhook received. ";
    $string .= "Post params: ".print_r($_POST, true) . "\n";
    file_put_contents($file, $string, FILE_APPEND | LOCK_EX);

  // An item was updated
  case 'item.update':
    $string = gmdate('Y-m-d H:i:s') . " item.update webhook received. ";
    $string .= "Post params: ".print_r($_POST, true) . "\n";
    file_put_contents($file, $string, FILE_APPEND | LOCK_EX);

  // An item was deleted
  case 'item.delete':
    $string = gmdate('Y-m-d H:i:s') . " item.delete webhook received. ";
    $string .= "Post params: ".print_r($_POST, true) . "\n";
    file_put_contents($file, $string, FILE_APPEND | LOCK_EX);

}
{% endhighlight %}
