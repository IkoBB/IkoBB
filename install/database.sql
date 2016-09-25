CREATE TABLE `iko_configs` (
	`config_name`     VARCHAR(255) NOT NULL,
	`config_value`    VARCHAR(255) NOT NULL,
	`config_comment`  TEXT,
	`config_category` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`config_name`),
	KEY `config_category` (`config_category`)
)﻿;

INSERT INTO `iko_configs` VALUES ('site_name', 'Test Value', 'The name of the site', '1'),
	('site_template', 1, 'Insert the ID of the template which should be the default template.', '1'),
	('site_email', 'test@test.com', 'The contact email of the forum. Also used for sending emails.', '1'),
	('site_maintenance', 0, 'Indicates if the site is maintenance. 1 - Maintenance Mode on; 2 - Maintenance Mode off',
	 '1');


CREATE TABLE `iko_modules` (
	`module_author`      VARCHAR(255) NOT NULL,
	`module_name`        VARCHAR(255) NOT NULL,
	`module_displayname` VARCHAR(255) NOT NULL,
	`module_version`     VARCHAR(20)  NOT NULL,
	`module_status`      TINYINT(1)   NOT NULL DEFAULT '1',
	PRIMARY KEY (`module_name`)
);

INSERT INTO `iko_modules` VALUES ('IkoBB', 'template', 'Template Engine', '1.0.0a', '1'),
	('IkoBB', 'user', 'User Engine', '1.0.0a', '1'),
	('IkoBB', 'iko', 'Core Engine', '1.0.0a', '1'),
	('IkoBB', 'forum', 'Forum Engine', '1.0.0a', '1');


CREATE TABLE `iko_templates` (
	`template_id`                    INT(11)      NOT NULL AUTO_INCREMENT,
	`template_name`                  VARCHAR(255) NOT NULL,
	`template_author`                VARCHAR(255) NOT NULL,
	`template_version`               VARCHAR(20)           DEFAULT '1',
	`template_directory`             VARCHAR(255) NOT NULL,
	`template_required_core_version` VARCHAR(20)  NOT NULL,
	PRIMARY KEY (`template_id`),
	UNIQUE KEY `template_directory` (`template_directory`),
	UNIQUE KEY `template_auth_name` (`template_name`, `template_author`)
);

INSERT INTO `iko_templates` (`template_name`, `template_author`, `template_version`, `template_directory`, `template_required_core_version`)
VALUES ('Default Template', 'IkoBB', '0.0.1', 'default', '1.0.0a');

CREATE TABLE `iko_user` (
	`user_id`                 INT(11)      NOT NULL AUTO_INCREMENT,
	`user_name`               VARCHAR(30)  NOT NULL,
	`user_password`           TEXT         NOT NULL,
	`user_email`              VARCHAR(255) NOT NULL,
	`user_avatar_id`          INT(11)               DEFAULT '1',
	`user_signature`          VARCHAR(255)          DEFAULT NULL,
	`user_about_user`         TEXT,
	`user_location_id`        INT(11)               DEFAULT NULL,
	`user_gender`             TINYINT(1)            DEFAULT NULL,
	`user_date_joined`        INT(11)      NOT NULL,
	`user_birthday`           DATE                  DEFAULT NULL,
	`user_chosen_template_id` INT(11)               DEFAULT '1',
	`user_timezone_id`        INT(11)               DEFAULT '1',
	PRIMARY KEY (`user_id`),
	UNIQUE KEY `user_name` (`user_name`),
	UNIQUE KEY `user_email` (`user_email`),
	KEY `user_avatar_id` (`user_avatar_id`),
	KEY `user_location_id` (`user_location_id`),
	KEY `user_chosen_template_id` (`user_chosen_template_id`),
	KEY `user_timezone_id` (`user_timezone_id`)
);

CREATE TABLE `iko_user_assignment` (
  `user_assignment_id_user`   int(11)   NOT NULL,
  `user_assignment_id_group`  int(11)   NOT NULL,
  UNIQUE KEY `user_group_relation` (`user_assignment_id_user`,`user_assignment_id_group`),
  KEY `iko_user_assignment_id_igroup` (`user_assignment_id_group`),
  KEY `iko_user_assignment_id_user` (`user_assignment_id_user`)
  );


CREATE TABLE `iko_usergroups` (
  `usergroup_id`    int(11)       NOT NULL    AUTO_INCREMENT,
  `usergroup_name`  varchar(255)  NOT NULL,
  `usergroup_style` varchar(255)  NOT NULL,
  PRIMARY KEY (`usergroup_id`),
  UNIQUE KEY `iko_usergroups_usergroup_name_uindex` (`usergroup_name`)
);


CREATE TABLE `iko_permissions` (
  `permission_id`   int(11)       NOT NULL    AUTO_INCREMENT,
  `permission_name` varchar(255)  NOT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `iko_permissions_permission_name_uindex` (`permission_name`)
  );

