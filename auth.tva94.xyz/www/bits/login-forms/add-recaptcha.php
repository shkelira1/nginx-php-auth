<?php if (empty($config)) die(); // Disable direct open
$recaptcha_key = $config['google']['recaptcha_public']; ?>
<div class="google-recaptcha-wrapper">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <div class="g-recaptcha" data-sitekey="<?= $recaptcha_key ?>"></div>
</div>

