CREATE TABLE IF NOT EXISTS `PREFIX_sellermania_order` (
  `id_sellermania_order` int(11) NOT NULL AUTO_INCREMENT,
  `marketplace` varchar(128) NOT NULL,
  `ref_order` varchar(128) NOT NULL,
  `id_order` int(11) NOT NULL,
  `info` text NOT NULL,
  `date_paiement` datetime NOT NULL,
  `date_import` datetime NOT NULL,
  PRIMARY KEY (`id_sellermania_order`),
  KEY `marketplace` (`marketplace`),
  KEY `marketplace` (`ref_order`),
  KEY `marketplace` (`id_order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;