<?php

spl_autoload_register(function ($class_name) {
    $class_name = str_replace(['_', '\\'], ['/', '/'], $class_name);
    foreach ([
                 __DIR__ . '/app/',
                 __DIR__ . '/app/external/',
             ] as $dir) {
        $filename = $dir . $class_name . '.php';
        if (file_exists($filename)) {
            include $filename;
            return true;
        }
    }

    return false;
});

require __DIR__ . '/auth-config.php';

foreach ($config['php_ini_set'] ?? [] as $key => $value) {
    ini_set($key, $value);
}

$storage = Storage::getInstance();

if (($x = session_start($config['session'])) !== TRUE) {
    die('Session not created');
}
$_SESSION['created_time'] = $_SESSION['created_time'] ?? time();
