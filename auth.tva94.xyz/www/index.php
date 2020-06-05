<?php
require '../../include.php';
$redirect = $_REQUEST['url'] ?? '';
$error = $_REQUEST['error'] ?? null;
$action = $_REQUEST['action'] ?? null; //  ?: LoginByPasswordProvider::ACTION_NAME;
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <title>AUTH</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
<div class="wrapper">
    <div class="site">
        <div class="container">
            <div class="site__inner">
                <h1 class="site__title">
                    <a href="/" class="site__title-text">AUTH SITE</a>
                </h1>
                <? if (empty($_SESSION['logged_in']) || empty($account = AccountsProvider::getCurrentAccount())): ?>
                    <? if (!empty($error)): ?>
                        <div class="error">
                            Error: <?= $error ?>
                        </div>
                    <? endif; ?>

                    <ul class="login-providers-list">
                        <? foreach ($config['login_providers'] as $provider): ?>
                            <? if (!@class_exists($provider) || !is_subclass_of($provider, Abstract_LoginProvider::class)) continue; ?>
                            <? $providerAction = $provider::getActionName(); ?>
                            <li<?= ($providerAction == $action) ? ' class="active"' : '' ?>
                                    class="login-providers-list-data">
                                <a class="hbtn hb-fill-top-rev" href="/?<?= http_build_query([
                                        'action' => $providerAction
                                    ] + ($redirect ? ['url' => $redirect] : [])) ?>"><?= $provider::getName() ?? '--' ?></a>
                            </li>
                        <? endforeach; ?>
                    </ul>

                    <? $provider = $action ? ($config['login_providers'][$action] ?? null) : null; ?>
                    <? $provider = (@class_exists($provider) && is_subclass_of($provider, Abstract_LoginProvider::class)) ? $provider : null; ?>

                    <? if ($provider): ?>
                        <? if ($redirect) {
                            $_SESSION['redirect-after-auth'] = $redirect;
                        } ?>
                        <div class="login-provider-form">
                            <div class="login-provider-head">
                                <h2><?= $provider::getName() ?></h2>
                            </div>
                            <div class="login-provider-body">
                                <? include 'bits/login-forms/' . $provider::getFormFileName() ?>
                            </div>
                        </div>
                    <? endif; ?>

                <? else: ?>
                    <div class="breadcrumbs">
                        <h2 class="account-name">
                            Hello, <?= $account['name'] ?? '(nobody)' ?>!
                        </h2>
                        <ul class="account-actions_data">
                            <li><a href="/cabinet.php" class="hbtn hb-fill-top-rev">Cabinet</a></li>
                            <li><a class="hbtn hb-fill-top-rev" href="/auth.php?action=logout">Logout</a></li>
                        </ul>
                    </div>

                    <? if (!empty($account['allowed_sites'])): ?>
                    <div class="account-actions_sites">
                        <h3 class="account-actions_sites_title">Available sites for you:</h3>
                        <ul class="account-actions_sites_link">
                            <? foreach ($account['allowed_sites'] as $site): ?>
                                <li class="account-actions_sites_link-data"><a href="//<?= $site ?>/" class="hbtn hb-fill-top-rev"><?= $site ?></a></li>
                            <? endforeach; ?>
                        </ul>
                     </div>
                    <? endif; ?>

                <? endif; ?>
                <? include 'bits/footer.php' ?>
            </div>
        </div>
    </div>
</div>
</body>
<script src="js/main.js"></script>
</html>
