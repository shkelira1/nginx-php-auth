<?php
require '../../include.php';
$authUrl = $config['pages']['auth_site_url'] . '/';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AUTH</title>
    <link rel="stylesheet" href="<?= $config['pages']['auth_site_url'] ?>/css/style-errors.css">
</head>
<body>
<h1>403 &ndash; Forbidden</h1>
<h2>Site: <?= $_SERVER['HTTP_X_FORWARDED_HOST'] ?></h2>
<h2>Your ip: <?= $_SERVER['HTTP_X_REAL_IP'] ?></h2>
<a href="<?= $authUrl ?>">Go to login page &rarr;</a>
<? include 'bits/footer.php' ?>
</body>
</html>
