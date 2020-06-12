<?= password_hash($_REQUEST['password'] ?? '', PASSWORD_ARGON2I); ?>

