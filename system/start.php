<?php

if(!isset($_SESSION)) session_start();

/*
 * Configuration + overriding by .env
 */

$env = [];
if (file_exists(APP_PATH . '../.env')) {
    $env = parse_ini_file(APP_PATH . '../.env');

    // if file .env exists, developer mode = on
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function config($key) {
    global $env;
    global $config;
    global $route;

    if ($env) $config['dev'] = true;

    $config['route'] = $route;

    if (isset($env[$key])) {
        return $env[$key];
    }

    return isset($config[$key]) ? $config[$key] : false;
}

/*
 * Common Functions
 */

require_once APP_PATH . '/Common.php';
require_once SYSTEM_PATH . '/Common.php';

/*
 * Language
 */

$lang = [];
if (config('lang')
    && in_array(config('lang'), config('languages'))
    && file_exists(APP_PATH . 'Language/' . config('lang') . '.php')
) {
    require_once (APP_PATH . 'Language/' . config('lang') . '.php');
}

function lang($key, $replace = false) {
    global $lang;

    if ($replace) {
        $search = is_array($replace) ? array_keys($replace) : '%s';
        $lang[$key] = str_replace($search, $replace, $lang[$key]);
    }

    return !empty($lang[$key]) ? $lang[$key] : $key;
}

/*
 * Autoloader
 */

spl_autoload_register(function ($class) {
    if (file_exists(APP_PATH . '/Controllers/' . $class . '.php')) {
        require_once APP_PATH . '/Controllers/' . $class . '.php';
    } elseif (file_exists(SYSTEM_PATH . '/' . $class . '.php')) {
        require_once SYSTEM_PATH . '/' . $class . '.php';
    } else {
        redirect();
    }
});

/*
 * Routing
 */

$full_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$full_url = rtrim($full_url, '/');

$uri = str_replace(config('host'), '', $full_url);

if (!preg_match('/^[\/a-z0-9_-]+$/i', $uri)) {
     $uri = config('route')['default'];
}

if (in_array($uri, array_keys(config('route')))) {
    $uri = config('route')[$uri];
}

$explode = explode('/', $uri);

/*
 * Class/Object
 */
$class_name = !empty($explode[0]) ? ucfirst($explode[0]) : 'home';
$object = new $class_name;
unset($explode[0]);

/*
 * Method
 */
$method = !empty($explode[1]) ? $explode[1] : 'index';
if (!method_exists($object, $method)) {
    redirect();
}
unset($explode[1]);


/*
 * Loading
 */
call_user_func_array([$object, $method], array_values($explode));