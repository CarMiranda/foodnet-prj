CREATE TABLE `users_x_groups` (
    `uid` INT(11) UNSIGNED NOT NULL,
    `gid` INT(11) UNSIGNED NOT NULL,
    `admin` TINYINT(1) UNSIGNED DEFAULT 0,
    PRIMARY KEY (`uid`, `gid`),
    CONSTRAINT `fk_uxg_user` FOREIGN KEY (`uid`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_uxg_group` FOREIGN KEY (`gid`) REFERENCES `groups`(`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;