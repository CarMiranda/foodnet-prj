CREATE TABLE `friends` (
    `uid` INT(11) UNSIGNED NOT NULL,
    `fid` INT(11) UNSIGNED NOT NULL,
    `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0, -- 0 SENT, 1 ACCEPTED, 2 DELETED
    CONSTRAINT `fk_fl_user` FOREIGN KEY (`uid`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_fl_friend` FOREIGN KEY (`fid`) REFERENCES `users`(`id`),
    PRIMARY KEY (`uid`, `fid`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;