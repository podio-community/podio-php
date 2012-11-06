<?php

// This is a list of examples on podio-php basics.
// The script is not meant to be run (you'll get errors),
// it is simply an illustration of the syntax you must use.

// The first thing you must always do is to setup the API client and authenticate:

// Setup the client. See authentication.php
Podio::setup(CLIENT_ID, CLIENT_SECRET);

// Authenticate. In this case using App Authentication, but see authenticate.php for more ways to authenticate.
// You can check if authentication has already happened with Podio::is_authenticated()
// See session-manager.php for more
if (!Podio::is_authenticated()) {
  Podio::authenticate('app', array('app_id' => YOUR_APP_ID, 'app_token' => YOUR_APP_TOKEN));
}

// You will be working with two types of methods. Direct calls to the API is made using static methods on the classes:
$task = PodioTask::get(123);

// When possible these calls will return instances of the class in question. You can then work with that object directly. Above we get an instance of the PodioTask class and we can print the task_id and the link to the task. The properties are available directly on the object:
print $task->id;
print $task->link;

// Open each class in the 'models' folder to see which properties are available for each class.

// Some properties are instances of other classes. You can see these marked as 'has_one' or 'has_many' in the constructor.
// For example PodioTask has one 'created_by' (instance of PodioByLine) and it has_many 'labels' (array of PodioTaskLabel instances):
print get_class($task->created_by); // Will print 'PodioByLine'

// You can then drill into these relationships easily. E.g. to print the name of the author:
print $task->created_by->name;

// App items are the most complicated part of the Podio API and there are many shortcuts in the PHP library to make your life easier. See items.php for details.
