<?php if (empty($config)) die(); // Disable direct open ?>

<form action="/auth.php" method="POST" class="login-provider-form__inner">
    <input type="hidden" name="action" value="login-by-ip">
    <input type="hidden" name="redirect" value="<?= $redirect ?>">

    <div class="login-provider-form__inner-ip">
        Your ip address is: <strong><?= $_SERVER['REMOTE_ADDR'] ?? '' ?></strong>
    </div>

    <button type="submit" class="hbtn hb-fill-top-rev">LOGIN</button>
</form>
