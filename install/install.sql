CREATE TABLE IF NOT EXISTS `PREFIX_sellermania_order` (
  `id_sellermania_order` int(11) NOT NULL AUTO_INCREMENT,
  `ref_order_sm` varchar(128) NOT NULL,
  `id_order` int(11) NOT NULL,
  `date_import` datetime NOT NULL,
  PRIMARY KEY (`id_sellermania_order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;