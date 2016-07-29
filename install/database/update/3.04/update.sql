--
-- Table structure for table `users_queues_permissions`
--
CREATE TABLE IF NOT EXISTS `users_queues_permissions` (
  `id` integer NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `queue_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  CONSTRAINT `fk_id_user_p` FOREIGN KEY (`user_id` ) REFERENCES `users` (`id`),
  CONSTRAINT `fk_id_queue_p` FOREIGN KEY (`queue_id` ) REFERENCES `queues` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;