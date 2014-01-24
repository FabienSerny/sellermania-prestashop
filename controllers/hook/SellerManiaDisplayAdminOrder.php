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

require_once(dirname(__FILE__).'/../front/SellerManiaExport.php');

class SellerManiaDisplayAdminOrderController
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
	 * Run method
	 * @return string $html
	 */
	public function run()
	{
		// Check if credentials are ok
		if (Configuration::get('SM_CREDENTIALS_CHECK') != 'ok' || Configuration::get('SM_IMPORT_ORDERS') != 'yes' || Configuration::get('SM_DEFAULT_PRODUCT_ID') < 1)
			return '';

		// Retrieve order data
		$sellermania_order = Db::getInstance()->getValue('SELECT `info` FROM `'._DB_PREFIX_.'sellermania_order` WHERE `id_order` = '.(int)Tools::getValue('id_order'));
		if (empty($sellermania_order))
			return '';

		// Decode order data
		$sellermania_order = json_decode($sellermania_order, true);

		$this->context->smarty->assign('sellermania_order', $sellermania_order);
		$this->context->smarty->assign('sellermania_module_path', $this->web_path);

		return $this->module->compliantDisplay('displayAdminOrder.tpl');
	}
}

