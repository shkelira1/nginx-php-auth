<?php

require '../../include.php';

$userName = $_REQUEST['user'] ?? null ?: 'user';
$auth = new PHPGangsta_GoogleAuthenticator();
$key = $_REQUEST['key'] ?? null ?: $auth->createSecret(64);
$urlQr = $auth->getQRCodeGoogleUrl($userName . '@auth.tva94.xyz', $key);

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/main.css">
    <title>Document</title>
</head>
<body>
<div class="wrapper">
    <div class="user-otp">
        <div class="container">
            <div class="user-otp__inner">
                <h1 class="site__title">
                    <a href="/" class="site__title-text">AUTH ADMIN PAGE</a>
                </h1>

                <ul class="short__link">
                    <li><a href="/">Home→</a></li>
                    <li><a href="/otp.php">Otp helper</a></li>
                </ul>

                <div class="user-otp__qr">
                    <h2 class="update__title">Google auth keygen</h2>
                    <form>
                        <label for="user" style="width: 50px; display: inline-block;">Name</label>
                        <input type="text" name="user" id="user" value="<?= $userName ?>" style="width: 400px;"><br>
                        <label for="key" style="width: 50px; display: inline-block;">Key</label>
                        <textarea name="key" id="key" rows="3" style="width: 400px"><?= $key ?></textarea><br>
                        <br>
                        <button type="submit" class="hbtn hb-fill-top-rev">Update</button>
                    </form>
                    <br>
                    <div class="user-otp__qr-img">
                        <img src="<?= $urlQr ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
