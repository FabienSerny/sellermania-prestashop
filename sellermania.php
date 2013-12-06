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
if (version_compare(_PS_VERSION_, '1.5') < 0)
	require_once(dirname(__FILE__).'/classes/SellerManiaProduct14.php');
else
	require_once(dirname(__FILE__).'/classes/SellerManiaProduct15.php');
require_once(dirname(__FILE__).'/controllers/front/SellerManiaExport.php');


class SellerMania extends Module
{
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

		if (version_compare(_PS_VERSION_, '1.5') < 0)
			require(dirname(__FILE__).'/backward/backward.php');

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
	 * Compliant display between 1.4 and 1.5
	 * @param string $template
	 * @return string $html
	 */
	public function compliantDisplay($template)
	{
		if (version_compare(_PS_VERSION_, '1.5') < 0)
			return $this->display(__FILE__, 'views/templates/hook/'.$template);
		else
			return $this->display(__FILE__, $template);
	}



	/**
	 * Configuration method
	 * @return string html
	 */
	function getContent()
	{
		// Init vars
		$languages_list = Language::getLanguages();
		$this->context->shop->setContext(1);
		$module_web_path = Tools::getHttpHost(true).$this->context->shop->physical_uri.'modules/'.$this->name.'/';
		$export_directory_writable = 0;
		if (is_writable(dirname(__FILE__).'/export'))
			$export_directory_writable = 1;
		$sellermania_key = Configuration::get('SELLERMANIA_KEY');
		$smec = new SellerManiaExportController();

		// Check if file exists and retrieve the creation date
		$files_list = array();
		foreach ($languages_list as $language)
		{
			$iso_lang = strtolower($language['iso_code']);
			$web_path_file = $module_web_path.$smec->get_export_filename($iso_lang, true);
			$real_path_file = $smec->get_export_filename($iso_lang);
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

		$this->context->smarty->assign('sm_import_orders', Configuration::get('SM_IMPORT_ORDERS'));
		$this->context->smarty->assign('sm_order_email', Configuration::get('SM_ORDER_EMAIL'));
		$this->context->smarty->assign('sm_order_token', Configuration::get('SM_ORDER_TOKEN'));
		$this->context->smarty->assign('sm_order_endpoint', Configuration::get('SM_ORDER_ENDPOINT'));

		// Return display
		return $this->compliantDisplay('displayGetContent.tpl');
	}



	/**
	 * Export method
	 */
	public function export()
	{
		$controller = new SellerManiaExportController();
		$controller->run();
	}
}

