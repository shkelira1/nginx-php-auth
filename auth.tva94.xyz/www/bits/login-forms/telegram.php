<?php if (empty($config)) die(); // Disable direct open
$bot_username = $config['telegram']['bot_username']; ?>
<?php if (false) : ?>
<form action="/auth.php" method="POST" class="login-provider-form__inner">
    <input type="hidden" name="action" value="login-telegram">
    <input type="hidden" name="redirect" value="<?= $redirect ?>">

    <button type="submit" class="hbtn hb-fill-top-rev">LOGIN</button>
</form>
<?php else: ?>
<script async src="https://telegram.org/js/telegram-widget.js?7"
        data-telegram-login="<?= $bot_username ?>"
        data-size="large" data-auth-url="/auth-tg.php"
        data-request-access="write"></script>
<?php endif; ?>

