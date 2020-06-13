<?php

class LoginTelegramProvider extends Abstract_LoginWithRedirectsProvider
{
    protected static string $actionName = 'login-telegram';
    protected static string $friendlyName = 'Telegram';
    protected static string $formFileName = 'telegram.php';

    public function __construct()
    {
        global $config;
        $this->_bot_token = $config['telegram']['bot_token'] ?? null;
        $this->_bot_username = $config['telegram']['bot_username'] ?? null;
    }

    protected function _getLogin($login)
    {
        return AccountsProvider::getLogin('LoginTelegramProvider', $login);
    }

    public function checkAuth($auth_data)
    {
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', $this->_bot_token, true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            throw new Exception('WRONG_RESPONSE');
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            throw new Exception('WRONG_RESPONSE');
        }
        return $auth_data;
    }

    public function redirect()
    {
        $redirect = '/auth-tg.php';
        header("Location: {$redirect}");
        exit();
    }

    public function login()
    {
        $authData = $this->checkAuth($_GET);
        $login = $this->_getLogin($authData['username']);

        if (empty($login)) {
            throw new Exception('WRONG_CREDS');
        }

        return $login;
    }

    public function adminFieldsList()
    {
        return [
            '_login' => [
                'name' => 'Username',
                'get' => true, // Not used for login field
                'set' => true,
            ],
        ];
    }

    public function adminFieldsProcess($data, $oldData)
    {
        $data = array_intersect_key($data, ['_login' => 1, '_active' => 1]);
        if (isset($data['_login'])) {
            if (empty($data['_login'])) {
                unset($data['_login']);
            }
        }

        return $data;
    }
}
