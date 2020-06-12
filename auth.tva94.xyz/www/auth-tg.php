<?php
require '../../include.php';

$bot_username = $config['telegram']['bot_username'];

if (!empty($_GET['hash'])) {
    AccountsProvider::loginAndRedirect(new LoginTelegramProvider());
}
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login with telegram</title>
</head>
<body>
<script async src="https://telegram.org/js/telegram-widget.js?7"
        data-telegram-login="<?= $bot_username ?>"
        data-size="large" data-auth-url="/auth-tg.php"
        data-request-access="write"></script>
<br />
<a href="/">&larr; Back to login page</a>
<? include 'bits/footer.php' ?>
</body>
</html>
