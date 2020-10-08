<?php
/*
* 2010-2020 Sellermania / Froggy Commerce / 23Prod SARL
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
*  @copyright      2010-2020 Sellermania / Froggy Commerce / 23Prod SARL
*  @version        1.0
*  @license        http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

/*
 * Security
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

// Load ImportOrder Controller and DisplayAdminOrderController
require_once(dirname(__FILE__).'/SellermaniaImportOrder.php');
require_once(dirname(__FILE__).'/SellermaniaDisplayAdminOrder.php');


// Load ValidateOrder Controller
require_once(dirname(__FILE__).'/SellermaniaActionValidateOrder.php');


class SellermaniaDisplayBackOfficeHeaderController
{
    public $verbose = false;
    public $order_items_to_confirm = array();


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
     * Module can speak when verbose mode is enabled (for the moment only for orders importation via command line)
     * @param $string
     */
    public function speak($string)
    {
        if ($this->verbose) {
            echo $string."\n";
        }
    }

    /**
     * Import Sellermania orders
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
        $date_start = date("Y-m-d H:i:s", strtotime('-'.((int)Configuration::get('SM_ORDER_IMPORT_PAST_DAYS')).' days'));
        $date_end = date('Y-m-d H:i:s');
        if ($date_start < Configuration::get('SM_INSTALL_DATE'))
            $date_start = Configuration::get('SM_INSTALL_DATE');

        try
        {
            // Verbose mode
            $this->speak('Import orders from '.$date_start.' to '.$date_end.', please wait...');

            // Recovering dispatched orders for the last 30 days
            $result = $client->getOrderByDate(
                new \DateTime($date_start),
                new \DateTime($date_end)
            );

            // Import order
            if (isset($result['SellermaniaWs']['GetOrderResponse']['Order']))
            {
                // Fix data (when only one order, array is not the same)
                if (!isset($result['SellermaniaWs']['GetOrderResponse']['Order'][0])) {
                    $result['SellermaniaWs']['GetOrderResponse']['Order'] = array($result['SellermaniaWs']['GetOrderResponse']['Order']);
                }

                // Verbose mode
                $this->speak(count($result['SellermaniaWs']['GetOrderResponse']['Order']).' orders retrieved');

                // Import order
                foreach ($result['SellermaniaWs']['GetOrderResponse']['Order'] as $order)
                    if (isset($order['OrderInfo']['OrderId']) &&
                        Configuration::get('SM_MKP_'.str_replace('.', '_', $order['OrderInfo']['MarketPlace'])) != 'NO')
                    {
                        // Verbose mode
                        $this->speak('Import order #'.$order['OrderInfo']['OrderId'].' from '.$order['OrderInfo']['MarketPlace']);

                        // Check if order exists
                        $id_sellermania_order = SellermaniaOrder::getSellermaniaOrderId($order['OrderInfo']['MarketPlace'], $order['OrderInfo']['OrderId']);
                        if ($id_sellermania_order > 0)
                        {
                            // Verbose mode
                            $this->speak('Order exists, we update it');

                            // If do exist and associate to a PrestaShop order, we update order status
                            $smo = new SellermaniaOrder((int)$id_sellermania_order);
                            if ($smo->id_order > 0)
                            {
                                try
                                {
                                    // Import order as PrestaShop order
                                    $import_order = new SellermaniaImportOrderController($this->module, $this->dir_path, $this->web_path);
                                    $import_order->refreshOrder($smo, $order);

                                    $sdao = new SellermaniaDisplayAdminOrderController($this->module, $this->dir_path, $this->web_path);
                                    $sdao->refreshOrderStatus($smo->id_order, $order);
                                }
                                catch (\Exception $e)
                                {
                                    // Log error
                                    $log = '[UPDATE] - '.date('Y-m-d H:i:s').': '.$e->getMessage()."\n";
                                    $this->speak('EXCEPTION: '.$log);
                                    $log .= var_export($order, true)."\n";
                                    $this->module->logger('order-error', $log);
                                }
                            }
                        }
                        else
                        {
                            // Verbose mode
                            $this->speak('Order does not exist, we create it');

                            // Save config value
                            $ps_guest_checkout_enabled = Configuration::get('PS_GUEST_CHECKOUT_ENABLED');
                            Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', 1);
                            $ps_order_out_of_stock = Configuration::get('PS_ORDER_OUT_OF_STOCK');
                            Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', 1);

                            // If does not exist, we import the order
                            try
                            {
                                // Import order as PrestaShop order
                                $import_order = new SellermaniaImportOrderController($this->module, $this->dir_path, $this->web_path);
                                $import_order->run($order);
                                $count_order++;

                                // Refresh order status immediately
                                $sdao = new SellermaniaDisplayAdminOrderController($this->module, $this->dir_path, $this->web_path);
                                $sdao->refreshOrderStatus($import_order->order->id, $order);
                            }
                            catch (\Exception $e)
                            {
                                // Import order as error
                                $import_order = new SellermaniaImportOrderController($this->module, $this->dir_path, $this->web_path);
                                $import_order->data = $order;
                                $import_order->preprocessData();
                                if (!isset($import_order->order->id))
                                    $import_order->order = (object)$import_order->order;
                                $import_order->order->id = 0;
                                $import_order->saveSellermaniaOrder($e->getMessage());

                                // Log error
                                $log = '[INSERT] - '.date('Y-m-d H:i:s').': '.$e->getMessage()."\n";
                                $this->speak('EXCEPTION: '.$log);
                                $log .= var_export($order, true)."\n";
                                $this->module->logger('order-error', $log);
                            }

                            // Restore config value
                            Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', $ps_guest_checkout_enabled);
                            Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', $ps_order_out_of_stock);

                            // Register order that needs to be autoconfirm
                            $this->order_items_to_confirm = SellermaniaOrderConfirmation::registerAutoConfirmProducts($this->order_items_to_confirm, $order);

                            // Do not push it too hard
                            if ($count_order > (int)Configuration::get('SM_ORDER_IMPORT_LIMIT')) {
                                SellermaniaOrderConfirmation::updateOrderItems($this->order_items_to_confirm);
                                return true;
                            }
                        }
                    }
            }
        }
        catch (\Exception $e)
        {
            $log = date('Y-m-d H:i:s').': '.$e->getMessage()."\n";
            $this->speak('EXCEPTION: '.$log);
            $this->module->logger('webservice-error', $log);
        }
        SellermaniaOrderConfirmation::updateOrderItems($this->order_items_to_confirm);
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
        if (Tools::getValue('ajax') != '') {
            return '';
        }

        // Check import method, if cron, we disable automatic import to avoid multiple import
        if (Configuration::get('SM_IMPORT_METHOD') == 'cron') {
            return '';
        }

        // Check if it's time to import
        if ($this->timeToImportOrders()) {
            $this->importOrders();
        }

    }


    /**
     * Handle product quantity update
     */
    public function handleProductQuantityUpdate()
    {
        // We retrieve GET and POST values
        $id_product = (int)Tools::getValue('id_product');
        $id_product_attribute = (int)Tools::getValue('id_product_attribute');
        $id_lang = (int)$this->context->cookie->id_lang;

        // We retrieve the product
        $product = new Product((int)$id_product, false, $id_lang);

        // Retrieve SKU and quantity depending of PS version
        if (Tools::getValue('controller') == 'AdminProducts' && Tools::getValue('actionQty') == 'set_qty' && $id_product > 0)
        {
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

            // We calcul the new quantity
            $new_quantity = (int)Tools::getValue('value');
            $difference = $new_quantity - $current_quantity;
        }
        else if (Tools::getValue('tab') == 'AdminCatalog' && $id_product > 0 && Tools::getValue('id_mvt_reason') > 0)
        {
            // We retrieve the SKU and current quantity
            if ($id_product_attribute > 0 && Tools::getValue('attribute_mvt_quantity') > 0)
            {
                $attrs = $product->getAttributeCombinaisons($id_lang);
                foreach ($attrs as $attr)
                    if ($attr['id_product_attribute'] == $id_product_attribute)
                        $sku_value = $attr['reference'];
                $difference = (int)Tools::getValue('attribute_mvt_quantity');
                if (!in_array(Tools::getValue('id_mvt_reason'), array(1, 5)))
                    $difference = - ($difference);
            }
            else if (Tools::getValue('mvt_quantity') > 0)
            {
                $sku_value = $product->reference;
                $difference = (int)Tools::getValue('mvt_quantity');
                if (!in_array(Tools::getValue('id_mvt_reason'), array(1, 5)))
                    $difference = - ($difference);
            }
        }

        // If no SKU, we stop
        if (!isset($sku_value) || empty($sku_value))
            return false;

        // We synchronize the stock
        $skus_quantities = array($sku_value => $difference);
        $skus = array($sku_value);
        $savo = new SellermaniaActionValidateOrderController($this->module, $this->dir_path, $this->web_path);
        $savo->syncStock('INVENTORY', $id_product.'-'.$id_product_attribute, $skus, $skus_quantities);
    }


    /**
     * Handle order bulk actions
     */
    public function handleOrderBulkActionsUpdate()
    {
        // Retrieve SKU and quantity depending of PS version
        if (Tools::getValue('controller') == 'AdminOrders' &&
            Tools::getIsset('sellermania_bulk_action') &&
            Tools::getIsset('sellermania_selected_orders'))
        {
            $order_items_to_confirm = array();
            $selected_orders = json_decode(Tools::getValue('sellermania_selected_orders'), true);

            $return = array('result' => 'KO');
            $return['orders'] = $selected_orders;

            if (Tools::getValue('sellermania_bulk_action') == 'bulk-confirm-orders') {

                foreach ($selected_orders as $id_order) {
                    $sellermania_order = SellermaniaOrder::getSellermaniaOrderFromOrderId($id_order);
                    $sellermania_order_info = json_decode($sellermania_order->info, true);
                    $order_items_to_confirm = SellermaniaOrderConfirmation::registerBulkConfirmProducts($order_items_to_confirm, $sellermania_order_info);
                }
                $result_details = SellermaniaOrderConfirmation::updateOrderItems($order_items_to_confirm);

                $return['result'] = 'OK';
                $return['action'] = 'bulk-confirm-orders';
                $return['result_details'] = $result_details;
            }

            if (Tools::getValue('sellermania_bulk_action') == 'bulk-send-orders') {

                foreach ($selected_orders as $id_order) {
                    $sellermania_order = SellermaniaOrder::getSellermaniaOrderFromOrderId($id_order);
                    $sellermania_order_info = json_decode($sellermania_order->info, true);
                    $order_items_to_confirm = SellermaniaOrderConfirmation::registerBulkSendProducts($order_items_to_confirm, $sellermania_order_info, Tools::getValue('sellermania_carrier'));
                }
                $result_details = SellermaniaOrderConfirmation::updateOrderItems($order_items_to_confirm);

                $return['result'] = 'OK';
                $return['action'] = 'bulk-send-orders';
                $return['result_details'] = $result_details;
            }

            die(json_encode($return));
        }
    }


    public function handleSleepingOrdersUpdates()
    {
        $data = Configuration::get('SM_SLEEPING_ORDERS_UPDATES');
        if (!empty($data)) {
            Configuration::updateValue('SM_SLEEPING_ORDERS_UPDATES', '');
            $order_items_to_confirm = json_decode($data, true);
            $result_details = SellermaniaOrderConfirmation::updateOrderItems($order_items_to_confirm);
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
            $orders_in_error = SellermaniaOrder::getSellermaniaOrdersInError();
            $nb_orders_in_error = count($orders_in_error);
            $this->context->smarty->assign('nb_orders_in_error', $nb_orders_in_error);

            $this->context->smarty->assign('ps_version', $this->ps_version);
            $this->context->smarty->assign('sellermania_module_path', $this->web_path);
            $this->context->smarty->assign('sellermania_invoice_url', FroggyLib::getAdminLink('AdminModules').'&configure=sellermania&module_name=sellermania&display=invoice');
            $this->context->smarty->assign('sellermania_admin_orders_url', FroggyLib::getAdminLink('AdminOrders'));

            $carrier_name = '';
            $carrier = new Carrier((int)Configuration::get('SM_IMPORT_DEFAULT_CARRIER'));
            if (Validate::isLoadedObject($carrier)) {
                $carrier_name = $carrier->name;
            }
            $this->context->smarty->assign('sellermania_default_carrier', $carrier_name);

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
        $this->handleSleepingOrdersUpdates();
        $this->handleOrderBulkActionsUpdate();
        return $this->handleSellermaniaOrderDisplay();
    }
}

