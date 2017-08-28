CREATE TABLE group_replies {
    `user_id` INT(11) UNSIGNED NOT NULL,
    `group_id` INT(11) UNSIGNED NOT NULL,
    `body` TEXT,
    `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `group_id`, `timestamp`),
    CONSTRAINT `fk_cht_uid` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_cht_gid` FOREIGN KEY (`gorup_id`) REFERENCES `groups`(`id`)
} ENGINE = InnoDB DEFAULT CHARSET = utf8;