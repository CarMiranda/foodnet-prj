-- Perform a geolocation search for a radius r
DELIMITER |
CREATE PROCEDURE geodist(IN ulon DECIMAL(11,8), IN ulat DECIMAL(10,8), IN rad DECIMAL(11,8), IN lim INT)
BEGIN
    DECLARE maxlon1 DECIMAL(11,8); DECLARE maxlat1 DECIMAL(10,8);
    DECLARE maxlon2 DECIMAL(11,8); DECLARE maxlat2 DECIMAL(10,8);
    SET maxlon1 = ulon - r / ABS(COS(RADIANS(ulat)) * 69); SET maxlat1 = ulat - dist / 69;
    SET maxlon2 = ulon + r / ABS(COS(RADIANS(ulat)) * 69); SET maxlat2 = ulat + dist / 69;
    SELECT *, 12733.1 * ASIN(SQRT(POWER(SIN(RADIANS(ulat - `products`.`Lat`) / 2), 2) + COS(RADIANS(ulat)) * COS(RADIANS(`Products`.`Lat`)) * POWER(SIN(RADIANS(ulon - `Products`.`Lon`) / 2), 2))) AS `distance`
    FROM `Products`
    WHERE (`Lat` BETWEEN maxlat1 AND maxlat2) AND (`Lon` BETWEEN maxlon1 AND maxlon2)
    HAVING distance < rad
    ORDER BY distance
    LIMIT lim;
END |
DELIMITER ;
-- Send query with PHP: "CALL geodist($ulon, $ulat, $rad, $limit);"