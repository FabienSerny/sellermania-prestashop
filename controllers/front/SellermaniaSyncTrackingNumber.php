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

// Load ImportOrder Controller
require_once(dirname(__FILE__).'/../hook/SellermaniaImportOrder.php');
require_once(dirname(__FILE__).'/../hook/SellermaniaDisplayAdminOrder.php');


class SellermaniaSyncTrackingNumberController
{
    public $verbose = true;
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
     * Run method
     */
    public function run()
    {
        // Init
        global $argv;
        $argument_key = '';
        if (isset($argv[0]))
            $argument_key = Configuration::get('SELLERMANIA_KEY');

        // Set _PS_ADMIN_DIR_ define and set default Shop
        if (!defined('_PS_ADMIN_DIR_'))
            define('_PS_ADMIN_DIR_', getcwd());

        // Check if Sellermania key exists
        if (Configuration::get('SELLERMANIA_KEY') == '') {
            die('ERROR1');
        }
        if (Tools::getValue('k') == '' && $argument_key == '') {
            die('ERROR2');
        }

        // Check if key is good
        if (Tools::getValue('k') == Configuration::get('SELLERMANIA_KEY') || $argument_key == Configuration::get('SELLERMANIA_KEY')) {
            
            // Up time and memory limit
            set_time_limit(600);
            ini_set('memory_limit', '256M');
            
             // Check connection before going further
            try
            {
                $test = new SellermaniaTestAPI();
                $test->run();
            }
            catch (\Exception $e)
            {
                $log = date('Y-m-d H:i:s').': '.$e->getMessage()."\n";
                $this->speak('Tracking Number EXCEPTION: '.$log);
                $this->module->logger('webservice-error-Tracking-Number-Sync', $log);
                return false;
            }
            
            $this->syncTrackingNumber();
        }
        else {
            die('ERROR3');
        }
    }
    public function syncTrackingNumber()
    {
        if (Configuration::get('SM_IMPORT_AC_ORDERS_AFTER_ADDING_TRACKING_NUMBER') != 'on') 
            die('Please enable "Auto-shipment orders after adding tracking number" option and try again');
            
        // Calcul number of tracking number to synchronize
        $sm_tracking_numbers_to_synchronize = [];
        if (version_compare(_PS_VERSION_, '1.5') >= 0) {
            $sm_tracking_numbers_to_synchronize = SellermaniaOrder::getTrackingNumbersToSynchronize();
            $this->speak('Synchronizing Tracking Numbers, please wait...');
            $orders_count = 0;
            $order_message = '';
            foreach ($sm_tracking_numbers_to_synchronize as $stnts) {
                
                $info = json_decode($stnts['info'], true);                
                $sellermania_order = $info;
                              
                if (!empty($info) && (!isset($info['OrderInfo']['Transport']['TrackingNumber']) || empty($info['OrderInfo']['Transport']['TrackingNumber']))) 
                {
                    $prestashop_order = new Order($stnts['id_order']);
                    // Retrieve tracking number
                    $ps_id_carrier = 0;
                    if (version_compare(_PS_VERSION_, '1.5') < 0 || !method_exists($prestashop_order, 'getIdOrderCarrier')) {
                        $tracking_number = $prestashop_order->shipping_number;
                    } else {
                        $id_order_carrier = $prestashop_order->getIdOrderCarrier();
                        $order_carrier = new OrderCarrier($id_order_carrier);
                        $tracking_number = $order_carrier->tracking_number;
                        $ps_id_carrier = $order_carrier->id_carrier;
                    }
                    if($ps_id_carrier < 1){
                        $ps_id_carrier = (int)Configuration::get('SM_IMPORT_DEFAULT_CARRIER');
                    }
                    // Retrieve products from order
                    $current_sm_status = \Sellermania\OrderConfirmClient::STATUS_TO_DISPATCH;
                    $new_sm_status = \Sellermania\OrderConfirmClient::STATUS_DISPATCHED;
                    $order_items_to_confirm = array();
                    $order_items_to_confirm = SellermaniaOrderConfirmation::registerUpdatedProducts($order_items_to_confirm, $info, $current_sm_status, $new_sm_status, $ps_id_carrier, $tracking_number);
                    $orders_count++;
                    
                    // Check if there order item status to change
                    if (!empty($order_items_to_confirm)  && (Configuration::get('SM_IMPORT_AC_ORDERS_AFTER_ADDING_TRACKING_NUMBER') == 'on')) {
                        $result = SellermaniaOrderConfirmation::updateOrderItems($order_items_to_confirm);
                        foreach ($result['OrderItemConfirmationStatus'] as $k => $order) {
                                                        
                            if ($order['Status'] == 'SUCCESS') {
                                $message = 'Updated ';
                                
                                // Refresh order from Sellermania webservices
                                $sdao = new SellermaniaDisplayAdminOrderController($this->module, $this->dir_path, $this->web_path);

                                $return = $this->refreshOrder($order['orderId'], $stnts['id_order']);
                                if ($return !== false) {
                                    $sellermania_order = $return;
                                }
                                // Refresh flag to dispatch
                                $status_to_ship = $sdao->isStatusToShip($sellermania_order);

                                // Refresh order status
                                $sdao->refreshOrderStatus($stnts['id_order'], $sellermania_order);
                            } else {
                                $message = 'Not Updated '.$order['Status'].' : '.$order['Message'];
                            }
                            $order_message .= 'Tracking Number '.$tracking_number.' for Order #'.$order['orderId'].' : ' .$message.'<br/>';
                        }
                    } else {
                        $order_message .= 'Order #'. $info['OrderInfo']['OrderId'] .' Not updated please check the order status';
                    }
                }
                
            }
            $this->speak($orders_count.' orders retrieved.<br/> ');
            $this->speak($order_message);
        }
    }
    /**
     * Refresh order
     * @param string $order_id
     * @return mixed array data
     */
    public function refreshOrder($sm_order_id, $ps_order_id)
    {
        // Retrieving data
        try
        {
            $client = new Sellermania\OrderClient();
            $client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
            $client->setToken(Configuration::get('SM_ORDER_TOKEN'));
            $client->setEndpoint(Configuration::get('SM_ORDER_ENDPOINT'));
            $result = $client->getOrderById($sm_order_id);

            // Preprocess data and fix order
            $controller = new SellermaniaImportOrderController($this->module, $this->dir_path, $this->web_path);
            $controller->data = $result['SellermaniaWs']['GetOrderResponse']['Order'];
            $controller->preprocessData();
            $controller->order = new Order((int)$ps_order_id);
            $controller->fixOrder(true);

            // Saving it
            $id_sellermania_order = Db::getInstance()->getValue('SELECT `id_sellermania_order` FROM `'._DB_PREFIX_.'sellermania_order` WHERE `id_order` = '.(int)$ps_order_id);
            $sellermania_order = new SellermaniaOrder($id_sellermania_order);
            $sellermania_order->info = json_encode($controller->data);
            $sellermania_order->date_accepted = NULL;
            $sellermania_order->update();

            // Return data
            return $controller->data;
        }
        catch (\Exception $e)
        {            
            $this->speak('ERROR: '.strip_tags($e->getMessage()));
            return false;
        }
    }
}

