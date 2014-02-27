---
layout: default
active: api
---
# Making API requests
Each of the classes in the `models/` folder has a collection of static functions. You use these whenever you want to make API calls. **Important note:** When you are working with Apps, Items, AppField or ItemField classes you can take advantage of powerful instance methods. This will make your life easier. Read more under [Working with Podio items](/items).

## Reading the API reference
You can find API reference documentation at [https://developers.podio.com/doc](https://developers.podio.com/doc) it lists all available API operations possible. Almost all of them have been added to podio-php. If you need one added feel free to open a pull request on the [GitHub Project](https://github.com/podio/podio-php).

When you view an individual API operation (e.g. [Search in space](https://developers.podio.com/doc/search/search-in-space-22479)) you can see sample PHP code at the top. You can use this as a starting point when making API requests. For Search in space it's:

{% highlight php startinline %}
PodioSearchResult::space( $space_id, $attributes = array() );
{% endhighlight %}

The format for all API calls is the same. First the name of the class we're working with (`PodioSearchResult`), then the name of the static function/API operation we are invoking (`space`). The function arguments always have variables for the URL first (in this case just the `$space_id` of the space we're searching) followed by the object to use as the JSON request body (always called `$attributes`).

`$attributes` can either be an associative array or a `Podio*` object. If you already have a relevant `Podio*` object available you can pass it into the function as `$attributes`.

To continue the above example we can search the space with the space_id of `123` for the text `Podio is awesome` like so:

{% highlight php startinline %}
$search_results = PodioSearchResult::app( 123, array('query' => 'Podio is awesome') );

// $search_results now contains an array of `PodioSearchResult` objects that you can work with (see below).

{% endhighlight %}

You can see the expected structure for `$attributes` in the **Request** section for each API operation. You can preview your own arrays using `json_encode($attributes)` to see if it matches. If you have trouble see the section [_Debugging API calls_]({{site.baseurl}}/debug).

Most API requests return either a PodioObject or a PodioCollection. [They are flexible to work with]({{site.baseurl}}/objects).

## File uploads
When you want to attach a file to an object (e.g. a comment, status message etc.) you first need to upload the file to Podio and obtain a file object. To upload a file use the static `upload` method on `PodioFile`:

{% highlight php startinline %}
$file = PodioFile::upload($path_to_file, $filename_to_display);

print 'File uploaded. The file id is: '.$file->id;
{% endhighlight %}

## File downloads
To download a file you must first procure a `PodioFile` object. If you already have a `PodioItem`, `PodioComment` etc. object you probably already have the file object. Otherwise you have to get it manually. After that use the `get_raw` method to download the file.

{% highlight php startinline %}
// Get the file object. Only necessary if you don't already have it!
$file = PodioFile::get($file_id);

// Download the file. This might take a while...
$file_content = $file->get_raw();

// Store the file on local disk
file_put_contents($path_to_file, $file_content);
{% endhighlight %}

