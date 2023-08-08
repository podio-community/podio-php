---
layout: default
active: index
---
# About podio-php
Podio-php is a PHP client for interacting with the Podio API. All parts of the Podio API are covered through a collection of classes. This gives you a solid abstraction for working with the Podio API without having to worry about the nuts and bolts.

## Requirements
You need PHP 7.3+ with curl and openssl extensions enabled. There are no external dependencies.

There are many moving parts under the hood and you should be familiar with the basics of HTTP and Object-Oriented Programming (OOP) in PHP before diving in. If you are new to OOP there are some resources linked in [this answer on StackOverflow](http://stackoverflow.com/questions/5646356/php-oop-getting-started) to get you started. If you are not very familiar with PHP or the basics of HTTP a podio-php project will most likely be a poor beginners project. It would be best to seek out more basic PHP projects first.

## Installation
Installation is done using [Composer](http://getcomposer.org/) with [the package on Packagist](https://packagist.org/packages/podio-community/podio-php):

~~~ bash
composer require podio-community/podio-php
~~~

## Hello world
To get started right away, use app authentication to work on a single Podio app. To find your app id and token to go your app, click the wrench in the top right corner of the sidebar and click the <b>Developer</b> option.

{% highlight php startinline %}
require __DIR__ . '/vendor/autoload.php';

$client = new PodioClient($client_id, $client_secret);
$client->authenticate_with_app($app_id, $app_token);
$items = PodioItem::filter($client, $app_id);

print "My app has " . $items->total . " items";
{% endhighlight %}
