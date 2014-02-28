---
layout: default
active: items
---
# Working with Podio items

<span class="note">If you haven't read about [Podio* objects]({{site.baseurl}}/objects) yet, do so first.</span>

Apps and app items form the core of Podio and for that reason podio-php tries to make it as easy as possible to read and manipulate app items.



### App reference field

#### Getting values

App reference fields contains references to other items. When getting values they are returned as a PodioCollection of PodioItem objects:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'app-reference';
$collection = $item->fields[$field_id]->values;

foreach ($collection as $referenced_item) {
  print "Referenced item: ".$referenced_item->title;
}
{% endhighlight %}

#### Setting values
Setting a single value can be done by setting values to a single PodioItem object or by passing in an associative array of the item structure. The following are identical:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'app-reference';

// Set using object
$item->fields[$field_id]->values = new PodioItem(array('item_id' => 456));

// Set using associative array
$item->fields[$field_id]->values = array('item_id' => 456);
{% endhighlight %}

When setting multiple values you can use a PodioCollection or an array of associative arrays. The following are identical:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'app-reference';

// Set using object
$item->fields[$field_id]->values = new PodioCollection(array(
  new PodioItem(array('item_id' => 456)),
  new PodioItem(array('item_id' => 789))
));

// Set using associative array
$item->fields[$field_id]->values = array(
  array('item_id' => 456),
  array('item_id' => 678)
);
{% endhighlight %}
