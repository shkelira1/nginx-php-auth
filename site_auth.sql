CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `allowed_sites` (
  `account_id` int(11) NOT NULL,
  `site` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `logins` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `login` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `data` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `allowed_sites`
  ADD PRIMARY KEY (`account_id`,`site`);

ALTER TABLE `logins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provider-login` (`provider`,`login`) USING BTREE,
  ADD KEY `account_id` (`account_id`);

ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `allowed_sites`
  ADD CONSTRAINT `allowed_sites_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `logins`
  ADD CONSTRAINT `logins_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `logins`
    ADD COLUMN `active` int(11) NOT NULL DEFAULT '1' AFTER `login`,
    ADD INDEX `active` (`active`);

