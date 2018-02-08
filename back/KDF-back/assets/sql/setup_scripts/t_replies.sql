CREATE TABLE `replies` (
   `chat_id` BIGINT UNSIGNED NOT NULL,
   `sender_id` INT(11) UNSIGNED NOT NULL,
   `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   `body` TEXT,
   `status` TINYINT(1) UNSIGNED DEFAULT 0, -- 0: Sent, 1: Received, 2: Read
   `deleted_by` TINYINT(1) UNSIGNED DEFAULT 0, -- 0: No one, 1: Sender, 2: Receiver, 3: Both
   PRIMARY KEY (`chat_id`, `sender_id`, `timestamp`),
   CONSTRAINT `fk_r_cid` FOREIGN KEY (`chat_id`) REFERENCES `chats`(`id`),
   CONSTRAINT `fk_r_sid` FOREIGN KEY (`sender_id`) REFERENCES `chats`(`user_id1`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;