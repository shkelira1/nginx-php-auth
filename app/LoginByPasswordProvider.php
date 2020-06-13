<?php

class LoginByPasswordProvider extends Abstract_LoginProvider
{
    protected static string $actionName = 'login-password';
    protected static string $friendlyName = 'Login with password';
    protected static string $formFileName = 'password.php';

    public function __construct()
    {

    }

    protected function _getLogin($login)
    {
        return AccountsProvider::getLogin('LoginByPasswordProvider', $login);
    }

    public function login()
    {
        global $config;

        if (!empty($config['password']['require_captcha'])) {
            if (!(new GoogleRecaptcha())->verify()) {
                throw new Exception('WRONG_CAPTCHA');
            }
        }

        if (empty($_REQUEST['login']) || empty($_REQUEST['password']) || ($_REQUEST['login'] == '__')) {
            throw new Exception('WRONG_CREDS');
        }
        if (!$login = $this->_getLogin($_REQUEST['login'])) {
            throw new Exception('WRONG_CREDS');
        }
        if (!password_verify($_REQUEST['password'], $login['data']['password'])) {
            throw new Exception('WRONG_CREDS');
        }
        return $login;
    }

    public function adminFieldsList()
    {
        return [
            '_login' => [
                'name' => 'Login',
                'get' => true, // Not used for login field
                'set' => true,
            ],
            'password' => [
                'name' => 'Password',
                'get' => false,
                'set' => true,
                'input' => 'password',
            ],
        ];
    }

    public function adminFieldsProcess($data, $oldData)
    {
        $data = array_intersect_key($data, ['_login' => 1, '_active' => 1, 'password' => 1]);
        if (isset($data['_login'])) {
            if (empty($data['_login'])) {
                unset($data['_login']);
            }
        }

        if (isset($data['password'])) {
            if (empty($data['password'])) {
                $data['password'] = !empty($oldData['data']['password']) ? $oldData['data']['password'] : '';
            } else {
                $data['password'] = password_hash($data['password'], PASSWORD_ARGON2I);
            }
        }
        return $data;
    }
}
