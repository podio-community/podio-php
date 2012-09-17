<?php

require_once 'lib/PodioAPI.php';
require_once 'lib/PodioResponse.php';
require_once 'lib/PodioOAuth.php';
require_once 'lib/PodioError.php';
require_once 'lib/PodioObject.php';

foreach (glob("models/*.php") as $filename) {
  require_once $filename;
}
