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

	public function install()
	{
		Configuration::updateValue('SELLERMANIA_KEY', md5(rand()._COOKIE_KEY_.date('YmdHis')));
		return parent::install();
	}

	public function uninstall()
	{
		Configuration::deleteByName('SELLERMANIA_KEY');
		return parent::uninstall();
	}

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

	public function delete_export_files($iso_lang)
	{
		// Init
		$languages_list = Language::getLanguages();
		$sellermania_key = Configuration::get('SELLERMANIA_KEY');

		// Delete all export files or only export file of the selected language
		if ($iso_lang != '')
			@unlink(dirname(__FILE__).'/export/export-'.$iso_lang.'-'.$sellermania_key.'.csv');
		else
			foreach ($languages_list as $language)
				@unlink(dirname(__FILE__).'/export/export-'.strtolower($language['iso_code']).'-'.$sellermania_key.'.csv');
	}

	public function export($output, $iso_lang = '')
	{
		// If output is file, we delete old export files
		if ($output == 'file')
			$this->delete_export_files($iso_lang);
	}
}

