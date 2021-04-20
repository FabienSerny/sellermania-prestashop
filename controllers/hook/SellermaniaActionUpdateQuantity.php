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

// Load SellermaniaActionValidateOrder Controller
require_once(dirname(__FILE__).'/SellermaniaActionValidateOrder.php');

class SellermaniaActionUpdateQuantityController
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
        if (Configuration::get('SM_CREDENTIALS_CHECK') != 'ok' || Configuration::get('SM_IMPORT_ORDERS') != 'yes' || Configuration::get('SM_DEFAULT_PRODUCT_ID') < 1)
            return '';

        // If we are in a order creation context, we abort (a request will be done in the hookValidateOrder)
        if (isset($this->params['cart']) && Validate::isLoadedObject($this->params['cart'])) {
            return '';
        }

        // Init
        $id_product = $this->params['id_product'];
        $id_product_attribute = $this->params['id_product_attribute'];

        // Check wether it's a combination or a main product
        if ($id_product_attribute < 1) {

            // If product has combinations, no need to send this stock update to Sellermania
            $has_combination = Db::getInstance()->getValue('
            SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute`
            WHERE `id_product` = '.(int)$id_product);
            if ($has_combination > 0) {
                return '';
            }

            // If not we retrieve reference and stock
            $sku_value = Db::getInstance()->getValue('
            SELECT `reference` FROM `'._DB_PREFIX_.'product`
            WHERE `id_product` = '.(int)$id_product);
            $new_quantity = StockAvailable::getQuantityAvailableByProduct($id_product);

        } else {

            // We retrieve reference and stock
            $sku_value = Db::getInstance()->getValue('
            SELECT `reference` FROM `'._DB_PREFIX_.'product_attribute`
            WHERE `id_product` = '.(int)$id_product.' AND `id_product_attribute` = '.(int)$id_product_attribute);
            $new_quantity = StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute);
        }

        // We synchronize the stock
        $skus_quantities = array($sku_value => $new_quantity);
        $skus = array($sku_value);
        $savo = new SellermaniaActionValidateOrderController($this->module, $this->dir_path, $this->web_path);
        $savo->syncStock('INVENTORY-QTY-HOOK', $id_product.'-'.$id_product_attribute, $skus, $skus_quantities);

        // Update product date upd for compliancy with some modules
        if (Configuration::get('SM_UPDATE_PRODUCT_DATE_UPD') == 'yes') {
            Db::getInstance()->update('product', [ 'date_upd' => date('Y-m-d H:i:s') ], '`id_product` = '.(int)$id_product);
            if (version_compare(_PS_VERSION_, '1.5') >= 0) {
                Db::getInstance()->update('product_shop', [ 'date_upd' => date('Y-m-d H:i:s') ], '`id_product` = '.(int)$id_product);
            }
        }
    }
}

