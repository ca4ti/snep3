--
-- Table structure for table `core_config`
--
CREATE TABLE IF NOT EXISTS core_config (
    `config_name` VARCHAR (255) NOT NULL,
    `config_value` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
