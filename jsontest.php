<?php
// Include the config file and the Podio library
require_once 'examples/config.php';
require_once 'PodioAPI.php';

$task = new PodioTask();
$task->id = 123;

$item = new PodioItem();
$item->id = 123;
$item->rights = array('update', 'delete');

var_dump($task->can('asd'));
