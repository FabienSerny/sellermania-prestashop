CREATE TABLE IF NOT EXISTS `PREFIX_sellermania_order` (
  `id_sellermania_order` int(11) NOT NULL AUTO_INCREMENT,
  `marketplace` varchar(128) NOT NULL,
  `customer_name` varchar(256) NOT NULL,
  `ref_order` varchar(128) NOT NULL,
  `amount_total` varchar(16) NOT NULL,
  `order_imei` text NOT NULL,
  `info` text NOT NULL,
  `error` text NOT NULL,
  `id_order` int(11) NOT NULL,
  `id_employee_accepted` int NOT NULL,
  `date_payment` datetime NOT NULL,
  `date_accepted` datetime NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_sellermania_order`),
  KEY `marketplace` (`marketplace`),
  KEY `ref_order` (`ref_order`),
  KEY `id_order` (`id_order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_sellermania_marketplace` (
    `id_sellermania_marketplace` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `code` varchar(255) NOT NULL,
    `enabled` int(11) NOT NULL,
    `available` int(11) NOT NULL,
    PRIMARY KEY (`id_sellermania_marketplace`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_sellermania_field_error` (
    `id_sellermania_field_error` int(11) NOT NULL AUTO_INCREMENT,
    `field_name` varchar(255) DEFAULT NULL,
    `error_message` text,
    `section` varchar(255) DEFAULT NULL,
    `is_active` tinyint(4) NOT NULL,
    `date_add` datetime NOT NULL,
    PRIMARY KEY (`id_sellermania_field_error`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;