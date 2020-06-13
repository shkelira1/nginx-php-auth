<?php

$config = [
    'session' => [
        'name' => 'PHPSESSID_AUTH',
        'cookie_domain' => '.tva94.xyz',
        'cookie_secure' => 1,
        'save_path' => __DIR__ . '/sessions/',
        'save_handler' => 'files',
    ],
    'pages' => [
        'show_footer' => true,
        'auth_site_url' => "https://auth.tva94.xyz",
    ],
    'php_ini_set' => [
//  Uncomment next lines for debug (show error messages)
//        'error_reporting' => E_ALL,
//        'display_errors' => 1,
//  Make section 'session' empty array and use this to store sessions in memcache
//        'memcached.sess_prefix' => 'php.authsite.',
//        'session.gc_maxlifetime' => 3600 * 24,
//        'session.name' => 'PHPSESSID_AUTH',
//        'session.cookie_domain' => '.tva94.xyz',
//        'session.cookie_secure' => 1,
//        'session.save_handler' => 'memcache',
//        'session.save_path' => 'unix:///var/run/memcached/memcached.sock:0',
    ],
    'db' => [
        'host' => '127.0.0.1',
        // 'port' => 3306,
        // 'socket' => '/var/run/mysqld/mysqld.sock', // Use host or socket
        'db' => 'site_auth',
        'user' => 'site_auth',
        'password' => 'password',
        'charset' => 'UTF8',
    ],
    'google' => [
        'client_id' => '',
        'client_secret' => '',
        'redirect_url' => 'https://auth.tva94.xyz/auth-google.php',

        'recaptcha_secret' => '',
        'recaptcha_public' => '',
    ],
    'telegram' => [
        'bot_token' => '',
        'bot_username' => '',
    ],
    'password' => [
        'require_captcha' => false,
    ],
    'otp' => [
        'require_captcha' => false,
    ],
    'login_providers' => [
        LoginByPasswordProvider::getActionName() => LoginByPasswordProvider::class,
        LoginByOtpProvider::getActionName() => LoginByOtpProvider::class,
        // LoginDemoProvider::getActionName() => LoginDemoProvider::class,
        // LoginGoogleProvider::getActionName() => LoginGoogleProvider::class,
        // LoginTelegramProvider::getActionName() => LoginTelegramProvider::class,
        // LoginByIpProvider::getActionName() => LoginByIpProvider::class,
    ],
    'sites' => [
        'demo1.tva94.xyz',
        'demo2.tva94.xyz',
        'auth-admin.tva94.xyz',
        'auth-pma.tva94.xyz',
    ],
    'brute_force_protect' => [
        'first_delay' => 3,
        'warning_delay' => 5,
        'warning_errors' => 3,
    ],
    'kill_session_ip_change' => true,
];
