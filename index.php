<?php
require 'autoload.php';

$segment = explode('/', str_replace('\\', '/', dirname(__FILE__)));
define('BASE_DIR', array_pop($segment));
define('ROOT_DIR', str_replace('\\', '/', dirname(__FILE__)));

use Components\Core;

$core = new Core;