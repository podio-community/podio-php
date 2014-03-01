---
layout: default
active: items
---
# Working with Podio items

<span class="note">If you haven't read about [Podio* objects]({{site.baseurl}}/objects) yet, do so first.</span>

Apps and app items form the core of Podio and for that reason podio-php tries to make it as easy as possible to read and manipulate app items.

## Individual items
### Get item (basic)
### Item fields
#### Get field
#### Add field
#### Remove field
#### Change field value
The format varies a bit from field to field. You can see examples for all field types under [Item field examples]({{site.baseurl}}/fields). After you have changed the value of the field you must save your change back to the API. If you only change a single field you can save just that field, but if you are changing multiple fields it can perform better to save the entire item.

{% highlight php startinline %}
// Change a single field value and save.
$item = PodioItem::get_basic(123); // Get item with item_id=123

$field = $item->fields["sample-text-field"];
$field->values = "New value for this field";
$field->save();

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

### Update item

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

