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

class SellermaniaOrder extends ObjectModel
{
    public $id;

    /** @var string Marketplace */
    public $marketplace;

    /** @var string Customer Name */
    public $customer_name;

    /** @var string Ref Order */
    public $ref_order;

    /** @var string Total Amount */
    public $amount_total;

    /** @var string Info */
    public $info;

    /** @var string Info */
    public $error;

    /** @var integer Order ID */
    public $id_order;

    /** @var integer Employee ID who accepted the order */
    public $id_employee_accepted;

    /** @var string Date Payment */
    public $date_payment;

    /** @var string Date Import */
    public $date_accepted;

    /** @var string Date Import */
    public $date_add;

    /** @var string IMEI number */
    public $order_imei;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'sellermania_order',
        'primary' => 'id_sellermania_order',
        'multilang' => false,
        'fields' => array(
            'marketplace'          => array('type' => 3, 'required' => true, 'size' => 128),
            'customer_name'        => array('type' => 3, 'required' => true, 'size' => 256),
            'ref_order'            => array('type' => 3, 'required' => true, 'size' => 128),
            'amount_total'         => array('type' => 3, 'required' => true, 'size' => 16),
            'info'                 => array('type' => 3, 'required' => true),
            'error'                => array('type' => 3),
            'id_order'             => array('type' => 1, 'validate' => 'isUnsignedId', 'required' => true),
            'id_employee_accepted' => array('type' => 1, 'validate' => 'isUnsignedId', 'required' => true),
            'date_payment'         => array('type' => 3, 'validate' => 'isDate'),
            'date_accepted'        => array('type' => 3, 'validate' => 'isDate'),
            'date_add'             => array('type' => 5, 'validate' => 'isDate', 'copy_post' => false),
            'order_imei'           => array('type' => 3),
        ),
    );
    /*    Can't use constant if we want to be compliant with PS 1.4
     *     const TYPE_INT = 1;
     *     const TYPE_BOOL = 2;
     *     const TYPE_STRING = 3;
     *     const TYPE_FLOAT = 4;
     *     const TYPE_DATE = 5;
     *     const TYPE_HTML = 6;
     *     const TYPE_NOTHING = 7;
     */

    /**
     * Retrieve Sellermania Order object from id_order
     * @param $id_order
     */
    public static function getSellermaniaOrderFromOrderId($id_order)
    {
        // Retrieve Sellermania order details
        $id_sellermania_order = (int)Db::getInstance()->getValue('
        SELECT `id_sellermania_order`
        FROM `'._DB_PREFIX_.'sellermania_order`
        WHERE `id_order` = '.(int)$id_order);

        // Load Sellermania order
        $sellermania_order = new SellermaniaOrder($id_sellermania_order);
        if (!ValidateCore::isLoadedObject($sellermania_order)) {
            return $sellermania_order;
        }

        // Load order details
        $sellermania_order->details = json_decode($sellermania_order->info, true);

        // Calcul VAT
        $sellermania_order->details['OrderInfo']['SubtotalVAT'] = array();
        $sellermania_order->details['OrderInfo']['PackingShippingFee'] = array('PriceWithoutVAT' => 0, 'VATPercent' => 0, 'TotalVAT' => 0);
        foreach ($sellermania_order->details['OrderInfo']['Product'] as $kp => $product) {
            if ($product['ItemName'] != 'Frais de gestion') {
            // Calcul VAT percent
            $product_vat_percent = (string)((float)($product['VatRate'] / 100));
            $shipping_vat_percent = (float)($product['VatRateShipping'] / 100);
            $sellermania_order->details['OrderInfo']['Product'][$kp]['ProductVAT']['VATPercent'] = $product_vat_percent;

            // Calcul VAT for shipping
                if (isset($product['ShippingFee']['Amount']['Price']) && $product['ShippingFee']['Amount']['Price'] > 0) {
                $shipping_vat_rate = 1 + ($shipping_vat_percent / 100);
                $sellermania_order->details['OrderInfo']['PackingShippingFee']['PriceWithoutVAT'] += ($product['ShippingFee']['Amount']['Price'] / $shipping_vat_rate) * $product['QuantityPurchased'];
                $sellermania_order->details['OrderInfo']['PackingShippingFee']['TotalVAT'] += ($product['ShippingFee']['Amount']['Price'] - ($product['ShippingFee']['Amount']['Price'] / $shipping_vat_rate));
                $sellermania_order->details['OrderInfo']['PackingShippingFee']['VATPercent'] = $shipping_vat_percent;
            }

            // Calcul product price without VAT
            $sellermania_order->details['OrderInfo']['Product'][$kp]['Amount']['PriceWithoutVAT'] = $product['Amount']['Price'] - $product['ProductVAT']['unit'];

            // Calcul amount for each VAT from products
            if (!isset($sellermania_order->details['OrderInfo']['SubtotalVAT'][$product_vat_percent])) {
                $sellermania_order->details['OrderInfo']['SubtotalVAT'][$product_vat_percent] = 0;
            }
            $sellermania_order->details['OrderInfo']['SubtotalVAT'][$product_vat_percent] += $product['ProductVAT']['unit'] * $product['QuantityPurchased'];
        }
        }

        // Check if shipping is good
        if (isset($sellermania_order->details['OrderInfo']['Transport']['Amount']['Price'])) {

            // In fact, we should use only the variable OrderInfo Transport Amount Price
            // However, no shipping rate is communicated with this value, that's why we're additionning all VAT rate on our side
            // Unfortunatly, it seems there is a problem of rounding value :-/
            // The code below try to fix it, but it's a bit dirty
            if (!isset($shipping_vat_percent)) {
                $shipping_vat_rate = 1.20;
            }
            if (empty($shipping_vat_rate)) {
                $shipping_vat_rate = 1;
            }
            $total_shipping_without_tax = round($sellermania_order->details['OrderInfo']['Transport']['Amount']['Price'] / $shipping_vat_rate, 2);
            if ($total_shipping_without_tax != $sellermania_order->details['OrderInfo']['PackingShippingFee']['PriceWithoutVAT']) {
                $sellermania_order->details['OrderInfo']['PackingShippingFee']['PriceWithoutVAT'] = $total_shipping_without_tax;
            }
        }

        // Calcul amount for each VAT from shipping
        $shipping_vat_percent = $sellermania_order->details['OrderInfo']['PackingShippingFee']['VATPercent'];
        if (!isset($sellermania_order->details['OrderInfo']['SubtotalVAT'][$shipping_vat_percent])) {
            $sellermania_order->details['OrderInfo']['SubtotalVAT'][$shipping_vat_percent] = 0;
        }
        $sellermania_order->details['OrderInfo']['SubtotalVAT'][$shipping_vat_percent] += $sellermania_order->details['OrderInfo']['PackingShippingFee']['TotalVAT'];

        return $sellermania_order;
    }


    /**
     * Search Sellermania orders by reference
     * @param $order
     */
    public static function getOrderIdBySellermaniaOrderReference($marketplace, $ref_order)
    {
        return (int)Db::getInstance()->getValue('
        SELECT `id_order`
        FROM `'._DB_PREFIX_.'sellermania_order`
        WHERE `marketplace` = \''.pSQL(trim($marketplace)).'\'
        AND `ref_order` = \''.pSQL(trim($ref_order)).'\'');
    }


    /**
     * Search Sellermania orders by reference
     * @param $order
     */
    public static function searchSellermaniaOrdersByReference($ref_order)
    {
        return Db::getInstance()->executeS('
        SELECT * FROM `'._DB_PREFIX_.'sellermania_order`
        WHERE `ref_order` LIKE \'%'.pSQL(trim($ref_order)).'%\'');
    }

    /**
     * Check if order has already been imported
     * @param $order
     */
    public static function getSellermaniaOrderId($marketplace, $ref_order)
    {
        return (int)Db::getInstance()->getValue('
        SELECT `id_sellermania_order`
        FROM `'._DB_PREFIX_.'sellermania_order`
        WHERE `marketplace` = \''.pSQL(trim($marketplace)).'\'
        AND `ref_order` = \''.pSQL(trim($ref_order)).'\'');
    }

    /**
     * update order carrier by default carrier for order importation
     *
     * @param [type] $id_order
     * @param [type] $id_carrier
     */
    public static function updateOrderCarrierByOrderId($id_order, $id_carrier)
    {
        return Db::getInstance()->execute('
        UPDATE `'._DB_PREFIX_.'order_carrier` SET `id_carrier` = '.(int)$id_carrier.' 
        WHERE `id_order` = '.(int)$id_order);
    }


    /**
     * Get Nb Sellermania orders in error
     * @return int
     */
    public static function getNbSellermaniaOrdersInError()
    {
        return (int)Db::getInstance()->getValue('
        SELECT COUNT(`id_sellermania_order`)
        FROM `'._DB_PREFIX_.'sellermania_order`
        WHERE `id_order` = 0
        AND `date_add` > \''.pSQL(date("Y-m-d H:i:s", strtotime('-15 days'))).'\'');
    }

    /**
     * Get Sellermania orders in error
     * @return array
     */
    public static function getSellermaniaOrdersInError()
    {
        $not_imported_orders = Db::getInstance()->ExecuteS('
        SELECT * FROM `'._DB_PREFIX_.'sellermania_order`
        WHERE `id_order` = 0
        AND `date_add` > \''.pSQL(date("Y-m-d H:i:s", strtotime('-15 days'))).'\'');

        $orders = array();
        foreach ($not_imported_orders as $order) {
            $order['info'] = json_decode($order['info'], true);
           // if ($order['info']['OrderInfo']['TotalAmount']['Amount']['Price'] > 0) {
                $orders[] = $order;
           // }
        }

        return $orders;
    }

    /**
     * Delete Sellermania order
     * @return bool
     */
    public static function deleteSellermaniaOrderInError($id_sellermania_order)
    {
        return Db::getInstance()->getValue('
        DELETE FROM `'._DB_PREFIX_.'sellermania_order`
        WHERE `id_order` = 0
        AND `id_sellermania_order` = '.(int)$id_sellermania_order);
    }
    public static function deleteAllSellermaniaOrdersInError()
    {
        return Db::getInstance()->execute('
        DELETE FROM `'._DB_PREFIX_.'sellermania_order`
        WHERE `id_order` = 0');
    }

    /**
     * Get tracking numbers to synchronize
     */
    public static function getTrackingNumbersToSynchronize()
    {
        $ps_os_sm_dispatched = Configuration::get('PS_OS_SM_DISPATCHED');
        if (self::isJson($ps_os_sm_dispatched)) {
            if ($ps_os_sm_dispatched) {
                $dispatched_status = implode( ", " , json_decode($ps_os_sm_dispatched));
                if ($dispatched_status != null) {
                    $sql = '
                SELECT o.`id_order`, oc.`tracking_number`, so.id_sellermania_order, so.`info`,o.`current_state`
                FROM `'._DB_PREFIX_.'orders` o
                JOIN `'._DB_PREFIX_.'sellermania_order` so ON (so.id_order = o.id_order)
                LEFT JOIN `'._DB_PREFIX_.'order_carrier` oc ON (oc.id_order = o.id_order)
                WHERE oc.`tracking_number` != \'\'
                AND (
                    o.`current_state` NOT IN ('.$dispatched_status.') OR (
                        o.`current_state` IN ('.$dispatched_status.') AND so.`info` NOT LIKE \'%TrackingNumber%\'
                    )
                )
            ';

                    return Db::getInstance()->executeS($sql);
                }
            }
        }

        return [];
    }

    public static function isJson($str) {
        $json = json_decode($str);
        return $json && $str != $json;
    }

    /*** Retrocompatibility 1.4 ***/

    protected     $fieldsRequired = array('marketplace', 'ref_order', 'id_order');
    protected     $fieldsSize = array('marketplace' => 128, 'ref_order' => 128, 'id_order' => 32);
    protected     $fieldsValidate = array('id_order' => 'isUnsignedInt');

    protected     $table = 'sellermania_order';
    protected     $identifier = 'id_sellermania_order';

    public    function getFields()
    {
        if (version_compare(_PS_VERSION_, '1.5') >= 0)
            return parent::getFields();

        parent::validateFields();

        $fields['marketplace'] = pSQL($this->marketplace);
        $fields['customer_name'] = pSQL($this->customer_name);
        $fields['ref_order'] = pSQL($this->ref_order);
        $fields['amount_total'] = pSQL($this->amount_total);
        $fields['info'] = pSQL($this->info);
        $fields['error'] = pSQL($this->error);
        $fields['id_order'] = (int)$this->id_order;
        $fields['order_imei'] = pSQL($this->order_imei);
        $fields['id_employee_accepted'] = (int)$this->id_employee_accepted;
        $fields['date_payment'] = pSQL($this->date_payment);
        $fields['date_accepted'] = pSQL($this->date_accepted);
        $fields['date_add'] = pSQL($this->date_add);

        return $fields;
    }
}
