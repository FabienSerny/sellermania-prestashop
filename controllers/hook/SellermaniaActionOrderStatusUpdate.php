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
        if (Configuration::get('SM_CREDENTIALS_CHECK') != 'ok' || Configuration::get('SM_IMPORT_ORDERS') != 'yes' || Configuration::get('SM_DEFAULT_PRODUCT_ID') < 1) {
            return '';
        }

        if ($this->params['newOrderStatus']->id == Configuration::get('PS_OS_SM_DISPATCHED')) {

            $prestashop_order = new Order($this->params['id_order']);
            $sellermania_order = SellermaniaOrder::getSellermaniaOrderFromOrderId($this->params['id_order']);

            if (Validate::isLoadedObject($sellermania_order) && Validate::isLoadedObject($prestashop_order)) {

                // Retrieve tracking number
                if (version_compare(_PS_VERSION_, '1.5') < 0 || !method_exists($prestashop_order, 'getIdOrderCarrier')) {
                    $tracking_number = $prestashop_order->shipping_number;
                } else {
                    $id_order_carrier = $prestashop_order->getIdOrderCarrier();
                    $order_carrier = new OrderCarrier($id_order_carrier);
                    $tracking_number = $order_carrier->tracking_number;
                }

                // Retrieve sleeping orders updates
                $order_items_to_confirm = array();
                $data = Configuration::get('SM_SLEEPING_ORDERS_UPDATES');
                if (!empty($data)) {
                    $order_items_to_confirm = json_decode($data, true);
                }

                // Retrieve default carrier
                $carrier_name = '';
                $carrier = new Carrier((int)Configuration::get('SM_IMPORT_DEFAULT_CARRIER'));
                if (Validate::isLoadedObject($carrier)) {
                    $carrier_name = $carrier->name;
                }

                // Retrieve products from order
                $sellermania_order_info = json_decode($sellermania_order->info, true);
                $current_sm_status = \Sellermania\OrderConfirmClient::STATUS_TO_DISPATCH;
                $new_sm_status = \Sellermania\OrderConfirmClient::STATUS_DISPATCHED;
                $order_items_to_confirm = SellermaniaOrderConfirmation::registerUpdatedProducts($order_items_to_confirm, $sellermania_order_info, $current_sm_status, $new_sm_status, $carrier_name, $tracking_number);

                // Save sleeping orders updates
                Configuration::updateValue('SM_SLEEPING_ORDERS_UPDATES', json_encode($order_items_to_confirm));
            }
        }
    }
}

