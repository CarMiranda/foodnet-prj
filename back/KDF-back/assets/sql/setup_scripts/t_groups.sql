    CREATE TABLE `groups` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `avatar` VARCHAR(255),
        `visibility` TINYINT(1) UNSIGNED DEFAULT 1,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8;