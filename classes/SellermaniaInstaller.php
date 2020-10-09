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

class SellermaniaInstaller
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * Install method
     * @return boolean success
     */
    public function install()
    {
        // Execute module install MySQL commands
        $sql_file = dirname(__FILE__).'/../install/install.sql';
        if (!$this->loadSQLFile($sql_file)) {
            return false;
        }

        // Install Order States
        $this->installOrderStates();

        // Install Product
        $this->installSellermaniaProduct();

        // Gen Sellermania key
        Configuration::updateValue('SM_VERSION', $this->module->version);
        Configuration::updateValue('SM_INSTALL_DATE', date('Y-m-d H:i:s'));
        Configuration::updateValue('SELLERMANIA_KEY', md5(rand()._COOKIE_KEY_.date('YmdHis')));
        Configuration::updateValue('SM_ORDER_ENDPOINT', 'http://api.sellermania.com/OrdersAPISFR_07_03_2014/OrderAPIS?wsdl');
        Configuration::updateValue('SM_CONFIRM_ORDER_ENDPOINT', 'http://membres.sellermania.com/wsapi/wsdl/OrderConfirmation');
        Configuration::updateValue('SM_INVENTORY_ENDPOINT', 'http://api.sellermania.com/InventoryAPISFR_11_12_2017/InventoryAPIS?wsdl');

        Configuration::updateValue('SM_ENABLE_NATIVE_REFUND_SYSTEM', 'no');
        Configuration::updateValue('SM_ENABLE_EXPORT_COMB_NAME', 'yes');

        return true;
    }



    /**
     * Uninstall method
     * @return boolean success
     */
    public function uninstall()
    {
        // Execute module install MySQL commands
        // $sql_file = dirname(__FILE__).'/../install/uninstall.sql';
        // if (!$this->loadSQLFile($sql_file))
        //    return false;

        // Delete configuration values
        Configuration::deleteByName('SM_SLEEPING_UPDATES');
        Configuration::deleteByName('SM_IMPORT_ORDERS');
        Configuration::deleteByName('SM_ORDER_EMAIL');
        Configuration::deleteByName('SM_ORDER_TOKEN');
        Configuration::deleteByName('SM_ORDER_ENDPOINT');
        Configuration::deleteByName('SM_CONFIRM_ORDER_ENDPOINT');
        Configuration::deleteByName('SM_INVENTORY_ENDPOINT');
        Configuration::deleteByName('SM_NEXT_IMPORT');
        Configuration::deleteByName('SM_CREDENTIALS_CHECK');
        Configuration::deleteByName('SM_INSTALL_DATE');
        Configuration::deleteByName('SELLERMANIA_KEY');

        Configuration::deleteByName('SM_STOCK_SYNC_OPTION');
        Configuration::deleteByName('SM_STOCK_SYNC_POSITION');
        Configuration::deleteByName('SM_STOCK_SYNC_NB_CHAR');

        Configuration::deleteByName('SM_STOCK_SYNC_OPTION');
        Configuration::deleteByName('SM_STOCK_SYNC_OPTION_1');
        Configuration::deleteByName('SM_STOCK_SYNC_OPTION_2');
        Configuration::deleteByName('SM_STOCK_SYNC_POSITION');
        Configuration::deleteByName('SM_STOCK_SYNC_NB_CHAR');

        Configuration::deleteByName('SM_ALERT_MISSING_REF_OPTION');
        Configuration::deleteByName('SM_ALERT_MISSING_REF_MAIL');

        Configuration::deleteByName('SM_ENABLE_NATIVE_REFUND_SYSTEM');
        Configuration::deleteByName('SM_ENABLE_EXPORT_COMB_NAME');

        Configuration::deleteByName('SM_EXPORT_STAY_NB_DAYS');

        return true;
    }



    /**
     * Load SQL file
     * @return boolean success
     */
    public function loadSQLFile($sql_file)
    {
        // Get install MySQL file content
        $sql_content = file_get_contents($sql_file);

        // Replace prefix and store MySQL command in array
        $sql_content = str_replace('PREFIX_', _DB_PREFIX_, $sql_content);
        $sql_requests = preg_split("/;\s*[\r\n]+/", $sql_content);

        // Execute each MySQL command
        $result = true;
        foreach($sql_requests AS $request)
            if (!empty($request))
                $result &= Db::getInstance()->execute(trim($request));

        // Return result
        return $result;
    }



    public function isModuleRegisteredOnHook($module_instance, $hook_name, $id_shop)
    {
        $prefix = _DB_PREFIX_;
        $id_hook = (int)Hook::getIdByName($hook_name);
        $id_shop = (int) $id_shop;
        $id_module = (int) $module_instance->id;

        $sql = "SELECT * FROM {$prefix}hook_module
                  WHERE `id_hook` = {$id_hook}
                  AND `id_module` = {$id_module}
                  AND `id_shop` = {$id_shop}";

        $rows = Db::getInstance()->executeS($sql);

        return !empty($rows);
    }

    /**
     *  Module upgrade
     */
    public function upgrade()
    {
        $version_registered = Configuration::get('SM_VERSION');

        if ($version_registered == '' || version_compare($version_registered, '1.0.0', '<')) {
            if ((int)Configuration::get('PS_OS_SM_SEND') > 0) {
                // Change configuration name
                Configuration::updateValue('PS_OS_SM_TO_DISPATCH', Configuration::get('PS_OS_SM_SEND'));
                Configuration::updateValue('PS_OS_SM_DISPATCHED', Configuration::get('PS_OS_SM_SENT'));

                // Delete old ones
                Configuration::deleteByName('PS_OS_SM_SEND');
                Configuration::deleteByName('PS_OS_SM_SENT');
            }

            // Update order states
            $this->installOrderStates();

            // Set module version
            Configuration::updateValue('SM_VERSION', $this->module->version);
        }

        if (version_compare($version_registered, '1.1.0', '<')) {

            // Register new hook
            if (version_compare(_PS_VERSION_, '1.5') >= 0) {
                $this->module->registerHook('actionValidateOrder');
            } else {
                $this->module->registerHook('newOrder');
            }

            // Set module version
            Configuration::updateValue('SM_VERSION', $this->module->version);
        }

        if (Configuration::get('SM_EXPORT_ALL') == '') {
            Configuration::updateValue('SM_EXPORT_ALL', 'yes');
        }
        if (Configuration::get('SM_ENABLE_NATIVE_REFUND_SYSTEM') == '') {
            Configuration::updateValue('SM_ENABLE_NATIVE_REFUND_SYSTEM', 'no');
        }
        if (Configuration::get('SM_ENABLE_EXPORT_COMB_NAME') == '') {
            Configuration::updateValue('SM_ENABLE_EXPORT_COMB_NAME', 'yes');
        }
        if (Configuration::get('SM_IMPORT_METHOD') == '') {
            Configuration::updateValue('SM_IMPORT_METHOD', 'automatic');
        }
        if (Configuration::get('SM_CATCH_ALL_MAIL_ADDRESS') == '') {
            Configuration::updateValue('SM_CATCH_ALL_MAIL_ADDRESS', Configuration::get('PS_SHOP_EMAIL'));
        }
        if (Configuration::get('SM_ORDER_IMPORT_PAST_DAYS') == '' || Configuration::get('SM_ORDER_IMPORT_PAST_DAYS') < 1 || Configuration::get('SM_ORDER_IMPORT_PAST_DAYS') > 30) {
            Configuration::updateValue('SM_ORDER_IMPORT_PAST_DAYS', 30);
        }
        if (Configuration::get('SM_ORDER_IMPORT_LIMIT') == '' || Configuration::get('SM_ORDER_IMPORT_LIMIT') < 1 || Configuration::get('SM_ORDER_IMPORT_LIMIT') > 2000) {
            Configuration::updateValue('SM_ORDER_IMPORT_LIMIT', 100);
        }
        if (Configuration::get('SM_EXPORT_STAY_NB_DAYS') == '' || Configuration::get('SM_EXPORT_STAY_NB_DAYS') < 1) {
            Configuration::updateValue('SM_EXPORT_STAY_NB_DAYS', 7);
        }

        if (version_compare($version_registered, '2.1.6', '<')) {
            if (version_compare(_PS_VERSION_, '1.5') >= 0) {
                $this->module->registerHook('actionOrderStatusUpdate');
            } else {
                $this->module->registerHook('updateOrderStatus');
            }
        }

        if (version_compare(_PS_VERSION_, '1.6') >= 0) {
            if (!$this->isModuleRegisteredOnHook($this->module, 'actionUpdateQuantity', Context::getContext()->shop->id)) {
                $this->module->registerHook('actionUpdateQuantity');
            }
        }

        if (Tools::getValue('migrate') == 'v3') {
            Configuration::updateValue('SM_ORDER_ENDPOINT', 'http://api.sellermania.com/v3/OrdersAPIS?wsdl');
            Configuration::updateValue('SM_INVENTORY_ENDPOINT', 'http://api.sellermania.com/v3/InventoryAPIS?wsdl');
            Configuration::updateValue('SM_INVENTORY_ENDPOINT', 'http://api.sellermania.com/v3/InventoryAPIS?wsdl');
            Configuration::updateValue('SM_API_VERSION', 'v3');
        }
    }



    /**
     * Install Sellermania Order States
     */
    public function installOrderStates()
    {
        $languages = array(
            (int)Configuration::get('PS_LANG_DEFAULT') => 'en',
            (int)Language::getIdByIso('fr') => 'fr',
            (int)Language::getIdByIso('en') => 'en',
        );

        foreach ($this->module->sellermania_order_states as $order_state_key => $order_state_array)
        {
            $order_state = new OrderState(Configuration::get($order_state_key));
            if ($order_state->id < 1)
            {
                $order_state = new OrderState();
                $order_state->send_email = false;
                $order_state->module_name = $this->name;
                $order_state->invoice = $order_state_array['invoice'];
                $order_state->color = $order_state_array['color'];
                $order_state->logable = $order_state_array['logable'];
                $order_state->shipped = $order_state_array['shipped'];
                $order_state->unremovable = false;
                $order_state->delivery = $order_state_array['shipped'];
                $order_state->hidden = false;
                $order_state->paid = $order_state_array['invoice'];
                $order_state->deleted = false;

                $order_state->name = array();
                foreach ($languages as $key_lang => $iso_lang)
                    if ($key_lang > 0)
                        $order_state->name[$key_lang] = pSQL('Marketplace - '.$order_state_array['label'][$iso_lang]);

                if ($order_state->add())
                {
                    Configuration::updateValue($order_state_key, $order_state->id);
                    copy(dirname(__FILE__).'/logo.gif', dirname(__FILE__).'/../../img/os/'.$order_state->id.'.gif');
                    copy(dirname(__FILE__).'/logo.gif', dirname(__FILE__).'/../../img/tmp/order_state_mini_'.$order_state->id.'.gif');
                }
            }
            else
            {
                $order_state = new OrderState((int)Configuration::get($order_state_key));

                $order_state->color = $order_state_array['color'];
                $order_state->name = array();
                foreach ($languages as $key_lang => $iso_lang)
                    if ($key_lang > 0)
                        $order_state->name[$key_lang] = pSQL('Marketplace - '.$order_state_array['label'][$iso_lang]);

                $order_state->update();
            }
        }
    }


    /**
     * Install Sellermania Product (in case a product is not recognized)
     */
    public function installSellermaniaProduct()
    {
        if (Configuration::get('SM_DEFAULT_PRODUCT_ID') > 0)
        {
            $product = new Product((int)Configuration::get('SM_DEFAULT_PRODUCT_ID'));
            if ($product->id > 0)
                return true;
        }

        $label = 'Sellermania product';

        $product = new Product();
        $product->name = array((int)Configuration::get('PS_LANG_DEFAULT') => pSQL($label));
        $product->link_rewrite = array((int)Configuration::get('PS_LANG_DEFAULT') => 'sellermania-product');
        $product->id_tax_rules_group = 0;
        $product->id_supplier = 0;
        $product->id_manufacturer = 0;
        $product->id_category_default = 0;
        $product->quantity = 0;
        $product->minimal_quantity = 1;
        $product->price = 1;
        $product->wholesale_price = 0;
        $product->out_of_stock = 1;
        $product->available_for_order = 1;
        $product->show_price = 1;
        $product->date_add = pSQL(date('Y-m-d H:i:s'));
        $product->date_upd = pSQL(date('Y-m-d H:i:s'));
        $product->active = 1;
        $product->add();

        if (version_compare(_PS_VERSION_, '1.5') >= 0)
            StockAvailable::setProductOutOfStock((int)$product->id, 1);

        // Saving product ID
        Configuration::updateValue('SM_DEFAULT_PRODUCT_ID', (int)$product->id);

        return true;
    }


}
