CREATE TABLE `products_x_tags` (
    `tid` BIGINT UNSIGNED NOT NULL,
    `pid` BIGINT UNSIGNED NOT NULL,
    CONSTRAINT `fk_pxt_tag` FOREIGN KEY (`tid`) REFERENCES `tags`(`id`),
    CONSTRAINT `fk_pxt_product` FOREIGN KEY (`pid`) REFERENCES `products`(`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;