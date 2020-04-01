<?php

if (!function_exists('view')) {
    function view($name, $data = []) {
        foreach ($data as $key => $value) {
            $$key = $value;
        }
        $return = require_once APP_PATH . 'Views/' . $name . '.php';
        return $return;
    }
}

function value($input, $default = false) {
    if (isset($_SESSION['post'][$input])) {
        $return = $_SESSION['post'][$input];
        unset($_SESSION['post'][$input]);
        return $return;
    } elseif ($default) {
        return $default;
    }
    return false;
}

function href($url = false, $name = false, $class = false) {
    $return = $url = config('host') . $url;

    if ($name) {
        $name = match_lang($name);

        if ($class) {
            $class = ' class=" ' . $class . '"';
        }

        $return = '<a href="' . $url . '"' . $class . '>' . $name . '</a>';
    }

    return $return;
}

function match_lang($string) {
    if (preg_match('/^[a-z]+_.*/', $string)) {
        return lang($string);
    }
    return $string;
}

function exception($e, ... $errors)
{
    echo '<p style="font-weight: bold; color: red;">' . $e->getMessage() . '</p>';

    foreach ($errors as $error) {
        if ($error == 'post') {
            echo '<p style="font-weight: bold; color: black;">$_POST: ';
            print_r($_POST);
            echo '</p>';
        } else {
            echo '<p style="font-weight: bold; color: rgb(230,33,67);">' . $error . '</p>';
        }
    }

    $_SESSION['no_redirect'] = true;
}

function redirect($url = false) {
    global $config;

    if (isset($_SESSION['no_redirect'])) {
        unset($_SESSION['no_redirect']);
        return false;
    }

    header('location: ' . config('host') . $url);
    exit;
}

function redirect_with_input($url = false) {
    $_SESSION['post'] = $_POST;
    redirect($url);
}

function refresh() {
    header('location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

function back() {
    $referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    header('location: ' . $referer);
    exit;
}

function random_string($len = 10) {
    $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $return = false;

    for($i = 1; $i <= $len; $i++)
    {
        $mt_rand = mt_rand(0, strlen($string) - 1);
        $return .= $string[$mt_rand];
    }
    return $return;
}

function alert($message = false, $type = 'primary') {
    if ($message) {
        $_SESSION['alert']['message'] = preg_match('/^[a-z]+_/', $message) ? lang($message) : $message;
        $_SESSION['alert']['type'] = $type;
    } else {
        if (!empty($_SESSION['alert'])) {
            $alert = '<div class="alert alert-' . $_SESSION['alert']['type'] . ' alert-dismissible fade show" role="alert">' . $_SESSION['alert']['message'] . '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button></div>';
            unset($_SESSION['alert']);
            return $alert;
        }
    }
    return false;
}

function hsc($data) {
    if (is_array($data)){
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value);
        }
        return $data;
    }
    return htmlspecialchars($data);
}