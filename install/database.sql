CREATE TABLE `iko_configs` (
	`config_name`     VARCHAR(255) NOT NULL,
	`config_value`    VARCHAR(255) NOT NULL,
	`config_comment`  TEXT,
	`module_name` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`config_name`),
	UNIQUE KEY `config_module_relation` (`config_name`, `module_name`)
);

CREATE TABLE `iko_modules` (
	`module_author`      VARCHAR(255) NOT NULL,
	`module_name`        VARCHAR(255) NOT NULL,
	`module_displayname` VARCHAR(255) NOT NULL,
	`module_version`     VARCHAR(20)  NOT NULL,
	`module_status`      TINYINT(1)   NOT NULL DEFAULT '1',
	PRIMARY KEY (`module_name`)
);


CREATE TABLE `iko_templates` (
	`template_id`                    INT(11)      NOT NULL AUTO_INCREMENT,
	`template_name`                  VARCHAR(255) NOT NULL,
	`template_author`                VARCHAR(255) NOT NULL,
	`template_version`               VARCHAR(20)           DEFAULT '1',
	`template_directory`             VARCHAR(255) NOT NULL,
	`template_required_core_version` VARCHAR(20)  NOT NULL,
	KEY (`template_id`),
	UNIQUE KEY `template_directory` (`template_directory`),
	UNIQUE KEY `template_auth_name` (`template_name`, `template_author`)
);


CREATE TABLE `iko_users` (
	`user_id`                 INT(11)      NOT NULL AUTO_INCREMENT,
	`user_name`               VARCHAR(30)  NOT NULL,
	`user_password`           TEXT         NOT NULL,
	`user_email`              VARCHAR(255) NOT NULL,
	`user_avatar_id`          INT(11)               DEFAULT '1',
	`user_signature`   VARCHAR(255)          DEFAULT NULL,
	`user_about_user`  TEXT,
	`user_location_id` INT(11)               DEFAULT NULL,
	`user_gender`      TINYINT(1)            DEFAULT NULL,
	`user_date_joined` INT(11)      NOT NULL,
	`user_birthday`    DATE                  DEFAULT NULL,
	`user_timezone_id` INT(11)               DEFAULT '1',
	`user_last_login`  INT(11),
	`user_language`    varchar(255) NOT NULL Default 'english',
	`user_template`    INT(11)      NOT NULL DEFAULT '0',
	PRIMARY KEY (`user_id`),
	UNIQUE KEY `user_name` (`user_name`),
	UNIQUE KEY `user_email` (`user_email`),
	KEY `user_avatar_id` (`user_avatar_id`),
	KEY `user_location_id` (`user_location_id`),
	KEY `user_timezone_id` (`user_timezone_id`),
	KEY `user_template` (`user_template`)
);


CREATE TABLE `iko_user_assignment` (
	`user_id`      INT(11) NOT NULL,
	`usergroup_id` INT(11) NOT NULL,
	UNIQUE KEY `user_group_relation` (`user_id`, `usergroup_id`),
	KEY `group_id` (`usergroup_id`),
	KEY `user_id` (`user_id`)
);


CREATE TABLE `iko_group_assignment` (
	`parent_group_id` INT(11) NOT NULL,
	`child_group_id`  INT(11) NOT NULL,
	UNIQUE KEY `group_relation` (`parent_group_id`, `child_group_id`),
	KEY `iko_group_assignment` (`parent_group_id`),
	KEY `iko_group_child` (`child_group_id`)
);


CREATE TABLE `iko_usergroups` (
	`usergroup_id`    INT(11)      NOT NULL AUTO_INCREMENT,
	`usergroup_name`  VARCHAR(255) NOT NULL,
	`usergroup_style` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`usergroup_id`),
	UNIQUE KEY `iko_usergroups_usergroup_name_uindex` (`usergroup_name`)
);


CREATE TABLE `iko_permissions` (
	`permission_name` VARCHAR(255) NOT NULL,
	`module_name`     VARCHAR(255) NOT NULL,
	`comment`         TEXT         NOT NULL,
	PRIMARY KEY (`permission_name`),
	UNIQUE KEY `iko_permissions_permission_name_uindex` (`permission_name`),
	KEY `module_name` (`module_name`)
);

