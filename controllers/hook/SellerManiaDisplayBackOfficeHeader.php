<?php
/*
* 2010 - 2014 Sellermania / Froggy Commerce / 23Prod SARL
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
*  @author Fabien Serny - Froggy Commerce <team@froggy-commerce.com>
*  @copyright	2010-2014 Sellermania / Froggy Commerce / 23Prod SARL
*  @version		1.0
*  @license		http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_'))
	exit;

// Load ImportOrder Controller and DisplayAdminOrderController
require_once(dirname(__FILE__).'/SellerManiaImportOrder.php');
require_once(dirname(__FILE__).'/SellerManiaDisplayAdminOrder.php');


// Load ValidateOrder Controller
require_once(dirname(__FILE__).'/SellerManiaActionValidateOrder.php');


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
		$this->ps_version = str_replace('.', '', substr(_PS_VERSION_, 0, 3));
	}


	/**
	 * Import SellerMania orders
	 */
	public function importOrders()
	{
		// Define that we are in the Import Orders Context
		define('SELLERMANIA_IMPORT_ORDERS_CONTEXT', 1);

		// Creating an instance of OrderClient
		$client = new Sellermania\OrderClient();
		$client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
		$client->setToken(Configuration::get('SM_ORDER_TOKEN'));
		$client->setEndpoint(Configuration::get('SM_ORDER_ENDPOINT'));

		// Set dates limit
		$count_order = 0;
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
						// Check if order exists
						$id_sellermania_order = SellermaniaOrder::getSellermaniaOrderId($order['OrderInfo']['MarketPlace'], $order['OrderInfo']['OrderId']);
						if ($id_sellermania_order > 0)
						{
							// If do exist and associate to a PrestaShop order, we update order status
							$smo = new SellermaniaOrder((int)$id_sellermania_order);
							if ($smo->id_order > 0)
							{
								try
								{
									$sdao = new SellerManiaDisplayAdminOrderController($this->module, $this->dir_path, $this->web_path);
									$sdao->refreshOrderStatus($smo->id_order, $order);
								}
								catch (\Exception $e)
								{
									// Log error
									$log = '[UPDATE] - '.date('Y-m-d H:i:s').': '.$e->getMessage()."\n";
									$log .= var_export($order, true)."\n";
									file_put_contents(dirname(__FILE__).'/../../log/order-error-'.Configuration::get('SELLERMANIA_KEY').'.txt', $log, FILE_APPEND);
								}
							}
						}
						else
						{
							// If does not exist, we import the order
							try
							{
								// Save config value
								$ps_guest_checkout_enabled = Configuration::get('PS_GUEST_CHECKOUT_ENABLED');
								Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', 1);

								// Import order as PrestaShop order
								$import_order = new SellerManiaImportOrderController($this->module, $this->dir_path, $this->web_path);
								$import_order->run($order);
								$count_order++;

								// Refresh order status immediately
								$sdao = new SellerManiaDisplayAdminOrderController($this->module, $this->dir_path, $this->web_path);
								$sdao->refreshOrderStatus($import_order->order->id, $order);

								// Restore config value
								Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', $ps_guest_checkout_enabled);

								// Do not push it too hard
								if ($count_order > 100)
									return true;
							}
							catch (\Exception $e)
							{
								// Import order as error
								$import_order = new SellerManiaImportOrderController($this->module, $this->dir_path, $this->web_path);
								$import_order->data = $order;
								$import_order->preprocessData();
								if (!isset($import_order->order->id))
									$import_order->order = (object)$import_order->order;
								$import_order->order->id = 0;
								$import_order->saveSellermaniaOrder($e->getMessage());

								// Log error
								$log = '[INSERT] - '.date('Y-m-d H:i:s').': '.$e->getMessage()."\n";
								$log .= var_export($order, true)."\n";
								file_put_contents(dirname(__FILE__).'/../../log/order-error-'.Configuration::get('SELLERMANIA_KEY').'.txt', $log, FILE_APPEND);
							}
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
			$next_import = date("Y-m-d H:i:s", strtotime('+15 minutes'));
			Configuration::updateValue('SM_NEXT_IMPORT', $next_import);

			// It's time!
			return true;
		}

		// It's not the time!
		return false;
	}


	/**
	 * Handle order importation
	 */
	public function handleOrderImportation()
	{
		// If ajax, we do not do anything
		if (Tools::getValue('ajax') != '')
			return '';

		// Check if it's time to import
		if ($this->timeToImportOrders())
			$this->importOrders();

	}


	/**
	 * Handle product quantity update
	 */
	public function handleProductQuantityUpdate()
	{
		$id_product = (int)Tools::getValue('id_product');
		$id_product_attribute = (int)Tools::getValue('id_product_attribute');
		$id_lang = (int)$this->context->cookie->id_lang;
		if (Tools::getValue('controller') == 'AdminProducts' && Tools::getValue('actionQty') == 'set_qty' && $id_product > 0)
		{
			// We retrieve the product
			$product = new Product((int)$id_product, false, $id_lang);

			// We retrieve the SKU and current quantity
			if ($id_product_attribute > 0)
			{
				$attr = $product->getAttributeCombinationsById($id_product_attribute, $id_lang);
				$sku_value = $attr[0]['reference'];
				$current_quantity = (int)$attr[0]['quantity'];
			}
			else
			{
				$sku_value = $product->reference;
				$current_quantity = (int)$product->getQuantity($id_product, $id_product_attribute);
			}

			// If no SKU, we stop
			if (empty($sku_value))
				return false;

			// We calcul the difference in quantity
			$new_quantity = (int)Tools::getValue('value');
			$difference = $current_quantity - $new_quantity;

			// We synchronize the stock
			$skus_quantities = array($sku_value => $difference);
			$skus = array($sku_value);
			$savo = new SellerManiaActionValidateOrderController($this->module, $this->dir_path, $this->web_path);
			$savo->syncStock('INVENTORY', $id_product.'-'.$id_product_attribute, $skus, $skus_quantities);
		}
	}


	/**
	 * Handle Sellermania order display
	 */
	public function handleSellermaniaOrderDisplay()
	{
		// If ajax, we do not do anything
		if (Tools::getValue('ajax') != '')
			return '';

		// Include JS script
		if (Tools::getValue('controller') == 'AdminOrders' || Tools::getValue('tab') == 'AdminOrders')
		{
			$this->context->smarty->assign('ps_version', $this->ps_version);
			$this->context->smarty->assign('sellermania_module_path', $this->web_path);
			return $this->module->compliantDisplay('displayBackOfficeHeader.tpl');
		}
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

		// Handle order actions
		$this->handleOrderImportation();
		$this->handleProductQuantityUpdate();
		$this->handleSellermaniaOrderDisplay();
	}
}

