INSERT INTO `core_config` (`config_module`, `config_name`, `config_value`) VALUES ('default','host_inspect','http://api.opens.com.br:8080');
ALTER TABLE `peers` CHANGE `call-limit` `call-limit` VARCHAR(4) NULL DEFAULT '1';
