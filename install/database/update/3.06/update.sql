INSERT INTO `core_config` (`config_module`, `config_name`, `config_value`) VALUES ('default','host_inspect','http://api.opens.com.br:8080');
ALTER TABLE `peers` CHANGE `call-limit` `call-limit` VARCHAR(4) NULL DEFAULT '1';
ALTER TABLE `trunks` ADD `time_initial_date` INT NULL AFTER `time_chargeby`;
