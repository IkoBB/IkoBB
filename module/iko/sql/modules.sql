CREATE TABLE `{prefix}modules` (
    `module_author` VARCHAR(255) NOT NULL,
    `module_name` VARCHAR(255) NOT NULL,
    `module_displayname` VARCHAR(255) NOT NULL,
    `module_version` VARCHAR(20) NOT NULL,
    `module_status` TINYINT(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`module_name`)
);