<?php if (empty($config)) die(); // Disable direct open ?>

<form action="/auth.php" method="POST" class="login-provider-form__inner">
    <input type="hidden" name="action" value="login-password">
    <input type="hidden" name="redirect" value="<?= $redirect ?>">

    <input type="text" name="login" placeholder="login">
    <input type="password" name="password" placeholder="password">
<? if (!empty($config['password']['require_captcha'])): ?>
    <? include 'add-recaptcha.php' ?>
<? endif; ?>
    <button type="submit" class="hbtn hb-fill-top-rev">LOGIN</button>
</form>
