CREATE TABLE likes (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `post_id` BIGINT UNSIGNED NOT NULL,
    `like` TINYINT UNSIGNED, -- [0..5]
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `post_id`),
    UNIQUE (id),
    CONSTRAINT `fk_lik_uid` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_lik_pid` FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;