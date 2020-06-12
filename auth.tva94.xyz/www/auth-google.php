<?php
require '../../include.php';

AccountsProvider::loginAndRedirect(new LoginGoogleProvider());
