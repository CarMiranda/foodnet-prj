CREATE TABLE `favorites` (
    `user_id` INT(11) UNSIGNED NOT NULL,
    `tag_id` BIGINT UNSIGNED NOT NULL,
    CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_fav_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`),
    PRIMARY KEY (`user_id`, `tag_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;