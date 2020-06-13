<?php

class AccountsProvider
{
    public static function getAccountById($id)
    {
        if (empty($id)) return null;
        return Storage::getInstance()->getAccount($id);
    }

    public static function getCurrentAccount()
    {
        global $config;

        if (isset($_SESSION['logged_in'])) {
            if (!empty($config['kill_session_ip_change'])) {
                $remoteAddr = $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
                if (empty($_SESSION['logged_in_ip']) || empty($remoteAddr)
                    || ($_SESSION['logged_in_ip'] != $remoteAddr)) {
                    self::resetCurrentLogin();
                    return null;
                }
            }

            $account = self::getAccountById($_SESSION['logged_in']);
            if (empty($account)) {
                self::resetCurrentLogin();
                return null;
            }
            return $account ?: null;
        }
        return null;
    }

    public static function setCurrentLogin($login)
    {
        if (!empty($login['account_id'])) {
            $_SESSION['logged_in'] = $login['account_id'];
            $_SESSION['logged_in_login_id'] = $login['id'];
            $_SESSION['logged_in_login_provider'] = $login['provider'];
            $_SESSION['logged_in_ip'] = $_SERVER['REMOTE_ADDR'];
        } else {
            self::resetCurrentLogin();
        }
    }

    public static function getLogin($provider, $login)
    {
        return Storage::getInstance()->getLogin($provider, $login);
    }

    public static function resetCurrentLogin()
    {
        session_unset();
    }

    public static function login($loginProvider)
    {
        global $config;
        $time = time();

        try {
            // Brute-force protection
            if (!empty($config['brute_force_protect'])) {
                $bpConfig = $config['brute_force_protect'];
                $lastTime = $_SESSION['last_error_time'] ?? $_SESSION['created_time'] ?? null;
                $needDelay = ($_SESSION['error_count'] ?? 0) > $bpConfig['warning_errors'] ? $bpConfig['warning_delay'] : $bpConfig['first_delay'];
                if (empty($lastTime) || ($time - $lastTime < $needDelay)) {
                    throw new Exception('TOO_FAST_REQUESTS');
                }
            }

            $login = $loginProvider->login();
            $account = !empty($login['account_id']) ? self::getAccountById($login['account_id']) : null;

            if (empty($account) || empty($account['active'])) {
                throw new Exception('ACCOUNT_DISABLED_OR_DELETED');
            }

            unset($_SESSION['last_error_time']);
            unset($_SESSION['error_count']);
        } catch (Exception $ex) {
            $_SESSION['last_error_time'] = time();
            $_SESSION['error_count'] = ($_SESSION['error_count'] ?? 0) + 1;
            throw $ex;
        }

        self::setCurrentLogin($login);
    }

    public static function loginAndRedirect(Abstract_LoginProvider $provider, $redirect = null)
    {
        $redirect = $redirect ?: $_SESSION['redirect-after-auth'] ?? '/';
        try {
            self::login($provider);
            unset($_SESSION['redirect-after-auth']);

        } catch (Exception $ex) {
            $error = $ex->getMessage();
            $redirect = '/?' . http_build_query([
                'url' => $redirect,
                'error' => $error,
                'action' => $provider::getActionName(),
            ]);
        }

        http_response_code(302);
        header("Location: {$redirect}");
        exit();
    }
}
