<?php

class LoginDemoProvider extends Abstract_LoginProvider
{
    protected static string $actionName = 'login-demo';
    protected static string $friendlyName = 'Demo login';
    protected static string $formFileName = 'demo.php';

    public function __construct()
    {

    }

    protected function _getLogin()
    {
        return AccountsProvider::getLogin('LoginDemoProvider', '');
    }

    public function login()
    {
        $login = $this->_getLogin();
        return $login ?? null;
    }
}
