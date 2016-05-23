<?php
/*
* 2010-2016 Sellermania / Froggy Commerce / 23Prod SARL
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
*  @author         Froggy Commerce <team@froggy-commerce.com>
*  @copyright      2010-2016 Sellermania / Froggy Commerce / 23Prod SARL
*  @version        1.0
*  @license        http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

/*
 * Security
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('FroggyHelperTreeCategories'))
	require_once(dirname(__FILE__).'/../../classes/FroggyHelperTreeCategories.php');
require_once(dirname(__FILE__).'/../front/SellerManiaExport.php');

class SellerManiaGetContentController
{
	/**
	 * Controller constructor
	 */
	public function __construct($module, $dir_path, $web_path)
	{
		$this->module = $module;
		$this->web_path = $web_path;
		$this->dir_path = $dir_path;
		$this->context = Context::getContext();
	}


	/**
	 * Test configuration
	 */
	public function testConfiguration()
	{
		try
		{
			require_once(dirname(__FILE__).'/../../classes/SellermaniaTestAPI.php');
			$test = new SellermaniaTestAPI();
			$test->run();
			Configuration::updateValue('SM_CREDENTIALS_CHECK', 'ok');
			$this->context->smarty->assign('sm_confirm_credentials', 'ok');
		}
		catch (\Exception $e)
		{
			Configuration::updateValue('SM_CREDENTIALS_CHECK', 'ko');
			$this->context->smarty->assign('sm_error_credentials', $e->getMessage());
		}
	}


	/**
	 * Save configuration
	 */
	public function saveConfiguration()
	{
		if (Tools::isSubmit('export_configuration'))
		{
			Configuration::updateValue('SM_EXPORT_CATEGORIES', '');
			if (isset($_POST['categories_to_export']) && count($_POST['categories_to_export']) > 0)
			{
				$categories = $_POST['categories_to_export'];
				foreach ($categories as $kc => $vc)
					$categories[(int)$kc] = (int)$vc;
				Configuration::updateValue('SM_EXPORT_CATEGORIES', json_encode($categories));
			}
			$this->context->smarty->assign('sm_confirm_export_options', 1);
		}

		$params = array('sm_export_all', 'sm_import_orders', 'sm_order_email', 'sm_order_token', 'sm_order_endpoint',
						'sm_confirm_order_endpoint', 'sm_inventory_endpoint',
						'sm_stock_sync_option', 'sm_stock_sync_option_1', 'sm_stock_sync_option_2',
						'sm_stock_sync_nb_char', 'sm_stock_sync_position',
						'sm_alert_missing_ref_option', 'sm_alert_missing_ref_mail',
						'sm_enable_native_refund_system', 'sm_enable_export_comb_name');

		foreach ($params as $p)
			if (isset($_POST[$p]))
				Configuration::updateValue(strtoupper($p), trim($_POST[$p]));

		if (version_compare(PHP_VERSION, '5.3.0') >= 0)
			if (Configuration::get('SM_IMPORT_ORDERS') == 'yes')
				$this->testConfiguration();
	}


	/**
	 * Assign data to Smarty
	 */
	public function assignData()
	{
		// Init vars
		$languages_list = Language::getLanguages();

        if (empty($this->context->shop->id))
            $this->context->shop->setContext(1);

		$module_web_path = Tools::getHttpHost(true).$this->context->shop->physical_uri.'modules/'.$this->module->name.'/';
		$export_directory_writable = 0;
		if (is_writable($this->dir_path.'/export/'))
			$export_directory_writable = 1;
		$sellermania_key = Configuration::get('SELLERMANIA_KEY');
		if (empty($sellermania_key) && version_compare(_PS_VERSION_, '1.5') >= 0)
		{
			$sellermania_key_tmp = Configuration::get('SELLERMANIA_KEY', null, 1, 1);
			if (!empty($sellermania_key_tmp))
				Configuration::updateValue('SELLERMANIA_KEY', $sellermania_key_tmp);
			$sellermania_key = Configuration::get('SELLERMANIA_KEY');
		}

		$smec = new SellerManiaExportController();
		$module_url = 'index.php?controller='.Tools::getValue('controller').'&tab='.Tools::getValue('tab').'&token='.Tools::getValue('token');
		$module_url .= '&configure='.Tools::getValue('configure').'&tab_module='.Tools::getValue('tab_module').'&module_name='.Tools::getValue('module_name').'';

		// Retrieve orders in error
		if (Tools::getValue('reimport') > 0)
			SellermaniaOrder::deleteSellermaniaOrderInError((int)Tools::getValue('reimport'));
		if (Tools::getValue('reimport') == 'all')
			SellermaniaOrder::deleteAllSellermaniaOrdersInError();
		$orders_in_error = SellermaniaOrder::getSellermaniaOrdersInError();
		$nb_orders_in_error = count($orders_in_error);

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
		if (version_compare(PHP_VERSION, '5.3.0') < 0)
		{
			$this->context->smarty->assign('php_version', PHP_VERSION);
			$this->context->smarty->assign('no_namespace_compatibility', '1');
		}

		$documentation_iso_code = 'en';
		if (isset($this->context->language->iso_code) && in_array($this->context->language->iso_code, array('fr', 'en', 'es')))
			$documentation_iso_code = $this->context->language->iso_code;
		$this->context->smarty->assign('documentation_iso_code', $documentation_iso_code);

		$this->context->smarty->assign('orders_in_error', $orders_in_error);
		$this->context->smarty->assign('nb_orders_in_error', $nb_orders_in_error);

		$this->context->smarty->assign('module_url', $module_url);
		$this->context->smarty->assign('script_path', $this->dir_path);
		$this->context->smarty->assign('export_directory_writable', $export_directory_writable);
		$this->context->smarty->assign('module_web_path', $module_web_path);
		$this->context->smarty->assign('sellermania_key', $sellermania_key);
		$this->context->smarty->assign('files_list', $files_list);
		$this->context->smarty->assign('languages_list', $languages_list);
		$this->context->smarty->assign('sellermania_module_path', $this->web_path);

		$this->context->smarty->assign('category_tree', $this->renderCategoriesTree());

		$this->context->smarty->assign('sm_default_product', new Product(Configuration::get('SM_DEFAULT_PRODUCT_ID')));
		$this->context->smarty->assign('sm_default_product_id', Configuration::get('SM_DEFAULT_PRODUCT_ID'));
		$this->context->smarty->assign('sm_export_all', Configuration::get('SM_EXPORT_ALL'));
		$this->context->smarty->assign('sm_import_orders', Configuration::get('SM_IMPORT_ORDERS'));
		$this->context->smarty->assign('sm_order_email', Configuration::get('SM_ORDER_EMAIL'));
		$this->context->smarty->assign('sm_order_token', Configuration::get('SM_ORDER_TOKEN'));
		$this->context->smarty->assign('sm_order_endpoint', Configuration::get('SM_ORDER_ENDPOINT'));
		$this->context->smarty->assign('sm_confirm_order_endpoint', Configuration::get('SM_CONFIRM_ORDER_ENDPOINT'));
		$this->context->smarty->assign('sm_inventory_endpoint', Configuration::get('SM_INVENTORY_ENDPOINT'));
		$this->context->smarty->assign('sm_stock_sync_option', Configuration::get('SM_STOCK_SYNC_OPTION'));
		$this->context->smarty->assign('sm_stock_sync_option_1', Configuration::get('SM_STOCK_SYNC_OPTION_1'));
		$this->context->smarty->assign('sm_stock_sync_option_2', Configuration::get('SM_STOCK_SYNC_OPTION_2'));
		$this->context->smarty->assign('sm_stock_sync_nb_char', Configuration::get('SM_STOCK_SYNC_NB_CHAR'));
		$this->context->smarty->assign('sm_stock_sync_position', Configuration::get('SM_STOCK_SYNC_POSITION'));

		$this->context->smarty->assign('sm_alert_missing_ref_option', Configuration::get('SM_ALERT_MISSING_REF_OPTION'));
		$this->context->smarty->assign('sm_alert_missing_ref_mail', Configuration::get('SM_ALERT_MISSING_REF_MAIL'));

		$this->context->smarty->assign('sm_enable_native_refund_system', Configuration::get('SM_ENABLE_NATIVE_REFUND_SYSTEM'));
		$this->context->smarty->assign('sm_enable_export_comb_name', Configuration::get('SM_ENABLE_EXPORT_COMB_NAME'));

		if ($this->context->language->iso_code == 'fr')
		{
			$this->context->smarty->assign('sm_last_import', date('d/m/Y H:i:s', strtotime(Configuration::get('SM_NEXT_IMPORT').' -15 minutes')));
			$this->context->smarty->assign('sm_next_import', date('d/m/Y H:i:s', strtotime(Configuration::get('SM_NEXT_IMPORT'))));
		}
		else
		{
			$this->context->smarty->assign('sm_last_import', date('Y-m-d H:i:s', strtotime(Configuration::get('SM_NEXT_IMPORT').' -15 minutes')));
			$this->context->smarty->assign('sm_next_import', Configuration::get('SM_NEXT_IMPORT'));
		}
	}

	/**
	 * Render categories tree method
	 */
	public function renderCategoriesTree()
	{
		$root = Category::getRootCategory();

		$categories = array();
		$categories_selected = Configuration::get('SM_EXPORT_CATEGORIES');
		if (!empty($categories_selected))
			foreach (json_decode($categories_selected, true) as $key => $category)
				$categories[] = $category;

		$tree = new FroggyHelperTreeCategories();
		$tree->setAttributeName('categories_to_export');
		$tree->setRootCategory($root->id);
		$tree->setLang($this->context->employee->id_lang);
		$tree->setSelectedCategories($categories);
		$tree->setContext($this->context);
		$tree->setModule($this->module);
		return $tree->render();
	}

	/**
	 * Run method
	 * @return string $html
	 */
	public function run()
	{
		if (Tools::getValue('see') != 'orders-error')
			$this->saveConfiguration();
		$this->assignData();
		return $this->module->compliantDisplay('displayGetContent'.(isset($this->module->bootstrap) ? '.bootstrap' : '').'.tpl');
	}
}

