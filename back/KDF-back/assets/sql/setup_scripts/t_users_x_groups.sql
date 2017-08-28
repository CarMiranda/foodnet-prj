CREATE TABLE `users_x_groups` (
    `user_id` INT(11) UNSIGNED NOT NULL,
    `group_id` INT(11) UNSIGNED NOT NULL,
    `status` TINYINT(1) UNSIGNED DEFAULT 0, -- 0: In group, 1: Admin, 2: Removed
    `removed_by` INT(11) UNSIGNED,
    PRIMARY KEY (`user_id`, `group_id`),
    CONSTRAINT `fk_uxg_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_uxg_group` FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`),
    CONSTRAINT `fk_uxg_rem` FOREIGN KEY (`removed_by`) REFERENCES `users`(`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;