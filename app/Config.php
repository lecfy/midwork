<?php
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

$config['host'] = '';

$config['title'] = '';

/*
 * Custom Routes
 * KEYS are custom routes
 * VALUES are real url, i.e. controller/method
 * e.g. $route['login'] = 'site/login';
 */

$route['default'] = 'home/index';


define('APP_PATH', __DIR__ . '/');
define('SYSTEM_PATH', __DIR__ . '/../system/');