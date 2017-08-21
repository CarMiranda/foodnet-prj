CREATE TABLE `users_x_groups` (
    `uid` INT(11) UNSIGNED NOT NULL,
    `gid` INT(11) UNSIGNED NOT NULL,
    `status` TINYINT(1) UNSIGNED DEFAULT 0, -- 0: In group, 1: Admin, 2: Removed
    `removed_by` INT(11) UNSIGNED,
    PRIMARY KEY (`uid`, `gid`),
    CONSTRAINT `fk_uxg_user` FOREIGN KEY (`uid`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_uxg_group` FOREIGN KEY (`gid`) REFERENCES `groups`(`id`),
    CONSTRAINT `fk_uxg_rem` FOREIGN KEY (`removed_by`) REFERENCES `users`(`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;