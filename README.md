# Podio PHP client library

This is the non-official PHP Client for interacting with the Podio API maintained by the Podio community and the continuation of the former official Podio PHP Client. Most parts of the Podio API are covered in this client. See [podio-community.github.io/podio-php](https://podio-community.github.io/podio-php/) for documentation.

[![Build Status](https://github.com/podio-community/podio-php/actions/workflows/test.yml/badge.svg?branch=master)](https://github.com/podio-community/podio-php/actions)
[![Coverage Status](https://coveralls.io/repos/github/podio-community/podio-php/badge.svg?branch=master)](https://coveralls.io/github/podio-community/podio-php?branch=master)
[![Packagist Version](https://img.shields.io/packagist/v/podio-community/podio-php)](https://packagist.org/packages/podio-community/podio-php)

## Usage
Install via [composer](https://getcomposer.org):
```bash
composer require podio-community/podio-php
```

Use in your PHP files:
```php
require __DIR__ . '/vendor/autoload.php';

$client = new PodioClient($client_id, $client_secret);
$client->authenticate_with_app($app_id, $app_token);
$items = PodioItem::filter($client, $app_id);

print "My app has " . $items->total . " items";
```

## Contribute

To contribute, please read: [the contribution guide](https://github.com/podio-community/podio-php/blob/master/CONTRIBUTING.md).
