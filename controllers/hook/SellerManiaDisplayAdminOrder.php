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

class SellerManiaDisplayAdminOrderController
{
	/**
	 * @var private array conditions
	 */
	private $conditions_list = array();

	/**
	 * @var private array status
	 */
	private $status_list = array();

	/**
	 * Controller constructor
	 */
	public function __construct($module, $dir_path, $web_path)
	{
		$this->module = $module;
		$this->web_path = $web_path;
		$this->dir_path = $dir_path;
		$this->context = Context::getContext();

		$this->conditions_list = array(
			0 => $this->module->l('Unknown'),
			1 => $this->module->l('Like new'),
			2 => $this->module->l('Very good'),
			3 => $this->module->l('Good'),
			4 => $this->module->l('Acceptable'),
			5 => $this->module->l('Collectible like new'),
			6 => $this->module->l('Collectible very good'),
			7 => $this->module->l('Collectible good'),
			8 => $this->module->l('Collectible acceptable'),
			10 => $this->module->l('Refurbished mint'),
			11 => $this->module->l('New'),
			12 => $this->module->l('New OEM'),
			13 => $this->module->l('Used openbox'),
		);

		$this->status_list = array(
			6 => $this->module->l('To be confirmed'),
			10 => $this->module->l('Awaiting confirmation'),
			9 => $this->module->l('Confirmed'),
			3 => $this->module->l('Cancelled by the customer'),
			4 => $this->module->l('Cancelled by the seller'),
			1 => $this->module->l('To dispatch'),
			5 => $this->module->l('Awaiting dispatch'),
			2 => $this->module->l('Dispatched'),
		);
	}

	/**
	 * Save status
	 * @param string $order_id
	 */
	public function saveOrderStatus($order_id)
	{
		// Check if form has been submitted
		if (Tools::getValue('sellermania_line_max') == '')
			return false;

		// Check confirm or cancel
		$action = 'cancel';
		for ($i = 1; Tools::getValue('status_'.$i) != ''; $i++)
			if (Tools::getValue('status_'.$i) == 9)
				$action = 'confirm';

		// Preprocess data
		$order_items = array();
		$line_max = Tools::getValue('sellermania_line_max');
		for ($i = 1; $i <= $line_max; $i++)
			if (Tools::getValue('sku_status_'.$i) != '')
			{
				$order_items[] = array(
					'orderId' => pSQL($order_id),
					'sku' => pSQL(Tools::getValue('sku_status_'.$i)),
					'orderStatusId' => Tools::getValue('status_'.$i),
					'trackingNumber' => '',
					'shippingCarrier' => '',
				);
			}

		try
		{
			// Calling the confirmOrder service
			$client = new Sellermania\OrderConfirmClient();
			$client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
			$client->setToken(Configuration::get('SM_ORDER_TOKEN'));
			$client->setEndpoint(Configuration::get('SM_CONFIRM_ORDER_ENDPOINT'));
			$result = $client->confirmOrder($order_items);

			// Fix data (when only one result, array is not the same)
			if (!isset($result['OrderItemConfirmationStatus'][0]))
				$result['OrderItemConfirmationStatus'] = array($result['OrderItemConfirmationStatus']);

			// Change order status
			if ($result['Status'] == 'SUCCESS')
			{
				// Get new order state ID
				$new_order_state = Configuration::get('PS_OS_CANCELED');
				if ($action == 'confirm')
					$new_order_state = Configuration::get('PS_OS_SM_CONFIRMED');

				// Load order and check existings payment
				$order = new Order((int)Tools::getValue('id_order'));
				$use_existings_payment = false;
				if (!$order->hasInvoice())
					$use_existings_payment = true;

				// Create new OrderHistory
				$history = new OrderHistory();
				$history->id_order = $order->id;
				$history->id_employee = (int)$this->context->employee->id;
				$history->id_order_state = (int)$new_order_state;
				$history->changeIdOrderState((int)$new_order_state, $order, $use_existings_payment);
				$history->add();
			}

			// Return results
			return $result;
		}
		catch (\Exception $e)
		{
			$this->context->smarty->assign('sellermania_error', strip_tags($e->getMessage()));
		}

	}

