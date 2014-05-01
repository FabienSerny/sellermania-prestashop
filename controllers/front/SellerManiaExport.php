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

if (!defined('_PS_VERSION_'))
	exit;

// Define if not defined
if (!defined('_PS_BASE_URL_'))
	define('_PS_BASE_URL_', Tools::getShopDomain(true));

class SellerManiaExportController
{
	/**
	 * @var array fields to export
	 */
	private $fields_to_export = array(
		'id_product' => 'int', 'id_product_attribute' => 'int', 'id_unique' => 'string', 'ean13' => 'string', 'upc' => 'string', 'ecotax' => 'float',
		'quantity' => 'int', 'price' => 'float', 'wholesale_price' => 'float', 'reference' => 'string',
		'width' => 'float', 'height' => 'float', 'depth' => 'float', 'weight' => 'float',
		'name' => 'string', 'images' => 'string', 'category_default' => 'string',
		'description' => 'string', 'description_short' => 'string', 'manufacturer_name' => 'string',
		'meta_title' => 'string', 'meta_description' => 'string', 'meta_keywords' => 'string', 'product_url' => 'string',
	);

	/**
	 * @var array attribute groups
	 */
	private $attribute_groups = array();

	/**
	 * Controller constructor
	 */
	public function __construct()
	{
		$this->context = Context::getContext();

		$id_lang = Configuration::get('PS_LANG_DEFAULT');
		if (Tools::getValue('l') != '')
			$id_lang = Language::getIdByIso(Tools::getValue('l'));
		$tmp = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'attribute_group_lang` WHERE `id_lang` = '.(int)$id_lang);
		foreach ($tmp as $t)
			$this->attribute_groups[$t['id_attribute_group']] = $t['name'];
	}

	/**
	 * Get export filename
	 * @param string $iso_lang
	 * @return string export file name
	 */
	public function get_export_filename($iso_lang, $web_path = false)
	{
		$sellermania_key = Configuration::get('SELLERMANIA_KEY');
		if ($web_path)
			return 'export/export-'.strtolower($iso_lang).'-'.$sellermania_key.'.csv';
		return dirname(__FILE__).'/../../export/export-'.strtolower($iso_lang).'-'.$sellermania_key.'.csv';
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
			@unlink($this->get_export_filename($iso_lang));
		else
			foreach ($languages_list as $language)
				@unlink($this->get_export_filename($language['iso_code']));
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
			$result = SellerManiaProduct::getProductsRequest($id_lang, $start, $limit);
			while ($row = Db::getInstance()->nextRow($result))
			{
				$row['declinations'] = SellerManiaProduct::getProductDeclinations($row['id_product'], $id_lang);
				$row['images'] = SellerManiaProduct::getImages($row['id_product']);
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
		foreach ($this->fields_to_export as $field => $field_type)
			$line .= '"'.$field.'";';
		foreach ($this->attribute_groups as $id_attribute_group => $group_name)
			$line .= '"Attr '.$id_attribute_group.' - '.$group_name.'";';
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
		// If declination duplicate row for each declination
		if ($row['declinations'] && is_array($row['declinations']))
		{
			$rows = array();
			foreach ($row['declinations'] as $id_product_attribute => $declination)
			{
				$rowCopy = $row;
				$rowCopy['id_product_attribute'] = $id_product_attribute;
				$rowCopy['name'] = $rowCopy['name'].' '.implode(' ', $declination['attributes_values']);
				$rowCopy['price'] = Product::getPriceStatic($rowCopy['id_product'], true, $id_product_attribute, 2);
				$rowCopy['ecotax'] = $declination['ecotax'];
				$rowCopy['quantity'] = $declination['quantity'];
				$rowCopy['reference'] = (!empty($declination['reference']) ? $declination['reference'] : '');
				$rowCopy['ean13'] = (!empty($declination['ean13']) ? $declination['ean13'] : '');
				if (isset($declination['images']) && count($declination['images']) >= 1)
					$rowCopy['images'] = $declination['images'];
				$rowCopy['attributes_values'] = $declination['attributes_values'];
				$rows[] = $rowCopy;
			}
		}
		else
		{
			$row['price'] = Product::getPriceStatic($row['id_product'], true, null, 2);
			$rows = array($row);
		}

		// Begin rendering
		$line = '';
		foreach ($rows as $row)
			if ($row['id_product'] != Configuration::get('SM_DEFAULT_PRODUCT_ID') && $row['name'] != '')
			{
				$row['images'] = implode('|', $row['images']);
				foreach ($this->fields_to_export as $field => $field_type)
				{
					if ($field == 'id_unique')
						$row[$field] = $row['id_product'].'-'.$row['id_product_attribute'];
					else if ($field == 'product_url')
						$row[$field] = $this->context->link->getProductLink($row['id_product'], null, null, null, Language::getIdByIso($iso_lang));
					else if (!isset($row[$field]))
						$row[$field] = '';
					else if ($field_type == 'int')
						$row[$field] = (int)$row[$field];
					else if ($field_type == 'float')
						$row[$field] = number_format($row[$field], 2, '.', '');
					$line .= '"'.str_replace(array("\r\n", "\n", '"'), '', $row[$field]).'";';
				}
				foreach ($this->attribute_groups as $id_attribute_group => $group_name)
					$line .= '"'.(isset($row['attributes_values'][$id_attribute_group]) ? $row['attributes_values'][$id_attribute_group] : '').'";';
				$line .= "\n";
			}

		// Free memory
		$row = null;

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
			$real_path_file = $this->get_export_filename($iso_lang);
			file_put_contents($real_path_file, $line, FILE_APPEND);
		}
		else
			echo $line;
	}


	/**
	 * Run method
	 */
	public function run()
	{
		// Init
		global $argv;
		$argument_key = '';
		if (isset($argv[1]))
			$argument_key = $argv[1];

		// Set _PS_ADMIN_DIR_ define and set default Shop
		if (!defined('_PS_ADMIN_DIR_'))
			define('_PS_ADMIN_DIR_', getcwd());
		$this->context->shop->setContext(4);

		// Check if SellerMania key exists
		if (Configuration::get('SELLERMANIA_KEY') == '')
			die('ERROR1');
		if (Tools::getValue('k') == '' && $argument_key == '')
			die('ERROR2');
		if (Tools::getValue('k') == Configuration::get('SELLERMANIA_KEY') || $argument_key == Configuration::get('SELLERMANIA_KEY'))
		{
			// Up time and memory limit
			set_time_limit(600);
			ini_set('memory_limit', '256M');

			// If no cart, we create one
			if (!is_object($this->context->cart))
			{
				global $cart;
				$cart = new Cart();
				$this->context->cart = $cart;
			}

			// Run export method
			$this->export((empty($argument_key) ? 'display' : 'file'), Tools::getValue('l'), Tools::getValue('s'), Tools::getValue('e'));
		}
		else
			die('ERROR3');
	}
}

