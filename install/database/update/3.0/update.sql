--
-- Table structure for table `core_config`
--
CREATE TABLE IF NOT EXISTS `core_config` (
	`id` INTEGER PRIMARY KEY AUTO_INCREMENT,
    `config_module` VARCHAR (255) NOT NULL,
    `config_name` VARCHAR (255) NOT NULL,
    `config_value` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


