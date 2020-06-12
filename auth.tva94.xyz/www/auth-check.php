<?php

require '../../include.php';

// file_put_contents('/tmp/data.json', json_encode($_SERVER, JSON_PRETTY_PRINT));
// file_put_contents('/tmp/data.json', json_encode($_SESSION, JSON_PRETTY_PRINT));

if ($account = AccountsProvider::getCurrentAccount()) {
    if (empty($account) || empty($account['active'])) {
        http_response_code(401);
    }
    if (in_array($_SERVER['HTTP_X_FORWARDED_HOST'], $account['allowed_sites'] ?? [])) {
        http_response_code(200);
        header("X-Auth-Id: {$_SESSION['logged_in']}");
        header("X-Auth-Name: {$account['name']}");
    } else {
        http_response_code(403);
    }
} else {
    http_response_code(401);
}
