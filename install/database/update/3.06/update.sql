INSERT INTO `core_config` (`config_module`, `config_name`, `config_value`) VALUES ('default','host_inspect','http://api.opens.com.br:3003');
UPDATE `core_config` SET `config_value`='http://api.opens.com.br:3003/v2/notifications' WHERE `config_name`='host_notification' AND `config_module`='default';
ALTER TABLE `regras_negocio` ADD COLUMN `dates_alias` VARCHAR(20);
--
-- Table structure for table `date_alias`
--

CREATE TABLE IF NOT EXISTS `date_alias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Table structure for table `date_alias_list`
--
CREATE TABLE IF NOT EXISTS `date_alias_list` (
  `dateid` int(11) NOT NULL,
  `date` varchar(10) DEFAULT NULL,
  `timerange` varchar(11) DEFAULT '00:00-23:59',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
ALTER TABLE `peers` CHANGE `call-limit` `call-limit` VARCHAR(4) NULL DEFAULT '1';
ALTER TABLE `trunks` ADD `time_initial_date` INT NULL AFTER `time_chargeby`;
