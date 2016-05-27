ALTER TABLE `peers` ADD COLUMN `useragent` VARCHAR(250) DEFAULT NULL;
ALTER TABLE `queues` ADD COLUMN `ringinuse` BOOL NOT NULL DEFAULT '1';

INSERT INTO `core_config` (`config_module`, `config_name`, `config_value`) VALUES ("default","userfield","TSAAMMDDHHiiSRDS"),("default","userfield_ud","");
