<?php
require '../../include.php';
$fromUrl = $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://' . $_SERVER['HTTP_X_FORWARDED_HOST'] . $_SERVER['HTTP_X_FORWARDED_URI'];
$authUrl = $config['pages']['auth_site_url'] . '/?url=' . urlencode($fromUrl);
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AUTH</title>
    <link rel="stylesheet" href="<?= $config['pages']['auth_site_url'] ?>/css/style-errors.css">
</head>
<body>
<h1>401 &ndash; Need auth</h1>
<h2>Site: <?= $_SERVER['HTTP_X_FORWARDED_HOST'] ?></h2>
<h2>Your ip: <?= $_SERVER['HTTP_X_REAL_IP'] ?></h2>
<a href="<?= $authUrl ?>">Go to login page &rarr;</a>
<? include 'bits/footer.php' ?>
</body>
</html>
