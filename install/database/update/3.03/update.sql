alter table `peers` add column `useragent` VARCHAR(250) default NULL
ALTER TABLE `queues` ADD COLUMN `ringinuse` BOOL NOT NULL DEFAULT '1';
