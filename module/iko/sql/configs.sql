CREATE TABLE `{prefix}configs` (
    `config_name` VARCHAR(255) NOT NULL,
    `config_value` TEXT NULL,
    `config_comment` TEXT NULL,
    `config_category` VARCHAR(255) NULL,
    PRIMARY KEY (`config_name`),
    KEY `config_category` (`config_category` ASC)
);