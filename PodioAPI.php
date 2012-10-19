<?php

require_once 'lib/PodioAPI.php';
require_once 'lib/PodioResponse.php';
require_once 'lib/PodioOAuth.php';
require_once 'lib/PodioError.php';
require_once 'lib/PodioObject.php';
require_once 'lib/PodioLogger.php';

require_once 'models/PodioSuperApp.php'; // Included manually because other models inherits from this
foreach (glob("models/*.php") as $filename) {
  require_once $filename;
}
