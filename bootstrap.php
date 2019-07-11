<?php

define('ROOT_DIR', __DIR__);
define('CRAWLER_DIR', ROOT_DIR . '/crawler');
define('CONFIG_DIR', ROOT_DIR . '/config');
define('TEMP_DIR', ROOT_DIR . '/runtime');
define('LIB_DIR', ROOT_DIR . '/fastcrawler');

require ROOT_DIR . '/vendor/autoload.php';

$core = \Fastcrawler\Core::init();

return $core;
