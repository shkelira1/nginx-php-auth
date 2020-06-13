-- change site to auth-admin on your domain and allow LoginDemoProvider in config
-- After you login with Demo -- create youtr own admin and disable LoginDemoProvider
INSERT INTO `accounts` (`id`, `name`, `active`) VALUES (1, 'demo', '1');
INSERT INTO `logins` (`account_id`, `provider`, `login`, `data`, `active`) VALUES (1, 'LoginDemoProvider', '', '{}', 1);
INSERT INTO `allowed_sites` (`account_id`, `site`) VALUES (1, 'auth-admin.tva94.xyz');