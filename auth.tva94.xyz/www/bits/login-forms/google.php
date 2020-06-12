<?php if (empty($config)) die(); // Disable direct open ?>

<form action="/auth.php" method="POST" class="login-provider-form__inner">
    <input type="hidden" name="action" value="login-google">
    <input type="hidden" name="redirect" value="<?= $redirect ?>">

    <button type="submit" class="hbtn hb-fill-top-rev">LOGIN</button>
</form>
