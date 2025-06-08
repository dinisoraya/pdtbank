<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'pdtbank');
define('DB_USER', 'root');
define('DB_PASS', '');

if (php_sapi_name() == 'cli') {
    define('BASE_URL', '');
} else {
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

    if ($path === '' || $path === '/') {
        define('BASE_URL', "http://$host");
    } else {
        define('BASE_URL', "http://$host$path");
    }
}
