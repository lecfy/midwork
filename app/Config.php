<?php
/*
 * IMPORTANT! Update your path to the system folder
 */
define('SYSTEM_PATH', __DIR__ . '/../system/');
define('APP_PATH', __DIR__ . '/');

/*
 * IMPORTANT! Full website url with trailing slash, e.g. https://midreg.com/
 */
$config['host'] = '';

/*
 * Database
 */
$config['db_host'] = 'localhost'; ;
$config['db_name'] = ''; // database
$config['db_user'] = ''; // username connected to database
$config['db_password'] = ''; // user password

/*
 * Language
 */
$config['lang'] = '';
$config['languages'] = [];

$config['title'] = '';

/*
 * Custom Routes
 * KEYS are custom routes
 * VALUES are real url, i.e. controller/method
 * e.g. $route['login'] = 'site/login';
 */

$route['default'] = 'home/index';