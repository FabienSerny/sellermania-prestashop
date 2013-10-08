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

// Require Db requests class
require_once(dirname(__FILE__).'/classes/SellerManiaProduct15.php');

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
			$iso_lang = strtolower($language['iso_code']);
			$web_path_file = $module_web_path.$this->get_export_filename($iso_lang, true);
			$real_path_file = $this->get_export_filename($iso_lang);
			$files_list[$iso_lang]['file'] = $web_path_file;
			if (file_exists($real_path_file))
				$files_list[$iso_lang]['generated'] = date("d/m/Y H:i:s", filectime($real_path_file));
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
	 * Get export filename
	 * @param string $iso_lang
	 * @return string export file name
	 */
	public function get_export_filename($iso_lang, $web_path = false)
	{
		$sellermania_key = Configuration::get('SELLERMANIA_KEY');
		if ($web_path)
			return 'export/export-'.strtolower($iso_lang).'-'.$sellermania_key.'.csv';
		return dirname(__FILE__).'/export/export-'.strtolower($iso_lang).'-'.$sellermania_key.'.csv';
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
				@unlink($this->get_export_filename($iso_lang));
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
				$row['combinations'] = SellerManiaProduct::getProductCombinations($row['id_product'], $id_lang);
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
			$real_path_file = $this->get_export_filename($iso_lang);
			file_put_contents($real_path_file, $line, FILE_APPEND);
		}
		else
			echo $line;
	}
}

