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
    public static function registerAutoConfirmProducts($order_items_to_confirm, $order, $sm_status)
    {
        if (Configuration::get('SM_MARKETPLACE_'.str_replace('.', '_', $order['OrderInfo']['MarketPlace'])) == 'AUTO') {
            foreach ($order['OrderInfo']['Product'] as $kp => $product)
                if ($order['OrderInfo']['Product'][$kp]['Status'] == \Sellermania\OrderConfirmClient::STATUS_TO_BE_CONFIRMED)
                {
                    $order_items_to_confirm[] = array(
                        'orderId' => pSQL($order['OrderInfo']['OrderId']),
                        'sku' => pSQL($product['Sku']),
                        'orderStatusId' => $sm_status,
                        'trackingNumber' => '',
                        'shippingCarrier' => '',
                    );
                }
        }

        return $order_items_to_confirm;
    }

    public static function confirmOrderItems($order_items)
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

            // Return results
            return $result;
        }
        catch (\Exception $e)
        {
            $this->context->smarty->assign('sellermania_error', strip_tags($e->getMessage()));
        }
    }
}