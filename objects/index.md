---
layout: default
active: objects
---
# Using PodioObject & PodioCollection

<span class="note">If you are manipulating items from Podio apps be sure to read [Podio items]({{site.baseurl}}/items) as well.</span>

Most API calls return a Podio object that matches the class you called a static method on or a PodioCollection of those objects. E.g. `PodioSearchResult::app()` will return a PodioCollection containing PodioSearchResult objects. `PodioItem::get()` will return a single PodioItem object.

Once you have a Podio* object you might be tempted to simply `var_dump` it to see which properties are available. This would be a mistake. Due to the dynamic nature of the data structures returned from the Podio API Podio* objects use magic getters and setters. With a `var_dump` you will just see the internal data structure which is cumbersome to work with.

To see which properties you can access on an object type open the class file and look at the constructor. The class files are all located in the [models folder](https://github.com/podio-community/podio-php/tree/master/models). As an example look at [PodioTask](https://github.com/podio-community/podio-php/blob/master/models/PodioTask.php). You can see three methods being called to setup the properties:

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
$task = PodioTask::get($client, 123);

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
$task = PodioTask::get($client, 123);

// Change the task text:
$task->text = 'This is my new task text';

// Manually save the task:
PodioTask::update($client, $task->id, $task);

{% endhighlight %}

Additionally, Podio* objects have a few functions that can make your life easier.

{% highlight php startinline %}
$item = PodioItem::get($client, 123);

// Check rights with `can`
if ($item->can('update')) {
  print "Yay, the current user can update this item";
}

// Get a JSON representation with `as_json`
print $item->as_json();
{% endhighlight %}

## PodioCollection
A PodioCollection is as the name implies a collection of Podio* objects. They behave in most ways the same way an array would. You can iterate over it and add/remove objects in the same way you would for arrays.

{% highlight php startinline %}
// Get a PodioCollection of tasks
$tasks = PodioTask::get_all($client);

// See how large the collection is
print "The collection has ".count($tasks)." tasks.";

// Iterate over the collection
foreach ($tasks as $task) {
  // You can now work on each individual object:
  print "This task has the id: ".$task->task_id;
}

// Add to collection
$tasks[] = new PodioTask(123);

// Get object by their id (in this case task_id=123)
$task = $tasks->get(123);

// Remove from collection by id (in this case task_id=456)
$tasks->remove(456);

{% endhighlight %}
