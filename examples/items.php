<?php

// This is a list of examples on how to work with items.
// The script is not meant to be run (you'll get errors),
// it is simply an illustration of the syntax you must use.


// Setup the client. See authentication.php
Podio::setup(CLIENT_ID, CLIENT_SECRET);

// Authenticate. In this case using App Authentication, but see authenticate.php for more ways to authenticate.
Podio::authenticate('app', array('app_id' => YOUR_APP_ID, 'app_token' => YOUR_APP_TOKEN));

// Get a single item by item_id
// $item will hold an instance of the PodioItem class.
// See the available properties in PodioItem.php
$item = PodioItem::get( YOUR_ITEM_ID );

// You can for example print the title
print $item->title;

// You can access the fields in the 'fields' property (an array of PodioItemField instances)
foreach ($item->fields as $field) {
  print "This field has the external_id: {$field->external_id}\n";
}

// Even easier you can access individual fields by external_id or field_id using the 'field' method.
// E.g. to get the field with an external_id of 'title' you would do:
$field = $item->field('title');

// You can also get all fields of a specific type. E.g. if you want to get all date fields:
$date_fields = $item->fields_of_type('date');

// Now that you have a PodioItemField you can print the 'humanized_value' (a value that's easy to read for humans)
print $field->humanized_value();

// The raw values are in the 'values' property. Their format depends on the field type.
print_r($field->values);

// Certain field types have extra methods. For example an App Reference field can give you a list of PodioItem instances so you don't have to work with complicated JSON structures.
// See what other special methods are available in PodioItemField.php
$field = $item->field('app_field_external_id');
foreach ($field->items() as $referenced_item) {
  print "I am an instance of PodioItem with title: {$referenced_item->title}\n";
}

// You can change the value of a PodioItemField by using 'set_value'. This methods takes one argument and tries to be smart about assigning values.

// E.g. to change the value of a text field:
$item->field('title')->set_value('This is my new title');

// Or an image field which takes an array of file_ids
$item->field('image-field')->set_value(array(1, 2, 3));

// If you already have a PodioItem or a PodioItemField you can save any changes back to Podio by calling the 'save' method.
// For example if you want to change the value of a single text field and then save the new field value:
$item->field('title')->set_value('My new title')->save();

// If you are making changes to multiple fields you can save the entire item in one go:
$item->field('title')->set_value('My new title');
$item->field('image-field')->set_value(array(1, 2, 3));
$item->save();

// Often you want to get a collection of items rather than a single one. You do so with 'filter'
$item_collection = PodioItem::filter( YOUR_APP_ID );

// A collection is an associative array with some extra data in addition to the list of items.
print $item_collection['total']; // The total amount of items in the app
print $item_collection['filtered']; // Number of items matching the current filter
print_r($item_collection['items']); // Array of PodioItem instances

// PodioItem::filter is very powerful. You can filter and sort on almost any field. See all the details on https://developers.podio.com/doc/filters
// If you have a money field with a field_id of '1234' and you want to get all items in the app where the value is between 100 and 200:
$item_collection = PodioItem::filter(YOUR_APP_ID, array(
  'filters' => array(1234 => array('from' => 100, 'to' => 200)),
));

// The following field types use the 'from' / 'to' format for filtering: number, money, calculation, progress, duration, date

// With other fields -- category, app reference, contact, question -- you provide an array of ids to match. E.g. if you want to get a collection of items where the app reference field with the field_id 5678 only matches items referencing items with the item_ids 1, 2 and 3:

$item_collection = PodioItem::filter(YOUR_APP_ID, array(
  'filters' => array(5678 => array(1, 2, 3)),
));

// You can set sorting options and limit and offset values also. E.g. getting the 100 first items, sorted by their last edit date:

$item_collection = PodioItem::filter(YOUR_APP_ID, array(
  'sort_by' => 'last_edit_on',
  'sort_desc' => true,
  'limit' => 100,
  'offset' => 0,
));

// It's also doable to create new items from scratch (useful if you are migrating data from another system).

// Create the base item. Notice how you have to create a PodioApp instance. This is so we know what app to store the item in.
$item = new PodioItem(array(
  'app' => new PodioApp( YOUR_APP_ID ),
  'fields' => array(),
  'external_id' => 'my-legacy-id-number', // It's often useful to store ids from other systems in the external_id property
));

// We can add some fields to the item:
$item->fields = array(
  new PodioTextItemField('title'),
  new PodioImageItemField('image-field'),
);

// Or you can use add_field and remove_field methods to add/remove fields one at a time:
$item->add_field(new PodioNumberItemField('number-field')); // Field object must have a field_id or an external_id
$item->remove_field('number-field'); // Remove by field_id or external_id

// Notice how the external_id of the fields is being passed as the first argument to the constructor? When you create new instances of any of the Podio objects you can send three things to the constructor:

// 1. An associative array of properties. Like we did above when creating the item.
// 2. A string representing the external_id you want to set. Like we did for the PodioItemFields just above
// 3. An id value. Like we did for the PodioApp we attached to the PodioItem above.

// Give the fields values:
$item->field('title')->set_value('My title');
$item->field('image-field')->set_value(array(1, 2, 3));

// It is slightly cumbersome to first create the PodioItemFields and afterwards set the value for each one. You can set the values directly when creating the instances if you wish, but since the format is different for each field type it's often easier to call 'set_value' and have the formatting handled automatically.

// Finally save the item to Podio:
$item->save();
