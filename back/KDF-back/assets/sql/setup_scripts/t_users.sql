CREATE TABLE `users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `uname` VARCHAR(15) NOT NULL UNIQUE,
    `mail` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(60) NOT NULL,
    `reg_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0, -- 0 INACTIVE, 1 ACTIVE, 2 BANNED
    `fname` VARCHAR(50) NOT NULL,
    `lname` VARCHAR(50) NOT NULL,
    `country` VARCHAR(255),
    `address` VARCHAR(255),
    `postal_code` INT(5) NOT NULL,
    `phone` VARCHAR(255),
    -- Store Longitude and Latitude for geolocation search
    `lon` DECIMAL(11,8) NOT NULL, -- Ranges from -180 to +180
    `lat` DECIMAL(10,8) NOT NULL, -- Ranges from -90 to +90
    `gender` TINYINT(1) UNSIGNED DEFAULT 0,
    `dob` DATE,
    `avatar` VARCHAR(255), -- Set trigger
    `lang` VARCHAR(2) DEFAULT 'fr',
    `last_seen` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Update when the user uses the app
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- Setting trigger for avatar if no pic was specified
-- An avatar image will be associated automatically.
--DELIMITER |
--CREATE TRIGGER before_insert_user BEFORE INSERT ON `user` FOR EACH ROW
--IF NEW.AvatarSrc IS NULL THEN
--    SET NEW.AvatarSrc := ""; -- Image source path
--END IF |
--DELIMITER ;

-- Setting trigger for lastseen on update
-- Lastseen will update to CURRENT_TIMESTAMP whenever a user performs an action
--DELIMITER |
--CREATE TRIGGER after_update_user AFTER UPDATE ON `user` FOR EACH ROW
--SET NEW.