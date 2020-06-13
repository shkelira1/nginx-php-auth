<?php

class LoginGoogleProvider extends Abstract_LoginWithRedirectsProvider
{
    protected static string $actionName = 'login-google';
    protected static string $friendlyName = 'Google';
    protected static string $formFileName = 'google.php';

    protected $_clientId;
    protected $_clientSecret;
    protected $_redirect
    ;
    public function __construct()
    {
        global $config;
        $this->_clientId = $config['google']['client_id'] ?? null;
        $this->_clientSecret = $config['google']['client_secret'] ?? null;
        $this->_redirect = $config['google']['redirect_url'] ?? null;
    }

    protected function _getLogin($login)
    {
        return AccountsProvider::getLogin('LoginGoogleProvider', $login);
    }

    public function redirect()
    {
        $url = 'https://accounts.google.com/o/oauth2/auth';
        $params = [
            'redirect_uri' => $this->_redirect,
            'response_type' => 'code',
            'client_id' => $this->_clientId,
            'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
        ];

        http_response_code(302);
        $redirect = $url . '?' . urldecode(http_build_query($params));
        header("Location: {$redirect}");
        exit();
    }

    public function login()
    {
        if (empty($_GET['code'])) {
            throw new Exception('WRONG_RESPONSE');
        }
        $params = [
            'client_id' => $this->_clientId,
            'client_secret' => $this->_clientSecret,
            'redirect_uri' => $this->_redirect,
            'grant_type' => 'authorization_code',
            'code' => $_GET['code'],
        ];

        $url = 'https://accounts.google.com/o/oauth2/token';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);
        $tokenInfo = json_decode($result, true);

        if (empty($tokenInfo['access_token'])) {
            throw new Exception('WRONG_RESPONSE');
        }

        $params['access_token'] = $tokenInfo['access_token'];

        $userInfo = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo' . '?' . urldecode(http_build_query($params))), true);

        if (empty($userInfo['id']) || empty($userInfo['verified_email'])) {
            throw new Exception('WRONG_RESPONSE');
        }

        $login = $this->_getLogin($userInfo['email']);

        if (empty($login)) {
            throw new Exception('WRONG_CREDS');
        }

        return $login;
    }

    public function adminFieldsList()
    {
        return [
            '_login' => [
                'name' => 'Email',
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
