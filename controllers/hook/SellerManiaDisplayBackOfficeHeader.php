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

// Load ImportOrder Controller and DisplayAdminOrderController
require_once(dirname(__FILE__).'/SellerManiaImportOrder.php');
require_once(dirname(__FILE__).'/SellerManiaDisplayAdminOrder.php');

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
		$date_end = date('Y-m-d H:i:s');
		if ($date_start < Configuration::get('SM_INSTALL_DATE'))
			$date_start = Configuration::get('SM_INSTALL_DATE');


		try
		{
			// Recovering dispatched orders for the last 30 days
			$result = $client->getOrderByDate(
				new \DateTime($date_start),
				new \DateTime($date_end)
			);

			// Import order
			if (isset($result['SellermaniaWs']['GetOrderResponse']['Order']))
			{
				// Fix data (when only one order, array is not the same)
				if (!isset($result['SellermaniaWs']['GetOrderResponse']['Order'][0]))
					$result['SellermaniaWs']['GetOrderResponse']['Order'] = array($result['SellermaniaWs']['GetOrderResponse']['Order']);

				// Import order
				foreach ($result['SellermaniaWs']['GetOrderResponse']['Order'] as $order)
					if (isset($order['OrderInfo']['OrderId']))
					{
						$id_sellermania_order = SellermaniaOrder::getSellermaniaOrderId($order['OrderInfo']['MarketPlace'], $order['OrderInfo']['OrderId']);
						if ($id_sellermania_order > 0)
						{
							$smo = new SellermaniaOrder((int)$id_sellermania_order);
							if ($smo->id_order > 0)
							{
								$sdao = new SellerManiaDisplayAdminOrderController($this->module, $this->dir_path, $this->web_path);
								$sdao->refreshOrderStatus($smo->id_order, $order);
							}
						}
						else
						{
							// Save config value
							$ps_guest_checkout_enabled = Configuration::get('PS_GUEST_CHECKOUT_ENABLED');
							Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', 1);

							// Import Order
							try
							{
								$import_order = new SellerManiaImportOrderController($this->module, $this->dir_path, $this->web_path);
								$import_order->run($order);
							}
							catch (\Exception $e)
							{
								// If could not import it in PrestaShop we stored it anyway
								$currency_iso_code = 'EUR';
								if (isset($this->data['OrderInfo']['Amount']['Currency']))
									$currency_iso_code = $this->data['OrderInfo']['Amount']['Currency'];
								$id_currency = Currency::getIdByIsoCode($currency_iso_code);
								$amount_total = $order['OrderInfo']['TotalAmount']['Amount']['Price'];
								$sellermania_order = new SellermaniaOrder();
								$sellermania_order->marketplace = trim($order['OrderInfo']['MarketPlace']);
								$sellermania_order->customer_name = $order['User'][0]['Name'];
								$sellermania_order->ref_order = trim($order['OrderInfo']['OrderId']);
								$sellermania_order->amount_total = Tools::displayPrice($amount_total, $id_currency);
								$sellermania_order->info = json_encode($order);
								$sellermania_order->error = $e->getMessage();
								$sellermania_order->id_order = 0;
								$sellermania_order->id_employee_accepted = 0;
								$sellermania_order->date_payment = (isset($order['Paiement']['Date']) ? substr($order['Paiement']['Date'], 0, 19) : '');
								$sellermania_order->add();
							}

							// Restore config value
							Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', $ps_guest_checkout_enabled);
						}
					}
			}
		}
		catch (\Exception $e)
		{
			$log = date('Y-m-d H:i:s').': '.$e->getMessage()."\n";
			file_put_contents(dirname(__FILE__).'/../../log/webservice-error-'.Configuration::get('SELLERMANIA_KEY').'.txt', $log, FILE_APPEND);
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
		if (Configuration::get('SM_CREDENTIALS_CHECK') != 'ok' || Configuration::get('SM_IMPORT_ORDERS') != 'yes' || Configuration::get('SM_DEFAULT_PRODUCT_ID') < 1)
			return '';

		// If ajax, we do not import orders
		if (Tools::getValue('ajax') != '')
			return '';

		// Check if it's time to import
		if ($this->timeToImportOrders())
			$this->importOrders();

		// Include JS script
		if (Tools::getValue('controller') == 'AdminOrders' || Tools::getValue('tab') == 'AdminOrders')
		{
			$this->context->smarty->assign('sellermania_module_path', $this->web_path);
			return $this->module->compliantDisplay('displayBackOfficeHeader.tpl');
		}
	}
}

