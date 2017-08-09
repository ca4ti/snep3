CREATE TABLE IF NOT EXISTS `telcos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(35) NOT NULL,
  `mobile_price` varchar(10) NOT NULL,
  `landline_price` varchar(10) NOT NULL,
  `start_time` varchar(2) NOT NULL,
  `fract` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `billing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `area` varchar(10),
  `price` varchar(10) NOT NULL,
  `telco` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`telco`) REFERENCES telcos(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `billing_calls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `uniqueid` varchar(20) NOT NULL,
  `userfield` varchar(40),
  `price` varchar(10) NOT NULL DEFAULT '0,00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
