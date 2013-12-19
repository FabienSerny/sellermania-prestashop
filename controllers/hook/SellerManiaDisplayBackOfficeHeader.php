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

// Load ImportOrder Controller
require_once(dirname(__FILE__).'/SellerManiaImportOrder.php');

class SellerManiaDisplayBackOfficeHeaderController
{
	public $marketplaces_list = array(
              Sellermania\OrderClient::MKP_AMAZON_FR,
              Sellermania\OrderClient::MKP_AMAZON_COM,
              Sellermania\OrderClient::MKP_AMAZON_DE,
              Sellermania\OrderClient::MKP_AMAZON_UK,
              Sellermania\OrderClient::MKP_AMAZON_CA,
              Sellermania\OrderClient::MKP_AMAZON_IT,
              Sellermania\OrderClient::MKP_AMAZON_ES,
              Sellermania\OrderClient::MKP_2XMOINSCHER,
              Sellermania\OrderClient::MKP_FNAC_COM,
              Sellermania\OrderClient::MKP_PRICEMINISTER_FR,
              Sellermania\OrderClient::MKP_EBAY_FR,
              Sellermania\OrderClient::MKP_EBAY_DE,
              Sellermania\OrderClient::MKP_EBAY_UK,
              Sellermania\OrderClient::MKP_PIXMANIA_FR,
              Sellermania\OrderClient::MKP_PIXMANIA_UK,
              Sellermania\OrderClient::MKP_PIXMANIA_DE,
              Sellermania\OrderClient::MKP_PIXMANIA_IT,
              Sellermania\OrderClient::MKP_PIXMANIA_ES,
              Sellermania\OrderClient::MKP_RUEDUCOMMERCE_FR,
              Sellermania\OrderClient::MKP_CDISCOUNT_COM,
	);


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
	 * Import SellerMania orders
	 */
	public function importOrders()
	{
		// Creating an instance of OrderClient
		$client = new Sellermania\OrderClient();
		$client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
		$client->setToken(Configuration::get('SM_ORDER_TOKEN'));
		$client->setEndpoint(Configuration::get('SM_ORDER_ENDPOINT'));


		// Set dates limit
		$date_start = date("Y-m-d H:i:s", strtotime('-30 days'));
		$date_end = date('Y-m-d');
		//$date_start = '2013-06-01';
		//$date_end = '2013-06-31';

		foreach ($this->marketplaces_list as $marketplace)
		{
			try
			{
				// Recovering dispatched orders for the last 30 days
				$result = $client->getOrderByStatus(
					Sellermania\OrderClient::STATUS_TO_BE_CONFIRMED,
					$marketplace,
					new \DateTime($date_start),
					new \DateTime($date_end)
				);

				// Import order
				if (isset($result['SellermaniaWs']['GetOrderResponse']['Order']))
					foreach ($result['SellermaniaWs']['GetOrderResponse']['Order'] as $order)
						if (!SellermaniaOrder::orderHasAlreadyBeenImported($order['OrderInfo']['MarketPlace'], $order['OrderInfo']['OrderId']))
						{
							// Save config value
							$ps_guest_checkout_enabled = Configuration::get('PS_GUEST_CHECKOUT_ENABLED');
							Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', 1);

							// Import Order
							$import_order = new SellerManiaImportOrderController($this->module, $this->dir_path, $this->web_path);
							$import_order->run($order);

							// Restore config value
							Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', $ps_guest_checkout_enabled);
						}
			}
			catch (\Exception $e)
			{
				if (Tools::getValue('debug') == 'import')
				{
					$log = date('Y-m-d H:i:s').': '.$e->getMessage()."\n";
					file_put_contents(dirname(__FILE__).'/../../log/log-'.Configuration::get('SELLERMANIA_KEY').'.txt', $log, FILE_APPEND);
				}
			}
		}
	}


	/**
	 * Check if it's the time to import orders
	 */
	public function timeToImportOrders()
	{
		$next_import = Configuration::get('SM_NEXT_IMPORT');
		if ($next_import == '' || $next_import < date('Y-m-d H:i:s') || Tools::getValue('debug') == 'import')
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

