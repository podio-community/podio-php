---
layout: default
active: items
---
# Working with Podio items

<span class="note">If you haven't read about [Podio* objects]({{site.baseurl}}/objects) yet, do so first.</span>

Apps and app items form the core of Podio and for that reason podio-php tries to make it as easy as possible to read and manipulate app items.

## Individual items
### Get an item
There are multiple ways to get a single item from the API. All result in a `PodioItem` object. Which one you should use depends on what data you have available and how much data you need returned.

If you have the `item_id` you can use either `PodioItem::get()` or `PodioItem::get_basic()`. The first will return all auxiliary data about the item but is slower, the latter returns no auxiliary data.

{% highlight php startinline %}
// Get only data about the item
$item = PodioItem::get_basic(123); // Get item with item_id=123
{% endhighlight %}

{% highlight php startinline %}
// Get item and auxiliary data such as comments
$item = PodioItem::get(123); // Get item with item_id=123
{% endhighlight %}

If you have assigned an external_id to an item you can get the item by providing that external_id. Since external_ids are not unique you need to provide the app_id as well.

{% highlight php startinline %}
// Get item by external_id

$app_id = 456;
$external_id = "my_sample_external_id";

$item = PodioItem::get_by_external_id($app_id, $external_id);
{% endhighlight %}

In a similar fashion each item in an app has a short numeric id called `app_item_id`. These are unique within the app, but not globally unique. It's the numeric id you can see in the URL when viewing an item on Podio. You can also get an item by providing an app_id and the app_item_id.

{% highlight php startinline %}
// Get item by app_item_id

$app_id = 456;
$app_item_id = 1;

$item = PodioItem::get_by_app_item_id($app_id, $app_item_id);
{% endhighlight %}

