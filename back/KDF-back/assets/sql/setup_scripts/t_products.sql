CREATE TABLE `products` (
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

-- Perform a geolocation search for a radius r
--DELIMITER |
--CREATE PROCEDURE geodist(IN ulon DECIMAL(11,8), IN ulat DECIMAL(10,8), IN rad DECIMAL(11,8))
--BEGIN
--    DECLARE maxlon1 DECIMAL(11,8); DECLARE maxlat1 DECIMAL(10,8);
--    DECLARE maxlon2 DECIMAL(11,8); DECLARE maxlat2 DECIMAL(10,8);
--    SET maxlon1 = ulon - r / ABS(COS(RADIANS(ulat)) * 69); SET maxlat1 = ulat - dist / 69;
--    SET maxlon2 = ulon + r / ABS(COS(RADIANS(ulat)) * 69); SET maxlat2 = ulat + dist / 69;
--    SELECT *, 12733.1 * ASIN(SQRT(POWER(SIN(RADIANS(ulat - `products`.`Lat`) / 2), 2) + COS(RADIANS(ulat)) * COS(RADIANS(`Products`.`Lat`)) * POWER(SIN(RADIANS(ulon - `Products`.`Lon`) / 2), 2))) AS `distance`
--    FROM `Products`
--    WHERE (`Lat` BETWEEN maxlat1 AND maxlat2) AND (`Lon` BETWEEN maxlon1 AND maxlon2)
--    HAVING distance < rad
--    ORDER BY distance
--    LIMIT 30;
--END |
--DELIMITER ;
-- Send query with PHP: "CALL geodist($ulon, $ulat, $rad);"