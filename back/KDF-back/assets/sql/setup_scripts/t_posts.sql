CREATE TABLE `posts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `owner` INT(11) UNSIGNED NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    `description` TINYTEXT,
    `createdat` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expdate` DATE NOT NULL,
    `autoremovedate` DATE NOT NULL, -- Set trigger
    `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `image` VARCHAR(255) NOT NULL, -- Set trigger to check Pattern: CONCAT(`Owner`, "_", NOW(), ".jpg")
    `inactivesince` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_p_user` FOREIGN KEY (`owner`) REFERENCES `users`(`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- Setting trigger for AutoRemoveDate if no date was specified
-- Auto remove will happen a week after expiry date.
--DELIMITER |
--CREATE TRIGGER before_insert_products BEFORE INSERT ON `Products` FOR EACH ROW
--IF NEW.AutoRemoveDate IS NULL THEN
--    SET NEW.AutoRemoveDate := DATE_ADD(NEW.ExpiryDate, INTERVAL 1 WEEK);
--END IF |
--DELIMITER ;