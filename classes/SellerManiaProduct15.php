<?php
/*
* 2010 - 2014 Sellermania / Froggy Commerce / 23Prod SARL
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to team@froggy-commerce.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade your module to newer
* versions in the future.
*
*  @author Fabien Serny - Froggy Commerce <team@froggy-commerce.com>
*  @copyright	2010-2014 Sellermania / Froggy Commerce / 23Prod SARL
*  @version		1.0
*  @license		http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

// Security
if (!defined('_PS_VERSION_'))
	exit;

class SellerManiaProduct
{
	/**
	 * Make products MySQL request
	 * @param integer $id_lang
	 * @param integer $start
	 * @param integer $limit
	 * @return mysql ressource
	 */
	public static function getProductsRequest($id_lang, $start = 0, $limit = 0)
	{
		// Retrieve context
		$context = Context::getContext();

		// Init
		$limitSQL = '';
		if ((int)$start > 0 && (int)$limit > 0)
			$limitSQL = ' LIMIT '.(int)$start.','.(int)$limit;

		// SQL request
		$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_shop.id_product_attribute) id_product_attribute,
					   product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
					   pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image,
					   il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default, product_shop.price AS orderprice
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)
				'.Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
				AND product_shop.`active` = 1 AND product_shop.`visibility` IN ("both", "catalog")
				GROUP BY product_shop.id_product '.$limitSQL;

		// Return query
		return Db::getInstance()->query($sql);
	}

	/**
	 * Get product declinations
	 * @param integer $id_product
	 * @param integer $id_lang
	 * @return boolean|array
	 */
	public static function getProductDeclinations($id_product, $id_lang)
	{
		if (!Combination::isFeatureActive())
			return false;

		$sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name,
					a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, pa.`id_product_attribute`,
					IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`price`, product_attribute_shop.`ecotax`, pa.`weight`,
					product_attribute_shop.`default_on`, pa.`reference`, pa.`ean13`, product_attribute_shop.`unit_price_impact`,
					pa.`minimal_quantity`, pa.`available_date`, ag.`group_type`
				FROM `'._DB_PREFIX_.'product_attribute` pa
				'.Shop::addSqlAssociation('product_attribute', 'pa').'
				'.Product::sqlStock('pa', 'pa').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group`
				'.Shop::addSqlAssociation('attribute', 'a').'
				WHERE pa.`id_product` = '.(int)$id_product.'
					AND al.`id_lang` = '.(int)$id_lang.'
					AND agl.`id_lang` = '.(int)$id_lang.'
				GROUP BY id_attribute_group, id_product_attribute
				ORDER BY ag.`position` ASC, a.`position` ASC, agl.`name` ASC';
		$attributes_groups = Db::getInstance()->executeS($sql);

		$combinations = false;
		if (is_array($attributes_groups) && $attributes_groups)
		{
			// Retrieve context
			$context = Context::getContext();
			if (!isset($context->link))
				$context->link = new Link();

			// Retrieve images corresponding to each declination
			$ids = array();
			$images = array();
			foreach ($attributes_groups as $pa)
				$ids[] = (int)$pa['id_product_attribute'];
			$result = Db::getInstance()->executeS('
			SELECT pai.`id_image`, pai.`id_product_attribute`
			FROM `'._DB_PREFIX_.'product_attribute_image` pai
			LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_image` = pai.`id_image`)
			WHERE pai.`id_product_attribute` IN ('.implode(', ', $ids).') ORDER by i.`position`');
			foreach ($result as $row)
				if ($row['id_image'] > 0)
					$images[$row['id_product_attribute']][] = $context->link->getImageLink('product', $row['id_image'], 'large_default');

			// Retrieve infos for each declination
			foreach ($attributes_groups as $k => $row)
			{
				$combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
				$combinations[$row['id_product_attribute']]['price'] = (float)$row['price'];
				$combinations[$row['id_product_attribute']]['ecotax'] = (float)$row['ecotax'];
				$combinations[$row['id_product_attribute']]['weight'] = (float)$row['weight'];
				$combinations[$row['id_product_attribute']]['quantity'] = (int)$row['quantity'];
				$combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
				$combinations[$row['id_product_attribute']]['ean13'] = $row['ean13'];
				$combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
				if (isset($images[$row['id_product_attribute']]))
					$combinations[$row['id_product_attribute']]['images'] = $images[$row['id_product_attribute']];
			}
		}
		return $combinations;
	}

	/**
	 * Get product tags
	 * @param integer $id_product
	 * @param integer $id_lang
	 * @return string
	 */
	public static function getProductTags($id_product, $id_lang)
	{
		$sql = 'SELECT t.`name` FROM `'._DB_PREFIX_.'product_tag` pt
				LEFT JOIN `'._DB_PREFIX_.'tag` t ON (t.`id_tag` = pt.`id_tag` AND t.`id_lang` = '.(int)$id_lang.')
				WHERE pt.`id_product` = '.(int)$id_product;
		$tags_list = Db::getInstance()->executeS($sql);

		$tags = array();
		$tags_tmp = array();
		foreach ($tags_list as $t)
		{
			if (count($tags_tmp) == 5)
			{
				$tags[] = implode(',', $tags_tmp);
				$tags_tmp = array();
			}
			if (!empty($t['name']))
				$tags_tmp[] = $t['name'];
		}
		$tags[] = implode(',', $tags_tmp);

		return $tags;
	}

	/**
	 * Get images
	 * @param integer $id_product
	 * @return array
	 */
	public static function getImages($id_product)
	{
		// Retrieve context
		$context = Context::getContext();

		// Retrieves images
		$images = array();
		$sql = 'SELECT image_shop.`cover`, i.`id_image`, i.`position`
				FROM `'._DB_PREFIX_.'image` i
				'.Shop::addSqlAssociation('image', 'i').'
				WHERE i.`id_product` = '.(int)$id_product.'
				ORDER BY `position`';
		$result = Db::getInstance()->executeS($sql);

		foreach ($result as $row)
			$images[] = $context->link->getImageLink('product', $row['id_image'], 'large_default');

		return $images;
	}
}

