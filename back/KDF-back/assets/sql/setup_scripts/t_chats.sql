CREATE TABLE chats {
    `chat_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id1` INT(11) UNSIGNED NOT NULL,
    `user_id2` INT(11) UNSIGNED NOT NULL,
    `last_active` TIMESTAMP,
    PRIMARY KEY (`chat_id`, `user_id1`, `user_id2`),
    CONSTRAINT `fk_cht_uid1` FOREIGN KEY (`user_id1`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_cht_uid2` FOREIGN KEY (`user_id2`) REFERENCES `users`(`id`)
} ENGINE = InnoDB DEFAULT CHARSET = utf8;