CREATE TABLE `friends` (
    `user_id` INT(11) UNSIGNED NOT NULL,
    `friend_id` INT(11) UNSIGNED NOT NULL,
    `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT `fk_fl_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_fl_friend` FOREIGN KEY (`friend_id`) REFERENCES `users`(`id`),
    PRIMARY KEY (`user_id`, `friend_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
-- There are two rows per friendship relation. status is 0 if user_id has not answered the request, 1 if user_id sent the request, 2 if both accepted the request