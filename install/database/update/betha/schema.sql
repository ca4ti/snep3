-- Update SNEP Alpha -> Betha e Asterisk 12 -> 13
ALTER TABLE  `peers` CHANGE  `ipaddr` `ipaddr` VARCHAR(45);
ALTER TABLE  `peers` ADD  `username` `defaultuser` VARCHAR(80);
-- ALTER TABLE  `peers` ADD  `useragent` VARCHAR(45);
ALTER TABLE  `peers` CHANGE  `regserver` `regserver` VARCHAR(20);
ALTER TABLE  `voicemail_users` CHANGE  `password`  `password` VARCHAR(10);
ALTER TABLE  `queue_members` CHANGE  `paused`  `paused` INT(2);
