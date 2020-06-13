<?php

class LoginByOtpProvider extends Abstract_LoginProvider
{
    protected static string $actionName = 'login-otp';
    protected static string $friendlyName = 'Login with OTP';
    protected static string $formFileName = 'otp.php';

    public function __construct()
    {

    }

    protected function _getLogin($login)
    {
        return AccountsProvider::getLogin('LoginByOtpProvider', $login);
    }

    public function login()
    {
        global $config;

        if (!empty($config['otp']['require_captcha'])) {
            if (!(new GoogleRecaptcha())->verify()) {
                throw new Exception('WRONG_CAPTCHA');
            }
        }

        if (empty($_REQUEST['login']) || empty($_REQUEST['otp_password']) || ($_REQUEST['login'] == '__')) {
            throw new Exception('WRONG_CREDS');
        }
        if (!$login = $this->_getLogin($_REQUEST['login'])) {
            throw new Exception('WRONG_CREDS');
        }
        $auth = new PHPGangsta_GoogleAuthenticator();
        if (!$auth->verifyCode($login['data']['otp_secret'], $_REQUEST['otp_password'], 2)) {
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
            'otp_secret' => [
                'name' => 'Otp secret',
                'get' => false,
                'set' => true,
            ],
        ];
    }

    public function adminFieldsProcess($data, $oldData)
    {
        $data = array_intersect_key($data, ['_login' => 1, '_active' => 1, 'otp_secret' => 1]);
        if (isset($data['_login'])) {
            if (empty($data['_login'])) {
                unset($data['_login']);
            }
        }

        if (isset($data['otp_secret'])) {
            if (empty($data['otp_secret'])) {
                $data['otp_secret'] = !empty($oldData['data']['otp_secret']) ? $oldData['data']['otp_secret'] : '';
            }
        }
        return $data;
    }
}
