--
-- Estrutura da tabela `profiles`
--
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Estrutura da tabela `users`
--
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `profile_id` INT NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_id` (`profile_id`),
  CONSTRAINT `fk_user_profile` FOREIGN KEY (`profile_id` ) REFERENCES `profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Estrutura da tabela `profiles_permissions`
--
CREATE TABLE IF NOT EXISTS `profiles_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_id` varchar(256) NOT NULL,
  `profile_id` INT NOT NULL,
  `allow` tinyint(1) NOT NULL default '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_id` (`profile_id`),
  CONSTRAINT `fk_user_profiles` FOREIGN KEY (`profile_id` ) REFERENCES `profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Estrutura da tabela `users_permissions`
--
CREATE TABLE IF NOT EXISTS `users_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permission_id` varchar(256) NOT NULL,
  `allow` tinyint(1) NOT NULL default '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_user_users` FOREIGN KEY (`user_id` ) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Estrutura da tabela `binds`
--
CREATE TABLE IF NOT EXISTS `binds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `peer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `peer_id` (`peer_id`),
  CONSTRAINT `binds_refs_peer_id` FOREIGN KEY (`peer_id`) REFERENCES `peers` (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `binds_peer_refs_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Estrutura da tabela `password_recovery`
--
CREATE TABLE IF NOT EXISTS `password_recovery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `created` datetime NOT NULL,
  `expiration` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_recovery_refs_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


drop table queue_log;

--
-- Estrutura da tabela `queue_log`
--
CREATE TABLE IF NOT EXISTS `queue_log` (
  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  time char(26) default NULL,
  callid varchar(32) NOT NULL default '',
  queuename varchar(32) NOT NULL default '',
  agent varchar(32) NOT NULL default '',
  event varchar(32) NOT NULL default '',
  data1 varchar(100) NOT NULL default '',
  data2 varchar(100) NOT NULL default '',
  data3 varchar(100) NOT NULL default '',
  data4 varchar(100) NOT NULL default '',
  data5 varchar(100) NOT NULL default '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Estrutura da tabela `contacts_phone`
--
CREATE TABLE IF NOT EXISTS `contacts_phone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` char(11) NOT NULL,
  `phone` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`),
  CONSTRAINT `contacts_phone_refs_contact_id` FOREIGN KEY (`contact_id`) REFERENCES `contacts_names` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO profiles (name, created, updated) VALUES ('default',now(),now());
INSERT INTO users (name, password,email,profile_id, created, updated) VALUES ('admin','0192023a7bbd73250516f069df18b500','suporte@opens.com.br',1,now(),now());

