#attachment sql generated on: 2011-01-23 16:27:50 : 1295767670

DROP TABLE IF EXISTS `attachments`;


CREATE TABLE `attachments` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`width` int(11) DEFAULT NULL,
	`height` int(11) DEFAULT NULL,
	`size` int(11) DEFAULT NULL,
	`ext` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`basename` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	`created_by` int(11) DEFAULT NULL,
	`modified_by` int(11) DEFAULT NULL,
	`object` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`object_alias` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`object_id` int(11) DEFAULT NULL,
	`path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `uid` (`uid`))	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

