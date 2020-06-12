<?php

require '../../include.php';

if (!isset($_REQUEST['action'])) die();
$redirect = $_REQUEST['redirect'] ?? null ?: '/';
$error = '';

if (empty($_REQUEST['action'])) {
    die('No action');
}
$loginProvider = null;
switch ($_REQUEST['action']) {
    case 'logout':
        AccountsProvider::resetCurrentLogin();
        $redirect = '/';
        break;

    default:
        $action = $_REQUEST['action'];
        $provider = $action ? ($config['login_providers'][$action] ?? null) : null;
        $provider = (@class_exists($provider) && is_subclass_of($provider, Abstract_LoginProvider::class)) ? $provider : null;

        if ($provider) {
            $provider = new $provider();
            if ($provider instanceof Abstract_LoginWithRedirectsProvider) {
                $provider->redirect();
            } else {
                AccountsProvider::loginAndRedirect($provider, $redirect);
            }
        } else {
            die('Wrong action');
        }
        break;
}

$redirect = '/';
http_response_code(302);
header("Location: {$redirect}");
