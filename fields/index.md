---
layout: default
active: fields
---
# Field reference
Below you'll find examples for getting and setting field values for each of the fields available in Podio.

* [Relationship / App reference field](#relationshipapp-reference-field)
* [Calculation field](#calculation-field)
* [Category field](#category-field)
* [Contact field](#contact-field)
* [Date field](#date-field)
* [Duration field](#duration-field)
* [Image field](#image-field)
* [Link/embed field](#linkembed-field)
* [Location/Google Maps field](#locationgoogle-maps-field)
* [Money field](#money-field)
* [Number field](#number-field)
* [Progress field](#progress-field)
* [Text field](#text-field)

## Relationship / App reference field

#### Getting values
Values are returned as a PodioCollection of PodioItem objects:

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

------------------------------------------------------------------------------

## Calculation field

#### Getting values
Value is provided as a string with four decimals. It's often nicer to use `humanized_value()` which formats the number:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'calculation';

print $item->fields[$field_id]->values; // E.g. 123.5600
print $item->fields[$field_id]->humanized_value(); // E.g. 123.56
{% endhighlight %}

Calculation fields are read-only. It's not possible to modify the value.

------------------------------------------------------------------------------

## Category field

#### Getting values
Category and Question fields function in the same manner. Values are provided as an array of options.

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'category';

foreach ($item->fields[$field_id]->values as $option) {
  print "Option text: ".$option['text'].'. Option id: '.$option['id'];
}

{% endhighlight %}

#### Setting values
Set a single value by using the option_id. You can also add a value with `add_value()`
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'category';

// Set value to a single option
$item->fields[$field_id]->values = 4; // option_id=4

// Add value to existing selection
$item->fields[$field_id]->add_value(4); // option_id=4
{% endhighlight %}

Use an array to set multiple values
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'category';

$item->fields[$field_id]->values = array(4,5,6); // option_ids: 4, 5 and 6
{% endhighlight %}


------------------------------------------------------------------------------

## Contact field

#### Getting values

Values are returned as a PodioCollection of PodioContact objects:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'contact';
$collection = $item->fields[$field_id]->values;

foreach ($collection as $contact) {
  print "Contact: ".$contact->name;
}
{% endhighlight %}

#### Setting values
Setting a single value can be done by setting values to a single PodioContact object or by passing in an associative array of the contact structure. The following are identical:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'contact';

// Set using object
$item->fields[$field_id]->values = new PodioContact(array('profile_id' => 456));

// Set using associative array
$item->fields[$field_id]->values = array('profile_id' => 456);
{% endhighlight %}

When setting multiple values you can use a PodioCollection or an array of associative arrays. The following are identical:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'contact';

// Set using object
$item->fields[$field_id]->values = new PodioCollection(array(
  new PodioContact(array('profile_id' => 456)),
  new PodioContact(array('profile_id' => 789))
));

// Set using associative array
$item->fields[$field_id]->values = array(
  array('profile_id' => 456),
  array('profile_id' => 678)
);
{% endhighlight %}


------------------------------------------------------------------------------

## Date field

#### Getting values
Date field values have two components: The start date and the end date. You can access these through special properties, both are PHP DateTime objects. You can also access date and time sections individually. This is often preferred as the time component will be null for events without time.
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'date';

$item->fields[$field_id]->start; // E.g. DateTime or null
$item->fields[$field_id]->start_date; // E.g. DateTime or null
$item->fields[$field_id]->start_time; // E.g. DateTime or null
$item->fields[$field_id]->end; // E.g. DateTime or null
$item->fields[$field_id]->end_date; // E.g. DateTime or null
$item->fields[$field_id]->end_time; // E.g. DateTime or null
$item->fields[$field_id]->humanized_value; E.g. "2014-02-14 14:00-15:00"
{% endhighlight %}

#### Setting values
You can simply assign values to the special properties to modify the field value. Both DateTime objects and strings are accepted. DateTime objects can be provided in any timezone and will be converted to UTC. String values **must** be provided as UTC values.
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'date';

// Set start date using DateTime
$item->fields[$field_id]->start = DateTime::createFromFormat('Y-m-d H:i:s', '2012-12-24 14:00:00', new DateTimeZone("UTC"));

// Set dates and times individually.
$item->fields[$field_id]->start_date = DateTime::createFromFormat('Y-m-d', '2012-12-24', new DateTimeZone("UTC"));

$item->fields[$field_id]->start_time = DateTime::createFromFormat('H:i:s', '14:00:00', new DateTimeZone("UTC"));

// Setting end date and end time
$item->fields[$field_id]->end_date = DateTime::createFromFormat('Y-m-d', '2012-12-24', new DateTimeZone("UTC"));

$item->fields[$field_id]->end_time = DateTime::createFromFormat('H:i:s', '14:00:00', new DateTimeZone("UTC"));

// Set start date using strings in various forms
$item->fields[$field_id]->start = "2012-12-24";

$item->fields[$field_id]->start = "2012-12-24 14:00:00";

// Remove values by setting their values to null
$item->fields[$field_id]->start_date = null;
$item->fields[$field_id]->start_time = null;
$item->fields[$field_id]->end_date = null;
$item->fields[$field_id]->end_time = null;

{% endhighlight %}



------------------------------------------------------------------------------


## Duration field

#### Getting values
Progress fields return a simple integer representing the duration in seconds. Often you will want to use `humanized_value()` for a formatted display. You can use `hours()`, `minutes()` and `seconds()` to avoid doing your own math.
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'duration';

print $item->fields[$field_id]->values; // E.g. 3604 for one hour and 4 seconds
print $item->fields[$field_id]->humanized_value(); // E.g. "01:00:04"

print $item->fields[$field_id]->hours(); // E.g. 1
print $item->fields[$field_id]->minutes(); // E.g. 0
print $item->fields[$field_id]->seconds(); // E.g. 4

{% endhighlight %}

#### Setting values
Simply assign a new integer to set the value
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'duration';

// Set using object
$item->fields[$field_id]->values = 75; // One minute and 15 seconds ((60*1)+15)
{% endhighlight %}


------------------------------------------------------------------------------

## Image field

#### Getting values

Values are returned as a PodioCollection of PodioFile objects:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'image';
$collection = $item->fields[$field_id]->values;

foreach ($collection as $file) {
  print "File id: ".$file->file_id;
  print "File URL: ".$file->link;
}
{% endhighlight %}

You can download the files as usual

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'image';

// Download the first image
$file = $item->fields[$field_id]->values[0];
$file_content = $file->get_raw();
file_put_contents("/path/to/file".$file->name, $file_content);
{% endhighlight %}

#### Setting values
Setting a single value can be done by setting values to a single PodioFile object or by passing in an associative array of the file structure. You have to upload a file to get a file_id to use. The following are identical:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'image';

// Upload file
$file = PodioFile::upload("/path/to/file", "sample.jpg");

// Set using object
$item->fields[$field_id]->values = $file;

// Set using associative array
$item->fields[$field_id]->values = array('file_id' => $file->file_id);
{% endhighlight %}

When setting multiple values you can use a PodioCollection or an array of associative arrays. The following are identical:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'image';

// Upload files
$file_a = PodioFile::upload("/path/to/file_a", "sample_a.jpg");
$file_b = PodioFile::upload("/path/to/file_b", "sample_b.jpg");

// Set using object
$item->fields[$field_id]->values = new PodioCollection(array(
  $file_a,
  $file_b
));

// Set using associative array
$item->fields[$field_id]->values = array(
  array('file_id' => 456),
  array('file_id' => 678)
);
{% endhighlight %}


------------------------------------------------------------------------------

## Link/Embed field

#### Getting values

Values are returned as a PodioCollection of PodioEmbed objects:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'embed';
$collection = $item->fields[$field_id]->values;

foreach ($collection as $embed) {
  print "Embed id: ".$embed->embed_id;
  print "Embed URL: ".$embed->original_url;
}
{% endhighlight %}

#### Setting values
Setting a single value can be done by setting values to a single PodioEmbed object or by passing in an associative array of the embed structure. You will need to create the embed first. The following are identical:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'embed';

// Create embed
$embed = PodioEmbed::create(array('url' => 'http://example.com/'));

// Set using object
$item->fields[$field_id]->values = $embed;

// Set using associative array
$item->fields[$field_id]->values = array('embed_id' => $embed->embed_id);
{% endhighlight %}

When setting multiple values you can use a PodioCollection or an array of associative arrays. The following are identical:

{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'embed';

// Create embeds
$embed_a = PodioEmbed::create(array('url' => 'http://example.com/'));
$embed_b = PodioEmbed::create(array('url' => 'http://podio.com/'));

// Set using object
$item->fields[$field_id]->values = new PodioCollection(array(
  $embed_a,
  $embed_b
));

// Set using associative array
$item->fields[$field_id]->values = array(
  array('embed_id' => 456),
  array('embed_id' => 789)
);
{% endhighlight %}


------------------------------------------------------------------------------

## Location/Google Maps field

#### Getting values
Location fields returns an array with location data
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'location';

print_r($item->fields[$field_id]->values);

// Or just print the address:
print $item->fields[$field_id]->text;
{% endhighlight %}

#### Setting values
Set values using an array of location data
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'location';

// Set using array
$item->fields[$field_id]->values = array(
  'value' => '650 Townsend St., San Francisco, CA 94103',
  'lat' => 37.7710325,
  'lng' => -122.4033069
);

// You can also set just the text part of the location:
$item->fields[$field_id]->text = '650 Townsend St., San Francisco, CA 94103';

{% endhighlight %}


------------------------------------------------------------------------------

## Money field

#### Getting values
Money field values have two components: The amount and the currency. You can access these through special properties. The amount is a string in order to support very large numbers. Often you'll use `humanized_value()` which provides a formatted value.
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'money';

print $item->fields[$field_id]->currency; // E.g. "USD"
print $item->fields[$field_id]->amount; // E.g. "123.5400"
print $item->fields[$field_id]->humanized_value(); E.g. "$123.54"

{% endhighlight %}

#### Setting values
You can simply assign values to `currency` and `amount` properties to modify the value.
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'money';

// Set value
$item->fields[$field_id]->currency = "EUR";
$item->fields[$field_id]->amount = "456.00";
{% endhighlight %}


------------------------------------------------------------------------------

## Number field

#### Getting values
The value of a number field is a string in order to support very large numbers. Use `humanized_value()` to get a formatted string.
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'number';

print $item->fields[$field_id]->values; // E.g. 123.5600
print $item->fields[$field_id]->humanized_value(); // E.g. 123.56
{% endhighlight %}

#### Setting values
Simply assign a new string to set the value. Use a period "." as the decimal point
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'number';

// Set using object
$item->fields[$field_id]->values = "456.89";
{% endhighlight %}


------------------------------------------------------------------------------

## Progress field

#### Getting values
Progress fields return a simple integer between 0 and 100.
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'progress';

print $item->fields[$field_id]->values; // E.g. 55
{% endhighlight %}

#### Setting values
Simply assign a new integer to set the value
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'progress';

// Set using object
$item->fields[$field_id]->values = 75;
{% endhighlight %}


------------------------------------------------------------------------------

## Text field

#### Getting values
Text fields return a regular string
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'text';

print $item->fields[$field_id]->values;
{% endhighlight %}

#### Setting values
Simply assign the new string to set the value
{% highlight php startinline %}
$item = PodioItem::get_basic(123);
$field_id = 'text';

// Set using object
$item->fields[$field_id]->values = 'This is the new value for the field';
{% endhighlight %}
