CREATE TABLE chats {
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uid1` INT(11) UNSIGNED NOT NULL,
    `uid2` INT(11) UNSIGNED NOT NULL,
    `last_active` TIMESTAMP,
    PRIMARY KEY (`chat_id`, `uid1`, `uid2`),
    CONSTRAINT `fk_c_uid1` FOREIGN KEY (`uid1`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_c_uid2` FOREIGN KEY (`uid2`) REFERENCES `users`(`id`)
} ENGINE = InnoDB DEFAULT CHARSET = utf8;