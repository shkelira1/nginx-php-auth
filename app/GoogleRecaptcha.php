<?php

class GoogleRecaptcha
{
    protected $secretKey = null;

    public function __construct()
    {
        global $config;
        $this->secretKey = $config['google']['recaptcha_secret'] ?? null;
    }

    public function verify()
    {
        if (empty($this->secretKey)) return false;
        $response = $_POST["g-recaptcha-response"];
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->secretKey,
            'response' => $response
        ];
        $options = [
            'http' => [
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context  = stream_context_create($options);
        $verify = file_get_contents($url, false, $context);
        $captcha_success = json_decode($verify, true);
        return !empty($captcha_success['success']);
    }
}