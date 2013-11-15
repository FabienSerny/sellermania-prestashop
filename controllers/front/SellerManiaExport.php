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

class SellerManiaExportController
{
	/**
	 * Controller constructor
	 * @param array $fields_to_export
	 */
	public function __construct($fields_to_export = array())
	{
		$this->fields_to_export = $fields_to_export;
		$this->context = Context::getContext();
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
		// If declination duplicate row for each declination
		if ($row['declinations'] && is_array($row['declinations']))
		{
			$rows = array();
			foreach ($row['declinations'] as $id_product_attribute => $declination)
			{
				$rowCopy = $row;
				$rowCopy['name'] = $rowCopy['name'].' '.implode(' ', $declination['attributes_values']);
				$rowCopy['price'] = Product::getPriceStatic($rowCopy['id_product'], true, $id_product_attribute, 2);
				$rowCopy['ecotax'] = $declination['ecotax'];
				$rowCopy['quantity'] = $declination['quantity'];
				$rowCopy['reference'] = $declination['reference'];
				if (isset($declination['images']) && count($declination['images']) >= 1)
					$rowCopy['images'] = $declination['images'];
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
		{
			$row['images'] = implode('|', $row['images']);
			foreach ($this->fields_to_export as $field)
				$line .= str_replace(array("\r\n", "\n"), '', $row[$field]).';';
			$line .= "\n";
		}
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
		$argument_key = '';
		if (isset($argv[1]))
			$argument_key = $argv[1];

		// Include config file and set default Shop
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
			ini_set('memory_limit', '64M');

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

