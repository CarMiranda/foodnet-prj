CREATE TABLE `posts_x_tags` (
    `tag_id` BIGINT UNSIGNED NOT NULL,
    `post_id` BIGINT UNSIGNED NOT NULL,
    CONSTRAINT `fk_pxt_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`),
    CONSTRAINT `fk_pxt_post` FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`),
    PRIMARY KEY (`tag_id`, `post_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;