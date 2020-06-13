<?php

class LoginByIpProvider extends Abstract_LoginProvider
{
    protected static string $actionName = 'login-by-ip';
    protected static string $friendlyName = 'Login by ip address';
    protected static string $formFileName = 'ip.php';

    public function __construct()
    {

    }

    protected function _getLogin($ip)
    {
        return AccountsProvider::getLogin('LoginByIpProvider', $ip);
    }

    public function login()
    {
        if (empty($_SERVER['REMOTE_ADDR'])) {
            throw new Exception('WRONG_CREDS');
        }
        if (!$login = $this->_getLogin($_SERVER['REMOTE_ADDR'])) {
            throw new Exception('WRONG_CREDS');
        }

        return $login;
    }

    public function adminFieldsList()
    {
        return [
            '_login' => [
                'name' => 'ip',
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
