-- Perform a geolocation search for a radius r
DELIMITER |
CREATE PROCEDURE geodist(IN ulon DECIMAL(11,8), IN ulat DECIMAL(10,8), IN rad DECIMAL(11,8), IN lim INT, IN off INT)
BEGIN
    DECLARE maxlon1 DECIMAL(11,8); DECLARE maxlat1 DECIMAL(10,8);
    DECLARE maxlon2 DECIMAL(11,8); DECLARE maxlat2 DECIMAL(10,8);
    SET maxlon1 = ulon - rad / ABS(COS(RADIANS(ulat)) * 69); SET maxlat1 = ulat - rad / 69;
    SET maxlon2 = ulon + rad / ABS(COS(RADIANS(ulat)) * 69); SET maxlat2 = ulat + rad / 69;
    SELECT `id`, `fname`, `lname`, `lat`, `lon`, 12733.1 * ASIN(SQRT(POWER(SIN(RADIANS(ulat - `users`.`lat`) / 2), 2) + COS(RADIANS(ulat)) * COS(RADIANS(`users`.`lat`)) * POWER(SIN(RADIANS(ulon - `users`.`lon`) / 2), 2))) AS `distance`
    FROM `users`
    WHERE (`users`.`lat` BETWEEN maxlat1 AND maxlat2) AND (`users`.`lon` BETWEEN maxlon1 AND maxlon2)
    HAVING distance < rad
    ORDER BY distance
    LIMIT lim
    OFFSET off;
END |
DELIMITER ;
-- Send query with PHP: "CALL geodist($ulon, $ulat, $rad, $limit);"