CREATE TABLE `iko_group_permissions` (
	`usergroup_id`    INT(11)      NOT NULL,
	`permission_name` VARCHAR(255) NOT NULL,
	UNIQUE KEY `group_permission_relation` (`usergroup_id`, `permission_name`),
	KEY `group_permission_name` (`permission_name`),
	KEY `iko_group_permission_id_group` (`usergroup_id`)
);


CREATE TABLE `iko_user_permissions` (
	`user_id`         INT(11)      NOT NULL,
	`permission_name` VARCHAR(255) NOT NULL,
	UNIQUE KEY `group_permission_relation` (`permission_name`, `user_id`),
	KEY `iko_group_permission_name_permission` (`permission_name`),
	KEY `iko_group_permission_id_user` (`user_id`)
);


CREATE TABLE `iko_translation` (
  `translation_key`     varchar(255)   NOT NULL  ,
  `german`              text      NOT NULL,
  `english`             text      NOT NULL,
  PRIMARY KEY (`translation_key`)
);

CREATE TABLE `iko_log` (
	`log_id`      INT(11)      NOT NULL        AUTO_INCREMENT,
	`module_name` VARCHAR(255) NOT NULL,
	`log_type`    INT(11)      NOT NULL,
	`log_code`    TEXT         NOT NULL,
	`log_message` TEXT         NOT NULL,
	`log_time`    INT(11)      NOT NULL,
	`log_extra`   TEXT,
	PRIMARY KEY (`log_id`),
	KEY (`module_name`)
);

CREATE TABLE `iko_cms` (
	`cms_id`      INT(11)      NOT NULL        AUTO_INCREMENT,
	`cms_content` TEXT         NOT NULL,
	`user_id`     INT(11)      NOT NULL,
	`cms_time`    TIMESTAMP    NOT NULL,
	`cms_title`   VARCHAR(255) NOT NULL,
	PRIMARY KEY (`cms_id`),
	KEY (`user_id`)
);



/*Relation between Tables */

ALTER TABLE iko_user_assignment
	ADD CONSTRAINT `iko_users_ibfk_1`
FOREIGN KEY (`user_id`)
REFERENCES `iko_users` (`user_id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE iko_user_assignment
	ADD CONSTRAINT `iko_user_assignment_ibfk_1`
FOREIGN KEY (`usergroup_id`)
REFERENCES `iko_usergroups` (`usergroup_id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE iko_group_permissions
	ADD CONSTRAINT `iko_group_permissions_ibfk_1`
FOREIGN KEY (`usergroup_id`)
REFERENCES `iko_usergroups` (`usergroup_id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE iko_group_permissions
	ADD CONSTRAINT `iko_group_permissions_ibfk_2`
FOREIGN KEY (`permission_name`)
REFERENCES `iko_permissions` (`permission_name`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE iko_permissions
	ADD CONSTRAINT `iko_permissions_ibfk_2`
FOREIGN KEY (`module_name`)
REFERENCES `iko_modules` (`module_name`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE iko_user_permissions
	ADD CONSTRAINT `iko_user_permissions_ibfk_1`
FOREIGN KEY (`user_id`)
REFERENCES `iko_users` (`user_id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE iko_user_permissions
	ADD CONSTRAINT `iko_user_permissions_ibfk_2`
FOREIGN KEY (`permission_name`)
REFERENCES `iko_permissions` (`permission_name`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE iko_group_assignment
	ADD CONSTRAINT `iko_group_assignment_ibfk_1`
FOREIGN KEY (`parent_group_id`)
REFERENCES `iko_usergroups` (`usergroup_id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE iko_group_assignment
	ADD CONSTRAINT `iko_group_assignment_ibfk_2`
FOREIGN KEY (`child_group_id`)
REFERENCES `iko_usergroups` (`usergroup_id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;


ALTER TABLE iko_configs
	ADD CONSTRAINT `iko_configs_ibfk1`
FOREIGN KEY (`module_name`)
REFERENCES `iko_modules` (`module_name`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE iko_users
	ADD CONSTRAINT `iko_user_template_ibfk_2`
FOREIGN KEY (`user_template`)
REFERENCES `iko_templates` (`template_id`);

ALTER TABLE iko_log
	ADD CONSTRAINT `iko_log_ibfk_1`
FOREIGN KEY (`module_name`)
REFERENCES `iko_modules` (`module_name`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE iko_cms
	ADD CONSTRAINT `iko_users_cms_ibfk_1`
FOREIGN KEY (`user_id`)
REFERENCES `iko_users` (`user_id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;