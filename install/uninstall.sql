DROP TABLE `PREFIX_sellermania_order`;
DROP TABLE `PREFIX_sellermania_marketplace`;
DROP TABLE `PREFIX_sellermania_field_error`;

DELETE 
	FROM 
		`PREFIX_configuration` 
	WHERE 
		`name` LIKE 'PS_OS_SM_%' 
		OR 
		`name` LIKE 'SM_%'
;
