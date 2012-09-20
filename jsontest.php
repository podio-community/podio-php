<?php
// Include the config file and the Podio library
require_once 'examples/config.php';
require_once 'PodioAPI.php';

$task = new PodioTask();
$task->id = 123;


print $task;
