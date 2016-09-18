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

INSERT INTO `iko_modules` VALUES ('IkoBB', 'template', 'Template Engine', '1.0.0a', '1');


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