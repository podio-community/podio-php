---
layout: default
active: debug
---
# Debugging and error handling

<span class="note">Using debug mode can also be used to diagnose performance problems where you are making more requests than you intended.</span>

Podio-php will throw exceptions when something goes predictably wrong. For example if you try to update something you don't have permissions to update, if you don't include required attributes, if you hit the rate limit etc. All exceptions extends `PodioError` and you can see a list in [`PodioError.php`](https://github.com/podio-community/podio-php/blob/master/lib/error)

All these exceptions will end up in your PHP error log so if you just see a blank screen look there for them (or configure PHP to print errors to the screen).

The exceptions all contain information about the request that caused the problem, including the request URL and body. Inspect this carefully to make sure you are sending the data you meant to be sending.

If you get unexpected results but you are not seeing exceptions you can switch podio-php into debug mode. Add this before making any API requests:

{% highlight php startinline %}
Podio::set_debug(true);
{% endhighlight %}

This will output information about all API requests you make to the screen. This can sometimes be overwhelming so you can choose to output to a file instead:

{% highlight php startinline %}
Podio::set_debug(true, 'file');
{% endhighlight %}

This allows you to see exactly the data that's being sent between your script and the Podio API. If you are not writing a command line script [Kint](http://raveren.github.io/kint/) will be used to make everything display nicely.

If you only want to debug part of a script you can turn off debug mode with:

{% highlight php startinline %}
Podio::set_debug(false);
{% endhighlight %}
