<?php

// Version
define('VERSION', '1.0.0.0');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('catalog');
