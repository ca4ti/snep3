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
-- Estrutura da tabela `permissions`
--
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
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
  `permission_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `peers_groups_peers_id_refs_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `peers_groups_peers_id_refs_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
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
  `code` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  `expiration` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_recovery_refs_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO profiles (name, created, updated) VALUES ('default',now(),now());
INSERT INTO users (name, password,email,profile_id, created, updated) VALUES ('admin','0192023a7bbd73250516f069df18b500','suporte@opens.com.br',1,now(),now());

