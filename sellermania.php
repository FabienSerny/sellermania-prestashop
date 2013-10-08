<?php
/*
* 2010 - 2013 Sellermania / 23Prod SARL
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to fabien@23prod.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade your module to newer
* versions in the future.
*
*  @author Fabien Serny - 23Prod <fabien@23prod.com>
*  @copyright	2010-2013 23Prod SARL
*  @version		1.0
*  @license		http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_'))
	exit;

class SellerMania extends Module
{
	/**
	 * @var array fields to export
	 */
	private $fields_to_export = array(
		'id_product', 'ean13', 'upc', 'ecotax', 'quantity', 'price', 'wholesale_price', 'reference',
		'width', 'height', 'depth', 'weight', 'description', 'description_short', 'name', 'image', 'category_default',
		'manufacturer_name'
	);


	/**
	 * Module Constructor
	 */
	function __construct()
	{
		$this->name = 'sellermania';
		$this->tab = 'advertising_marketing';
		$this->author = '23Prod';
		$this->version = '1.0';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('SellerMania');
		$this->description = $this->l('Connect your PrestaShop with SellerMania webservices');
	}

	/**
	 * Install method
	 * @return boolean success
	 */
	public function install()
	{
		Configuration::updateValue('SELLERMANIA_KEY', md5(rand()._COOKIE_KEY_.date('YmdHis')));
		return parent::install();
	}

	/**
	 * Uninstall method
	 * @return boolean success
	 */
	public function uninstall()
	{
		Configuration::deleteByName('SELLERMANIA_KEY');
		return parent::uninstall();
	}

	/**
	 * Configuration method
	 * @return string html
	 */
	function getContent()
	{
		// Init vars
		$languages_list = Language::getLanguages();
		$shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
		$module_web_path = Tools::getHttpHost(true).$shop->physical_uri.'modules/'.$this->name.'/';
		$export_directory_writable = 0;
		if (is_writable(dirname(__FILE__).'/export'))
			$export_directory_writable = 1;
		$sellermania_key = Configuration::get('SELLERMANIA_KEY');

		// Check if file exists and retrieve the creation date
		$files_list = array();
		foreach ($languages_list as $language)
		{
			$iso_code = strtolower($language['iso_code']);
			$web_path_file = $module_web_path.'export/export-'.$iso_code.'-'.$sellermania_key.'.csv';
			$real_path_file = dirname(__FILE__).'/export/export-'.$iso_code.'-'.$sellermania_key.'.csv';
			$files_list[$iso_code]['file'] = $web_path_file;
			if (file_exists($real_path_file))
				$files_list[$iso_code]['generated'] = date("d/m/Y H:i:s", filectime($real_path_file));
		}

		// Assign to Smarty
		$this->context->smarty->assign('script_path', dirname(__FILE__));
		$this->context->smarty->assign('export_directory_writable', $export_directory_writable);
		$this->context->smarty->assign('module_web_path', $module_web_path);
		$this->context->smarty->assign('sellermania_key', $sellermania_key);
		$this->context->smarty->assign('files_list', $files_list);
		$this->context->smarty->assign('languages_list', $languages_list);
		$this->context->smarty->assign('sellermania_module_path', $this->_path);

		// Return display
		return $this->display(__FILE__, 'displayGetContent.tpl');
	}


	/**
	 * Delete old exported files
	 * @param string $iso_lang
	 */
	public function delete_export_files($iso_lang)
	{
		// Init
		$languages_list = Language::getLanguages();
		$sellermania_key = Configuration::get('SELLERMANIA_KEY');

		// Delete all export files or only export file of the selected language
		if (!empty($iso_lang))
			@unlink(dirname(__FILE__).'/export/export-'.$iso_lang.'-'.$sellermania_key.'.csv');
		else
			foreach ($languages_list as $language)
				@unlink(dirname(__FILE__).'/export/export-'.strtolower($language['iso_code']).'-'.$sellermania_key.'.csv');
	}

	/**
	 * Make products MySQL request
	 * @param integer $id_lang
	 * @param integer $start
	 * @param integer $limit
	 * @return mysql ressource
	 */
	public function getProductsRequest($id_lang, $start = 0, $limit = 0)
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
	 * Get product combinations
	 * @param integer $id_product
	 * @param integer $id_lang
	 * @return boolean|array
	 */
	public function getProductCombinations($id_product, $id_lang)
	{
		if (!Combination::isFeatureActive())
			return false;
		$sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name,
					a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, pa.`id_product_attribute`,
					IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`price`, product_attribute_shop.`ecotax`, pa.`weight`,
					product_attribute_shop.`default_on`, pa.`reference`, product_attribute_shop.`unit_price_impact`,
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
			foreach ($attributes_groups as $k => $row)
			{
				if (!isset($groups[$row['id_attribute_group']]))
					$groups[$row['id_attribute_group']] = array(
						'name' => $row['public_group_name'],
						'group_type' => $row['group_type'],
						'default' => -1,
					);
				$groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
				if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1)
					$groups[$row['id_attribute_group']]['default'] = (int)$row['id_attribute'];
				if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']]))
					$groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
				$groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int)$row['quantity'];
				$combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
				$combinations[$row['id_product_attribute']]['price'] = (float)$row['price'];
				$combinations[$row['id_product_attribute']]['ecotax'] = (float)$row['ecotax'];
				$combinations[$row['id_product_attribute']]['weight'] = (float)$row['weight'];
				$combinations[$row['id_product_attribute']]['quantity'] = (int)$row['quantity'];
				$combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
				$combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
			}

			// Wash attributes list (if some attributes are unavailables and if allowed to wash it)
			if (Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0)
			{
				foreach ($groups as &$group)
					foreach ($group['attributes_quantity'] as $key => &$quantity)
						if (!$quantity)
							unset($group['attributes'][$key]);
			}
		}
		return $combinations;
	}

	/**
	 * Export method
	 * @param string $output (display|file)
	 * @param string $iso_lang
	 * @param integer $start
	 * @param integer $limit
	 */
	public function export($output, $iso_lang = '', $start = 0, $limit = 0)
	{
		// If output is file, we delete old export files
		if ($output == 'file')
			$this->delete_export_files($iso_lang);

		// Init
		if (!empty($iso_lang))
			$languages_list = array(array('iso_code' => $iso_lang));
		else
			$languages_list = Language::getLanguages();

		// Get products list for each lang
		foreach ($languages_list as $language)
		{
			$iso_lang = strtolower($language['iso_code']);
			$id_lang = Language::getIdByIso($iso_lang);
			$this->renderExportHeader($iso_lang, $output);
			$result = $this->getProductsRequest($id_lang, $start, $limit);
			while ($row = Db::getInstance()->nextRow($result))
			{
				$row['combinations'] = $this->getProductCombinations($row['id_product'], $id_lang);
				$row['image'] = '';
				$this->renderExport($row, $iso_lang, $output);
			}
		}
	}

	/**
	 * Render export header
	 * @param string $iso_lang
	 * @param string $output (display|file)
	 */
	public function renderExportHeader($iso_lang, $output)
	{
		$line = '';
		foreach ($this->fields_to_export as $field)
			$line .= $field.';';
		$line .= "\n";
		$this->renderLine($line, $iso_lang, $output);
	}

	/**
	 * Render export
	 * @param array $row
	 * @param string $iso_lang
	 * @param string $output (display|file)
	 */
	public function renderExport($row, $iso_lang, $output)
	{
		$line = '';
		foreach ($this->fields_to_export as $field)
			$line .= str_replace(array("\r\n", "\n"), '', $row[$field]).';';
		$line .= "\n";
		$this->renderLine($line, $iso_lang, $output);
	}

	/**
	 * Render line
	 * @param string $line
	 * @param string $iso_lang
	 * @param string $output (display|file)
	 */
	public function renderLine($line, $iso_lang, $output)
	{
		if ($output == 'file')
		{
			$sellermania_key = Configuration::get('SELLERMANIA_KEY');
			$real_path_file = dirname(__FILE__).'/export/export-'.$iso_lang.'-'.$sellermania_key.'.csv';
			file_put_contents($real_path_file, $line, FILE_APPEND);
		}
		else
			echo $line;
	}
}

