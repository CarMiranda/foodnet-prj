CREATE TABLE `favorites` (
    `uid` INT(11) UNSIGNED NOT NULL,
    `tid` BIGINT UNSIGNED NOT NULL,
    CONSTRAINT `fk_fav_user` FOREIGN KEY (`uid`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_fav_tag` FOREIGN KEY (`tid`) REFERENCES `tags`(`id`),
    PRIMARY KEY (`uid`, `tid`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;