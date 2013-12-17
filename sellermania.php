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
$db_requests_class_file = 'SellerManiaProduct15.php';
if (version_compare(_PS_VERSION_, '1.5') < 0)
	$db_requests_class_file = 'SellerManiaProduct14.php';
require_once(dirname(__FILE__).'/classes/'.$db_requests_class_file);

// Load ObjectModel and PaymentModule classes
require_once(dirname(__FILE__).'/classes/SellermaniaOrder.php');
require_once(dirname(__FILE__).'/classes/SellermaniaPaymentModule.php');

// Load the Sellermania API Client
require_once(dirname(__FILE__).'/lib/Sellermania.php');


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
		// Execute module install MySQL commands
		$sql_file = dirname(__FILE__).'/install/install.sql';
		if (!$this->loadSQLFile($sql_file))
			return false;

		// Register hooks
		if (!parent::install() || !$this->registerHook('displayAdminOrder') || !$this->registerHook('displayBackOfficeHeader'))
			return false;

		// Gen SellerMania key
		Configuration::updateValue('SELLERMANIA_KEY', md5(rand()._COOKIE_KEY_.date('YmdHis')));

		return true;
	}


	/**
	 * Uninstall method
	 * @return boolean success
	 */
	public function uninstall()
	{
		// Execute module install MySQL commands
		$sql_file = dirname(__FILE__).'/install/uninstall.sql';
		if (!$this->loadSQLFile($sql_file))
			return false;

		// Delete configuration values
		Configuration::deleteByName('SM_IMPORT_ORDERS');
		Configuration::deleteByName('SM_ORDER_EMAIL');
		Configuration::deleteByName('SM_ORDER_TOKEN');
		Configuration::deleteByName('SM_ORDER_ENDPOINT');
		Configuration::deleteByName('SM_NEXT_IMPORT');
		Configuration::deleteByName('SM_CREDENTIALS_CHECK');
		Configuration::deleteByName('SELLERMANIA_KEY');

		return parent::uninstall();
	}


	/**
	 * Load SQL file
	 * @return boolean success
	 */
	public function loadSQLFile($sql_file)
	{
		// Get install MySQL file content
		$sql_content = file_get_contents($sql_file);

		// Replace prefix and store MySQL command in array
		$sql_content = str_replace('PREFIX_', _DB_PREFIX_, $sql_content);
		$sql_requests = preg_split("/;\s*[\r\n]+/", $sql_content);

		// Execute each MySQL command
		$result = true;
		foreach($sql_requests AS $request)
			if (!empty($request))
				$result &= Db::getInstance()->execute(trim($request));

		// Return result
		return $result;
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
	 * @param string $hook_name
	 * @return mixed $result
	 */
	public function runController($controller_type, $controller_name)
	{
		// Include the controller file
		require_once(dirname(__FILE__).'/controllers/'.$controller_type.'/SellerMania'.$controller_name.'.php');
		$controller_name = 'SellerMania'.$controller_name.'Controller';
		$controller = new $controller_name($this, dirname(__FILE__), $this->_path);
		return $controller->run();
	}


	/**
	 * Configuration method
	 * @return string $html
	 */
	function getContent()
	{
		return $this->runController('hook', 'GetContent');
	}


	/**
	 * Display BackOffice Header Hook
	 * @return string $html
	 */
	public function hookDisplayBackOfficeHeader($params)
	{
		return $this->runController('hook', 'DisplayBackOfficeHeader');
	}


	/**
	 * Export method
	 * @return string $export
	 */
	public function export()
	{
		return $this->runController('front', 'Export');
	}

}

