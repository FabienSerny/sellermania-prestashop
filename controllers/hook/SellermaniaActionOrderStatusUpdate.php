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
            $sellermania_order = SellermaniaOrder::getSellermaniaOrderFromOrderId($this->params['id_order']);
            if (Validate::isLoadedObject($sellermania_order)) {

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
                $order_items_to_confirm = SellermaniaOrderConfirmation::registerBulkSendProducts($order_items_to_confirm, $sellermania_order_info, $carrier_name);

                // Save sleeping orders updates
                Configuration::updateValue('SM_SLEEPING_ORDERS_UPDATES', json_encode($order_items_to_confirm));
            }
        }
    }
}

