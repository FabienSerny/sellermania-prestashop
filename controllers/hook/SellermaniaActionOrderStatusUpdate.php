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
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
require_once(_PS_MODULE_DIR_.'sellermania'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SellermaniaHelper.php');

class SellermaniaActionOrderStatusUpdateController
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
     * Run method
     * @return string $html
     */
    public function run()
    { 
        // Check if credentials are ok
        if (Configuration::get('SM_CREDENTIALS_CHECK') != 'ok' || Configuration::get('SM_IMPORT_ORDERS') != 'yes' || $this->module->getDefaultProductID() < 1) {
            return '';
        }
        $new_sm_status = (int)SellermaniaHelper::getSMOrderStateIdByPSOrderStateId($this->params['newOrderStatus']->id,$this->module);

        if ($new_sm_status > 0) 
        {
            $prestashop_order = new Order($this->params['id_order']);
            $sellermania_order = SellermaniaOrder::getSellermaniaOrderFromOrderId($this->params['id_order']);
            
            if (Validate::isLoadedObject($sellermania_order) && Validate::isLoadedObject($prestashop_order)) 
            {                
                $sellermania_order_info = json_decode($sellermania_order->info, true);
                $order_items_to_confirm = array();
                $carrier_name = '';
                $tracking_number = '';
                $order_items = array();
                $flag_to_ship = 0;                
                $current_order_state = $prestashop_order->getCurrentOrderState();
                $current_status = (int)SellermaniaHelper::getSMOrderStateIdByPSOrderStateId($current_order_state-> id,$this->module);
                
               
                foreach ($this->module->sellermania_order_states as $sm_orderstatus) {
                    if ($sm_orderstatus['sm_status'] == $new_sm_status ) {
                        if ($new_sm_status == \Sellermania\OrderConfirmClient::STATUS_TO_DISPATCH || $new_sm_status == \Sellermania\OrderConfirmClient::STATUS_READY_TO_SHIP) {
                            $new_sm_status = \Sellermania\OrderConfirmClient::STATUS_CONFIRMED;
                        }
                        if ($new_sm_status == \Sellermania\OrderConfirmClient::STATUS_DISPATCHED || $new_sm_status == \Sellermania\OrderConfirmClient::STATUS_AVAILABLE_IN_STORE) {
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

                            // Retrieve sleeping orders updates
                            $order_items_to_confirm = array();
                            $data = Configuration::get('SM_SLEEPING_ORDERS_UPDATES');
                            if (!empty($data)) {
                                $order_items_to_confirm = json_decode($data, true);
                            }
                            
                            if($ps_id_carrier < 1){
                                $ps_id_carrier = (int)Configuration::get('SM_IMPORT_DEFAULT_CARRIER');
                            }
                            
                            // Retrieve carrier name
                            $carrier = new Carrier((int)$ps_id_carrier);
                            if (Validate::isLoadedObject($carrier)) {
                                $carrier_name = $carrier->name;
                            }
                            $flag_to_ship = 1;
                        }
                        $order_items = SellermaniaOrderConfirmation::registerUpdatedProducts($order_items_to_confirm, $sellermania_order_info, $current_status, $new_sm_status, $carrier_name, $tracking_number);
                        
                         
                    }
                    
                } 
                // Check if there order item status to change
                if (!empty($order_items)) {
                    $result = SellermaniaOrderConfirmation::updateOrderItems($order_items);
                    
                    foreach ($result['OrderItemConfirmationStatus'] as $k => $order) {
                                                        
                            if ($order['Status'] == 'ERROR') {
                                throw new OrderException($order['Message']);
                            }
                    }
                    
                }
            }
        }
    }
}

