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

// Load ImportOrder Controller
require_once(dirname(__FILE__).'/SellermaniaImportOrder.php');

class SellermaniaDisplayAdminOrderController
{
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
        $this->ps_version = str_replace('.', '', substr(_PS_VERSION_, 0, 3));

        $this->status_list = array(
            6 => $this->module->l('To be confirmed', 'sellermaniadisplayadminorder'),
            10 => $this->module->l('Awaiting confirmation', 'sellermaniadisplayadminorder'),
            9 => $this->module->l('Confirmed', 'sellermaniadisplayadminorder'),
            3 => $this->module->l('Cancelled by the customer', 'sellermaniadisplayadminorder'),
            4 => $this->module->l('Cancelled by the seller', 'sellermaniadisplayadminorder'),
            1 => $this->module->l('To dispatch', 'sellermaniadisplayadminorder'),
            5 => $this->module->l('Awaiting dispatch', 'sellermaniadisplayadminorder'),
            2 => $this->module->l('Dispatched', 'sellermaniadisplayadminorder'),
        );
    }

    /**
     * Save status
     * @param string $order_id
     */
    public function saveOrderStatus($order_id, $sellermania_order)
    {
        // Check if form has been submitted
        if (Tools::getValue('sellermania_line_max') == '')
            return false;

        // Preprocess data
        $order_items = array();
        $line_max = Tools::getValue('sellermania_line_max');
        for ($i = 1; $i <= $line_max; $i++)
            if (Tools::getValue('sku_status_'.$i) != '')
            {
                // Find match and check if not already marked as changed
                foreach ($sellermania_order['OrderInfo']['Product'] as $kp => $product)
                    if ($product['Sku'] == Tools::getValue('sku_status_'.$i) &&
                        $sellermania_order['OrderInfo']['Product'][$kp]['Status'] == \Sellermania\OrderConfirmClient::STATUS_TO_BE_CONFIRMED)
                    {
                        $order_items[] = array(
                            'orderId' => pSQL($order_id),
                            'sku' => pSQL(Tools::getValue('sku_status_'.$i)),
                            'orderStatusId' => Tools::getValue('status_'.$i),
                            'trackingNumber' => '',
                            'shippingCarrier' => '',
                        );
                        $sellermania_order['OrderInfo']['Product'][$kp]['Status'] = Tools::getValue('status_'.$i);
                    }
            }

        // Check if there order item status to change
        if (empty($order_items)) {
            return false;
        }

        return SellermaniaOrderConfirmation::updateOrderItems($order_items);
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
        $status_to_ship = self::isStatusToShip($sellermania_order);
        if ($status_to_ship != 1)
            return false;

        // Set orders param
        $orders = array(
            array(
                'id_order' => (int)Tools::getValue('id_order'),
                'tracking_number' => Tools::getValue('tracking_number'),
                'shipping_name' => Tools::getValue('shipping_name'),
            ),
        );

        // Register shipping data
        return self::registerShippingData($orders);
    }


    /**
     * Save shipping status
     * @param string $order_id
     */
    public static function registerShippingData($orders)
    {
        // Set order items array
        $order_items = array();

        // For each order
        foreach ($orders as $order)
        {
            // Retrieve order data
            $sellermania_order = Db::getInstance()->getValue('SELECT `info` FROM `'._DB_PREFIX_.'sellermania_order` WHERE `id_order` = '.(int)$order['id_order']);
            if (!empty($sellermania_order))
            {
                // Decode order data
                $sellermania_order = json_decode($sellermania_order, true);

                // Check shipping status
                $status_to_ship = self::isStatusToShip($sellermania_order);
                if ($status_to_ship == 1)
                {
                    // Preprocess data
                    foreach ($sellermania_order['OrderInfo']['Product'] as $product)
                        if ($product['Status'] == 1)
                            $order_items[] = array(
                                'orderId' => pSQL($sellermania_order['OrderInfo']['OrderId']),
                                'sku' => pSQL($product['Sku']),
                                'orderStatusId' => \Sellermania\OrderConfirmClient::STATUS_DISPATCHED,
                                'trackingNumber' => pSQL($order['tracking_number']),
                                'shippingCarrier' => pSQL($order['shipping_name']),
                            );
                }
            }
        }

        if (empty($order_items))
            return false;

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

            // Return results
            return $result;
        }
        catch (\Exception $e)
        {
            Context::getContext()->smarty->assign('sellermania_error', strip_tags($e->getMessage()));
            return false;
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
        try
        {
            $client = new Sellermania\OrderClient();
            $client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
            $client->setToken(Configuration::get('SM_ORDER_TOKEN'));
            $client->setEndpoint(Configuration::get('SM_ORDER_ENDPOINT'));
            $result = $client->getOrderById($order_id);

            // Preprocess data and fix order
            $controller = new SellermaniaImportOrderController($this->module, $this->dir_path, $this->web_path);
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
        catch (\Exception $e)
        {
            $this->context->smarty->assign('sellermania_error', strip_tags($e->getMessage()));
            return false;
        }
    }

    /**
     * Is order ready to be shipped
     * @param $sellermania_order
     * @return int flag
     */
    public static function isStatusToShip($sellermania_order)
    {
        // Check if there is a flag to dispatch
        $status_to_ship = 0;
        foreach ($sellermania_order['OrderInfo']['Product'] as $product)
            if (isset($product['Status']) && $product['Status'] == 1)
                $status_to_ship = 1;
        foreach ($sellermania_order['OrderInfo']['Product'] as $product)
            if (isset($product['Status']) && $product['Status'] != 1 && $product['Status'] != 4)
                $status_to_ship = 0;
        return $status_to_ship;
    }


    /**
     * Refresh order status
     * @param $sellermania_order
     * @return bool
     */
    public function refreshOrderStatus($id_order, $sellermania_order)
    {
        // Fix data (when only one product, array is not the same)
        if (!isset($sellermania_order['OrderInfo']['Product'][0]))
            $sellermania_order['OrderInfo']['Product'] = array($sellermania_order['OrderInfo']['Product']);

        // Check which status the order is
        $new_order_state = false;
        foreach ($this->module->sellermania_order_states as $kos => $os)
            if ($new_order_state === false)
            {
                // If the status is a priority status and one of the product has this status
                // The order will have this status
                if ($os['sm_prior'] == 1)
                {
                    foreach ($sellermania_order['OrderInfo']['Product'] as $kp => $product)
                        if (isset($product['Status']) && $product['Status'] == $os['sm_status'])
                            $new_order_state = Configuration::get($kos);
                }

                // If the status is not a priority status and all products have this status
                // The order will have this status
                if ($os['sm_prior'] == 0)
                {
                    $new_order_state = Configuration::get($kos);
                    foreach ($sellermania_order['OrderInfo']['Product'] as $kp => $product)
                        if (isset($product['Status']) && $product['Status'] != $os['sm_status'])
                            $new_order_state = false;
                }
            }

        // If all order states are either dispatched or cancel, then it's a dispatched order
        if ($new_order_state === false)
        {
            // Check if there is at least one line as "Dispatched"
            foreach ($sellermania_order['OrderInfo']['Product'] as $kp => $product)
                if ($product['Status'] == $this->module->sellermania_order_states['PS_OS_SM_DISPATCHED']['sm_status'])
                    $new_order_state = Configuration::get('PS_OS_SM_DISPATCHED');

            // If yes, we check if others states are not different of "CANCEL" or "DISPATCH"
            if ($new_order_state == Configuration::get('PS_OS_SM_DISPATCHED'))
                foreach ($sellermania_order['OrderInfo']['Product'] as $kp => $product)
                    if ($product['Status'] != $this->module->sellermania_order_states['PS_OS_SM_CANCEL_CUS']['sm_status'] &&
                        $product['Status'] != $this->module->sellermania_order_states['PS_OS_SM_CANCEL_SEL']['sm_status'] &&
                        $product['Status'] != $this->module->sellermania_order_states['PS_OS_SM_DISPATCHED']['sm_status'])
                        $new_order_state = false;
        }

        // If status is false or equal to first status assigned, we do not change it
        if ($new_order_state === false || $new_order_state == Configuration::get('PS_OS_SM_AWAITING'))
            return false;


        // We check if the status is not already set
        $id_order_history = Db::getInstance()->getValue('
        SELECT `id_order_history` FROM `'._DB_PREFIX_.'order_history`
        WHERE `id_order` = '.(int)$id_order.'
        AND `id_order_state` = '.(int)$new_order_state);
        if ($id_order_history > 0)
            return false;


        // Load order and check existings payment
        $order = new Order((int)$id_order);

        // If order does not exists anymore we stop status update
        if ($order->id < 1) {
            return false;
        }

        // Create new OrderHistory
        $history = new OrderHistory();
        $history->id_order = $order->id;
        $history->id_employee = (int)$this->context->employee->id;
        $history->id_order_state = (int)$new_order_state;
        $history->changeIdOrderState((int)$new_order_state, $order->id);
        $history->add();
    }


    /**
     * Run method
     * @return string $html
     */
    public function run()
    {
        // Retrieve order data
        $sellermania_order = Db::getInstance()->getValue('SELECT `info` FROM `'._DB_PREFIX_.'sellermania_order` WHERE `id_order` = '.(int)Tools::getValue('id_order'));
        if (empty($sellermania_order))
            return '';

        // Decode order data
        $sellermania_order = json_decode($sellermania_order, true);

        // Save order line status
        $result_status_update = $this->saveOrderStatus($sellermania_order['OrderInfo']['OrderId'], $sellermania_order);

        // Check if there is a flag to dispatch
        $result_shipping_status_update = $this->saveShippingStatus($sellermania_order);

        // Refresh order from Sellermania webservices
        $return = $this->refreshOrder($sellermania_order['OrderInfo']['OrderId']);
        if ($return !== false)
            $sellermania_order = $return;

        // Refresh flag to dispatch
        $status_to_ship = self::isStatusToShip($sellermania_order);

        // Refresh order status
        $this->refreshOrderStatus(Tools::getValue('id_order'), $sellermania_order);

        // Get order currency
        $order = new Order((int)Tools::getValue('id_order'));
        $sellermania_currency = new Currency($order->id_currency);

        // Compliancy date format with PS 1.4
        if ($this->ps_version == '14')
            $this->context->smarty->ps_language = new Language($this->context->cookie->id_lang);

        $this->context->smarty->assign('ps_version', $this->ps_version);
        $this->context->smarty->assign('sellermania_order', $sellermania_order);
        $this->context->smarty->assign('sellermania_currency', $sellermania_currency);
        $this->context->smarty->assign('sellermania_module_path', $this->web_path);
        $this->context->smarty->assign('sellermania_status_list', $this->status_list);
        $this->context->smarty->assign('sellermania_conditions_list', $this->module->sellermania_conditions_list);
        $this->context->smarty->assign('sellermania_status_to_ship', $status_to_ship);
        $this->context->smarty->assign('sellermania_status_update', $result_status_update);
        $this->context->smarty->assign('sellermania_shipping_status_update', $result_shipping_status_update);

		// Modif YB : variable pour admin par défaut
        $this->context->smarty->assign('sellermania_display_default_admin', Configuration::get('SM_ENABLE_DEFAULT_ADMIN'));
		// Fin Modif YB : variable pour admin par défaut
		
        $this->context->smarty->assign('sellermania_enable_native_refund_system', Configuration::get('SM_ENABLE_NATIVE_REFUND_SYSTEM'));

        return $this->module->compliantDisplay('displayAdminOrder.tpl');
    }
}

