CREATE TABLE comments (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `post_id` BIGINT UNSIGNED NOT NULL,
    `comment` TINYTEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT `fk_com_uid` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_com_pid` FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;