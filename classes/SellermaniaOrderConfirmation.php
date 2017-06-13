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

class SellermaniaOrderConfirmation
{
    public static function registerAutoConfirmProducts($order_items_to_confirm, $order)
    {
        if (Configuration::get('SM_MKP_'.str_replace('.', '_', $order['OrderInfo']['MarketPlace'])) == 'AUTO') {
            $current_sm_status = \Sellermania\OrderConfirmClient::STATUS_TO_BE_CONFIRMED;
            $new_sm_status = \Sellermania\OrderConfirmClient::STATUS_CONFIRMED;
            $order_items_to_confirm = SellermaniaOrderConfirmation::registerUpdatedProducts($order_items_to_confirm, $order, $current_sm_status, $new_sm_status);
        }
        return $order_items_to_confirm;
    }

    public static function registerBulkConfirmProducts($order_items_to_confirm, $order)
    {
        $current_sm_status = \Sellermania\OrderConfirmClient::STATUS_TO_BE_CONFIRMED;
        $new_sm_status = \Sellermania\OrderConfirmClient::STATUS_CONFIRMED;
        return SellermaniaOrderConfirmation::registerUpdatedProducts($order_items_to_confirm, $order, $current_sm_status, $new_sm_status);
    }

    public static function registerBulkSendProducts($order_items_to_confirm, $order, $carrier_name)
    {
        $current_sm_status = \Sellermania\OrderConfirmClient::STATUS_TO_DISPATCH;
        $new_sm_status = \Sellermania\OrderConfirmClient::STATUS_DISPATCHED;
        return SellermaniaOrderConfirmation::registerUpdatedProducts($order_items_to_confirm, $order, $current_sm_status, $new_sm_status, $carrier_name);
    }

    public static function registerUpdatedProducts($order_items_to_confirm, $order, $current_sm_status, $new_sm_status, $shipping_carrier = '', $tracking_number = '')
    {
        // Fix data (when only one product, array is not the same)
        if (!isset($order['OrderInfo']['Product'][0])) {
            $order['OrderInfo']['Product'] = array($order['OrderInfo']['Product']);
        }

        foreach ($order['OrderInfo']['Product'] as $kp => $product) {
            if ($order['OrderInfo']['Product'][$kp]['Status'] == $current_sm_status) {
                $order_items_to_confirm[] = array(
                    'orderId' => pSQL($order['OrderInfo']['OrderId']),
                    'sku' => pSQL($product['Sku']),
                    'orderStatusId' => $new_sm_status,
                    'trackingNumber' => $tracking_number,
                    'shippingCarrier' => $shipping_carrier,
                );
            }
        }
        return $order_items_to_confirm;
    }

    public static function updateOrderItems($order_items)
    {
        // Make API call
        try
        {
            // Calling the confirmOrder service
            $client = new Sellermania\OrderConfirmClient();
            $client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
            $client->setToken(Configuration::get('SM_ORDER_TOKEN'));
            $client->setEndpoint(Configuration::get('SM_CONFIRM_ORDER_ENDPOINT'));
            $result = $client->confirmOrder($order_items);

            // Fix data (when only one result, array is not the same)
            if (!isset($result['OrderItemConfirmationStatus'][0])) {
                $result['OrderItemConfirmationStatus'] = array($result['OrderItemConfirmationStatus']);
            }

            // Get PrestaShop Order ID
            foreach ($result['OrderItemConfirmationStatus'] as $k => $v) {
                $id_order_prestashop = '';
                $orders_found = SellermaniaOrder::searchSellermaniaOrdersByReference($v['orderId']);
                if (isset($orders_found[0]['id_order'])) {
                    $id_order_prestashop = $orders_found[0]['id_order'];
                }
                $result['OrderItemConfirmationStatus'][$k]['id_order_prestashop'] = $id_order_prestashop;
            }

            // Return results
            return $result;
        }
        catch (\Exception $e)
        {
            echo strip_tags($e->getMessage())."\n";
            //$this->context->smarty->assign('sellermania_error', strip_tags($e->getMessage()));
        }
    }
}