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


class SellerManiaDisplayBackOfficeHeaderController
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
	 * Check if it's the time to import orders
	 */
	public function timeToImportOrders()
	{
		$next_import = Configuration::get('SM_NEXT_IMPORT');
		if ($next_import == '' || $next_import < date('Y-m-d H:i:s'))
		{
			// Update next import
			$next_import = date("Y-m-d H:i:s", strtotime('+1 hour'));
			Configuration::updateValue('SM_NEXT_IMPORT', $next_import);

			// It's time!
			return true;
		}

		// It's not the time!
		return false;
	}

	/**
	 * Import SellerMania orders
	 */
	public function importOrders()
	{

	}

	/**
	 * Run method
	 * @return string $html
	 */
	public function run()
	{
		// Check if credentials are ok
		if (Configuration::get('SM_CREDENTIALS_CHECK') != 'ok')
			return '';

		// Check if it's time to import
		if ($this->timeToImportOrders())
			$this->importOrders();
	}
}

