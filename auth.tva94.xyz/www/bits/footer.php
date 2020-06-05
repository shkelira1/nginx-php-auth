<?php if (empty($config)) die(); // Disable direct open
$copyrightStartYear = 2020;
if (!empty($config['pages']['show_footer'])):
?>
<footer>
Powered by nginx-php-auth <br />
&copy; tva94, <?= date('Y') > $copyrightStartYear ? "{$copyrightStartYear}&ndash;" : '' ?><?= date('Y') ?>
</footer>
<?php endif; ?>

