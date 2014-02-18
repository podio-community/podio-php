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

## Getting the most out of Podio* objects
API calls returns a Podio object that matches the class you called a static method on or an array of those objects. E.g. `PodioSearchResult::app()` will return an array of PodioSearchResult objects. `PodioItem::get()` will return a single PodioItem object.

Once you have a Podio* object you might be tempted to simply `var_dump` it to see which properties are available. This would be a mistake. Due to the dynamic nature of the data structures returned from the Podio API Podio* objects use magic getters and setters. With a `var_dump` you will just see the internal data structure which is cumbersome to work with.

To see which properties you can access on an object type open the class file and look at the constructor. The class files are all located in the [models folder](https://github.com/podio/podio-php/tree/master/models). As an example look at [PodioTask](https://github.com/podio/podio-php/blob/master/models/PodioTask.php). You can see three methods being called to setup the properties:

* `$this->property()` declares a normal property.
* `$this->has_one()` declares a one-to-one relationship to another Podio* object. E.g. PodioTask has a property `created_by` which contains a single `PodioByLine` object.
* `$this->has_many()` declares a one-to-many relationship to another Podio* object. E.g. PodioTask has a property `comments` which contains an array of `PodioComment` objects.

Alternatively, create an object and call `properties` and `relationships` methods:

{% highlight php startinline %}
$task = new PodioTask();

// See basic properties
print_r($task->properties());

// See relationships to other types of objects
print_r($task->relationships());

{% endhighlight %}

You can access all attributes directly on your objects even though they are not declared as normal class properties would be. E.g. when working with a task:

{% highlight php startinline %}
// Get a task
$task = PodioTask::get(123);

// $task now holds a PodioTask object and we can work on the values:

// Print the task text:
print $task->text;

// Print the name of the creator. created_by holds a PodioByLine object:
print $task->created_by->name;

// Iterate over the comments:
foreach ($task->comments as $comment) {

  // $comment is a PodioComment object:
  print $comment->value;

}

// ...and so one. See each individual class file for available properties.

{% endhighlight %}

In a similar fashion you change values simply by assigning them. Changes are not automatically saved to the API. You must manually save them yourself.

{% highlight php startinline %}

// Get a task
$task = PodioTask::get(123);

// Change the task text:
$task->text = 'This is my new task text';

// Manually save the task:
PodioTask::update($task->id, $task);

{% endhighlight %}

## Helpful functions
Podio* objects have a few functions that can make your life easier.

{% highlight php startinline %}
$item = PodioItem::get(123);

// Check rights with `can`
if ($item->can('update')) {
  print "Yay, the current user can update this item";
}

// Get a JSON representation with `as_json`
print $item->as_json();

{% endhighlight %}

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