The final option is to [resolve an item URL](https://developers.podio.com/doc/reference/resolve-url-66839423) and create an item that way. This is useful in cases where you only have the browser's URL to work with (e.g. when creating browser extensions or when you are asking users to copy and paste URLs into your app).

{% highlight php startinline %}
// Get item by resolving its URL

$url = "http://podio.com/myorganization/myspace/apps/myapp/items/1";

// Resolve URL to a reference
$reference = PodioReference::resolve(array('url' => $url));

// Create item from reference
$item = new PodioItem($reference->data);
{% endhighlight %}

### Item fields
The most interesting part of an item is the `fields` attribute. Here the values for the item are found. If you're doing any work with items it's likely that you're modifying fields in one way or another.

Fields are a special kind of `PodioCollection` -- specifically a `PodioItemFieldCollection`. You can do all the thing you can do with a [PodioCollection]({{site.baseurl}}/objects) and then some.

#### Iterating over all fields
If you just want see all fields you can iterate over them.

{% highlight php startinline %}
// Get an item to work on
$item = PodioItem::get_base(123); // Get item with item_id=123

// Iterate over the field collection
foreach ($item->fields as $field) {
  // You can now work on each individual field object:
  print "This field has the id: ".$field->field_id;
  print "This field has the external_id: ".$field->external_id;
}
{% endhighlight %}

#### Get field
You can access individual fields either by `field_id` or more likely by the human-readable `external_id`. To use the `external_id` simply pretend the collection is an associative array:

{% highlight php startinline %}
// Get an item to work on
$item = PodioItem::get_base(123); // Get item with item_id=123

// Get the field with the external_id=sample-external-id
$field = $item->fields["sample-external-id"];

print "This field has the external_id: ".$field->external_id;
{% endhighlight %}

If you only have a field_id use the `get()` method to get the field:

{% highlight php startinline %}
// Get an item to work on
$item = PodioItem::get_base(123); // Get item with item_id=123

// Get the field with the field_id=456
$field = $item->fields->get(456);

print "This field has the field_id: ".$field->field_id;
{% endhighlight %}

#### Add field
You can add a field to the collection in the same way you add items to normal arrays. If you add a field that already exists the current one will be replaced.
{% highlight php startinline %}
// Create a new item
$item = new PodioItem();

// Create a fields collection
$collection = new PodioItemFieldCollection();

// Create a new field, in this case a text field
$field = new PodioItemTextField("my-sample-external-id");

// Add a new field to the collection
$collection[] = $field;
{% endhighlight %}

You can also create everything in one step:

{% highlight php startinline %}
// Create a new item with a fields collection
$item = new PodioItem(array('fields' =>
  new PodioItemFieldCollection(array(
    new PodioItemTextField("my-sample-external-id")
  ))
));
{% endhighlight %}

#### Remove field
You remove a field either by `unset` (if you have the `external_id`) or by calling the `remove()` method.

{% highlight php startinline %}
// Get an item to work on
$item = PodioItem::get_base(123); // Get item with item_id=123

// Remove the field with the external_id=sample-external-id
unset($item->fields["sample-external-id"]);
{% endhighlight %}

{% highlight php startinline %}
// Get an item to work on
$item = PodioItem::get_base(123); // Get item with item_id=123

// Remove the field with the field_id=456
$item->fields->remove(456);
{% endhighlight %}

#### Getting field values
See [Item field examples]({{site.baseurl}}/fields).

#### Change field value
The format varies a bit from field to field. You can see examples for all field types under [Item field examples]({{site.baseurl}}/fields). After you have changed the value of the field you must save your change back to the API. If you only change a single field you can save just that field, but if you are changing multiple fields it can perform better to save the entire item.

{% highlight php startinline %}
// Change a single field value and save.
$item = PodioItem::get_basic(123); // Get item with item_id=123

$field = $item->fields["sample-text-field"];
$field->values = "New value for this field";
$field->save();
{% endhighlight %}

{% highlight php startinline %}
// If you change multiple values save the entire item.
$item = PodioItem::get_basic(123); // Get item with item_id=123

$item->fields["my-sample-text-field"] = "New value for this field";
$item->fields["my-sample-progress-field"] = 75;
$item->save();
{% endhighlight %}

### Create item
To create a new item from scratch you create a new `PodioItem` without an `item_id`, attach your fields and call the save method.
{% highlight php startinline %}
// Create a field collection with some fields.
// Be sure to use the external_ids of your specific fields
$fields = new PodioItemFieldCollection(array(
  new PodioTextItemField(array("external_id" => "my-text-field", "values" => "FooBar")),
  new PodioProgressItemField(array("external_id" => "my-number-field", "values" => 75))
));

// Create the item object with fields
// Be sure to add an app or podio-php won't know where to create the item
$item = new PodioItem(array(
  'app' => new PodioApp(123), // Attach to app with app_id=123
  'fields' => $fields
));

// Save the new item
$item->save();
{% endhighlight %}

### Modifying items
Updating items are handled exactly the same way as creating items. If an item's `item_id` is set it will be updated once you call the `save()` method.

Often you will be fetching an existing item, making modifications and saving those changes:
{% highlight php startinline %}
// Get item from API
$item = PodioItem::get(123); Get item with item_id=123

// Make changes. Here we update the tags
$item->tags = array("my sample tag");

// Save your changes
$item->save();
{% endhighlight %}

You can also create the `PodioItem` object from scratch and save:

{% highlight php startinline %}
// Create item with item_id=123
$item = new PodioItem(123);

// Make changes. Here we set the tags
$item->tags = array("my sample tag");

// Save your changes to an existing item
$item->save();
{% endhighlight %}

## Item collections
One of the most common operations is getting a collection of items from an app, potentially with a filter applied. For this you can use [PodioItem::filter()](https://developers.podio.com/doc/items/filter-items-4496747). It returns a collection with two additional properties: filtered (total amount of items with the filter applied) and total (total amount of items in the app). You can iterate over this collection as normal.

{% highlight php startinline %}
$collection = PodioItem::filter(123); // Get items from app with app_id=123

print "The collection contains ".count($collection)." items";
print "There are ".$collection->total." items in the app in total";
print "There are ".$collection->filtered." items with the current filter applied";

// Output the title of each item
foreach ($collection as $item) {
  print $item->title;
}
{% endhighlight %}

### Sorting items
You can sort items by various properties. [See a full list in the API reference](https://developers.podio.com/doc/filters).
{% highlight php startinline %}
// Sort by last edit date for the items, descending
$collection = PodioItem::filter(123, array(
  'sort_by' => 'last_edit_on',
  'sort_desc' => true
));
{% endhighlight %}

### Filters
<span class="note">**Important:** You can use both `field_id` and `external_id` when filtering items. The examples below all use `field_id` for brevity.</span>

You can filter on most fields. Take a look at the [API reference for a full list of filter options](https://developers.podio.com/doc/filters). When filtering on app fields use the `field_id` or `external_id` as the key for your filter. Some examples below:

{% highlight php startinline %}
// Category: Only items with "FooBar" in category field value
$category_field_id = 1;
$collection = PodioItem::filter(123, array(
  'filters' => array(
    $category_field_id => array("FooBar")
  ),
));
{% endhighlight %}

{% highlight php startinline %}
// Number: Only items within a certain range
// Same concept for calculation, progress, duration & money fields
$number_field_id = 2;
$collection = PodioItem::filter(123, array(
  'filters' => array(
    $number_field_id => array("from" => 100, "to" => 200)
  ),
));
{% endhighlight %}

{% highlight php startinline %}
// App reference: Only items that has a specific reference
$app_reference_field_id = 3;

// Item id to filter against
$filter_target_item_id = 123456789;

$collection = PodioItem::filter(123, array(
  'filters' => array(
    $app_reference_field_id => array($filter_target_item_id)
  ),
));
{% endhighlight %}

{% highlight php startinline %}
// Contact: Only items that has a specific contact set
$contact_field_id = 4;

// profile_id of contact to filter on
$filter_target_profile_id = 123456789;

$collection = PodioItem::filter(123, array(
  'filters' => array(
    $contact_field_id => array($filter_target_profile_id)
  ),
));
{% endhighlight %}

{% highlight php startinline %}
// Date: Has several ways to filter.
// See https://developers.podio.com/doc/filters
$date_field_id = 4;

// Filter on absolute dates:
$collection = PodioItem::filter(123, array(
  'filters' => array(
    $date_field_id => array(
      "from" => "2014-01-01 00:00:00",
      "to" => "2014-01-31 23:59:59"
    )
  ),
));
{% endhighlight %}

