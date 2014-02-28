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
### Create item
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

