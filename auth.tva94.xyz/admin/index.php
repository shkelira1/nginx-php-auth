<?php
include '../../include.php';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AUTH ADMIN</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
<div class="wrapper">
    <div class="admin-page">
        <div class="container">
            <div class="admin-page__inner">
                <h1 class="site__title">
                    <a href="/" class="site__title-text">AUTH ADMIN PAGE</a>
                </h1>

                <ul class="short__link">
                    <li><a href="<?= $config['pages']['auth_site_url'] ?>">Back</a></li>
                </ul>

                <div class="admin-actions">
                    <h2 class="admin-actions__title">Actions</h2>
                    <ul class="admin-actions__link">
                        <li><a href="/users" class="hbtn hb-fill-top-rev">User editor</a></li>
                        <li><a href="/otp.php" class="hbtn hb-fill-top-rev">OTP helper</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
