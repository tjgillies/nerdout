INSERT INTO `settings` VALUES(NULL, 1, 'nerdout', 'enabled', 'TRUE');

CREATE TABLE  `checkins` (
`checkin_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`user_id` INT( 11 ) NOT NULL ,
`location_id` INT( 11 ) DEFAULT NULL ,
`nerdout_id` INT( 11 ) DEFAULT NULL ,
`source` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL ,
`geo_lat` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL ,
`geo_long` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL ,
`geo_accurracy` VARCHAR( 8 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL ,
`checkin_at` DATETIME NOT NULL
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(6) DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `address` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `district` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `locality` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `region` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;