	/**
	 * Save shipping status
	 * @param string $order_id
	 */
	public function saveShippingStatus($sellermania_order)
	{
		// Check if form has been submitted
		if (Tools::getValue('sellermania_tracking_registration') == '')
			return false;

		// Check shipping status
		$status_to_ship = $this->isStatusToShip($sellermania_order);
		if ($status_to_ship != 1)
			return false;

		// Preprocess data
		$order_items = array();
		foreach ($sellermania_order['OrderInfo']['Product'] as $product)
			if ($product['Status'] == 1)
				$order_items[] = array(
					'orderId' => pSQL($sellermania_order['OrderInfo']['OrderId']),
					'sku' => pSQL($product['Sku']),
					'orderStatusId' => 2,
					'trackingNumber' => pSQL(Tools::getValue('tracking_number')),
					'shippingCarrier' => pSQL(Tools::getValue('shipping_name')),
				);

		try
		{
			// Calling the confirmOrder service
			$client = new Sellermania\OrderConfirmClient();
			$client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
			$client->setToken(Configuration::get('SM_ORDER_TOKEN'));
			$client->setEndpoint(Configuration::get('SM_CONFIRM_ORDER_ENDPOINT'));
			$result = $client->confirmOrder($order_items);

			// Fix data (when only one result, array is not the same)
			if (!isset($result['OrderItemConfirmationStatus'][0]))
				$result['OrderItemConfirmationStatus'] = array($result['OrderItemConfirmationStatus']);

			// Change order status
			if ($result['Status'] == 'SUCCESS')
			{
				// Load order and check existings payment
				$order = new Order((int)Tools::getValue('id_order'));
				$use_existings_payment = false;
				if (!$order->hasInvoice())
					$use_existings_payment = true;

				// Create new OrderHistory
				$history = new OrderHistory();
				$history->id_order = $order->id;
				$history->id_employee = (int)$this->context->employee->id;
				$history->id_order_state = (int)Configuration::get('PS_OS_SM_SENT');
				$history->changeIdOrderState((int)Configuration::get('PS_OS_SM_SENT'), $order, $use_existings_payment);
				$history->add();
			}

			// Return results
			return $result;
		}
		catch (\Exception $e)
		{
			$this->context->smarty->assign('sellermania_error', strip_tags($e->getMessage()));
		}

	}

	/**
	 * Refresh order
	 * @param string $order_id
	 * @return mixed array data
	 */
	public function refreshOrder($order_id)
	{
		// Retrieving data
		$client = new Sellermania\OrderClient();
		$client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
		$client->setToken(Configuration::get('SM_ORDER_TOKEN'));
		$client->setEndpoint(Configuration::get('SM_ORDER_ENDPOINT'));
		$result = $client->getOrderById($order_id);

		// Preprocess data and fix order
		$controller = new SellerManiaImportOrderController($this->module, $this->dir_path, $this->web_path);
		$controller->data = $result['SellermaniaWs']['GetOrderResponse']['Order'];
		$controller->preprocessData();
		$controller->order = new Order((int)Tools::getValue('id_order'));
		$controller->fixOrder(false);

		// Saving it
		$id_sellermania_order = Db::getInstance()->getValue('SELECT `id_sellermania_order` FROM `'._DB_PREFIX_.'sellermania_order` WHERE `id_order` = '.(int)Tools::getValue('id_order'));
		$sellermania_order = new SellermaniaOrder($id_sellermania_order);
		$sellermania_order->info = json_encode($controller->data);
		$sellermania_order->date_accepted = NULL;
		$sellermania_order->update();

		// Return data
		return $controller->data;
	}

	public function isStatusToShip($sellermania_order)
	{
		// Check if there is a flag to dispatch
		$status_to_ship = 1;
		foreach ($sellermania_order['OrderInfo']['Product'] as $product)
			if ($product['Status'] != 1)
				$status_to_ship = 0;
		return $status_to_ship;
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

		// Save order line status
		$result_status_update = $this->saveOrderStatus($sellermania_order['OrderInfo']['OrderId']);

		// Check if there is a flag to dispatch
		$result_shipping_status_update = $this->saveShippingStatus($sellermania_order);

		// Refresh order from Sellermania webservices
		$sellermania_order = $this->refreshOrder($sellermania_order['OrderInfo']['OrderId']);

		// Refresh flag to dispatch
		$status_to_ship = $this->isStatusToShip($sellermania_order);

		$this->context->smarty->assign('sellermania_order', $sellermania_order);
		$this->context->smarty->assign('sellermania_module_path', $this->web_path);
		$this->context->smarty->assign('sellermania_status_list', $this->status_list);
		$this->context->smarty->assign('sellermania_conditions_list', $this->conditions_list);
		$this->context->smarty->assign('sellermania_status_to_ship', $status_to_ship);
		$this->context->smarty->assign('sellermania_status_update', $result_status_update);
		$this->context->smarty->assign('sellermania_shipping_status_update', $result_shipping_status_update);

		return $this->module->compliantDisplay('displayAdminOrder.tpl');
	}
}

