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
require_once(dirname(__FILE__).'/SellermaniaMarketplace.php');

class SellermaniaInstaller
{
    private $module;

    private $tabs = [
        "config" => [
            "name" => ["fr" => "Configuration", "en" => "Settings"],
            "class" => "AdminSellermaniaSettings",
            "icon" => "settings",
        ],
        "diagnostics" => [
            "name" => ["fr" => "Diagnostic", "en" => "Diagnostic"],
            "class" => "AdminSellermaniaDiagnostics",
            "icon" => "star_border",
        ],
    ];

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

        if (!$this->isTabsInstalled()) {
            $this->installTabs();
        }

        // Install Order States
        // $this->installOrderStates();

        // Install Product
        $this->installSellermaniaProduct();

        // Gen Sellermania key
        Configuration::updateValue('SM_VERSION', $this->module->version);
        Configuration::updateValue('SM_INSTALL_DATE', date('Y-m-d H:i:s'));
        Configuration::updateValue('SELLERMANIA_KEY', md5(rand()._COOKIE_KEY_.date('YmdHis')));
        Configuration::updateValue('SM_ORDER_ENDPOINT', 'https://api.sellermania.com/v3/OrdersAPIS?wsdl');
        Configuration::updateValue('SM_CONFIRM_ORDER_ENDPOINT', 'https://membres.sellermania.com/wsapi/wsdl/OrderConfirmation');
        Configuration::updateValue('SM_INVENTORY_ENDPOINT', 'https://api.sellermania.com/v3/InventoryAPIS?wsdl');

        Configuration::updateValue('SM_ENABLE_NATIVE_REFUND_SYSTEM', 'no');
        Configuration::updateValue('SM_ENABLE_EXPORT_COMB_NAME', 'yes');

        Configuration::updateValue('SM_IMPORT_ORDERS', "yes");
        Configuration::updateValue('SM_SCHEMA_LOADED', 1);
        Configuration::updateValue('SM_WIZARD_LAUNCHED', 0);
        Configuration::updateValue('SM_SECRET_KEY', $this->generateSecretKey());

        return true;
    }

    private function generateSecretKey ()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < 50; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }



    /**
     * Uninstall method
     * @return boolean success
     */
    public function uninstall()
    {
        // Execute module install MySQL commands
         $sql_file = dirname(__FILE__).'/../install/uninstall.sql';
         if (!$this->loadSQLFile($sql_file))
            return false;

        //Delete default sellermania product to avoid duplicates
         $default_product_id = $this->module->getDefaultProductID();
         if ($default_product_id > 0) {
            $product = new Product($default_product_id);
            $product->delete();
         }
       
        // Delete configuration values
        Configuration::deleteByName('SELLERMANIA_KEY');
        $this->uninstallTabs();

        return true;
    }

    public function installTabs()
    {
        $languages = Language::getLanguages(false);

        // parent tab
        $parent_tab = new Tab();
        foreach ($languages as $language) {
            $parent_tab->name[$language['id_lang']] = $this->module->l('Sellermania');
        }
        $parent_tab->class_name = 'Sellermania';
        $parent_tab->id_parent = 0; // Home tab
        $parent_tab->module = $this->module->name;
        $parent_tab->add();

        foreach ($this->tabs as $s_tab) {
            $tab = new Tab();
            // Need a foreach for the language
            foreach ($languages as $language) {
                if (isset($s_tab["name"][$language['iso_code']])) {
                    $tab->name[$language['id_lang']] = $s_tab["name"][$language['iso_code']];
                }
             }
            $tab->class_name = $s_tab["class"];
            $tab->id_parent = $parent_tab->id;
            $tab->module = $this->module->name;
            $tab->icon = $s_tab["icon"];
            $tab->add();
        }
    }

    public function uninstallTabs()
    {
        try {
            $sql = 'SELECT `id_tab`, `class_name` FROM `' . _DB_PREFIX_ . 'tab` WHERE `module` = \'sellermania\'';
            $tabs = Db::getInstance()->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            $tabs = array();
        }
        foreach ($tabs as $value) {
            try {
                $tab = new Tab((int) $value['id_tab']);
                if ($tab->id != 0) {
                    $tab->delete();
                }
            } catch (Exception $e) {
                continue;
            }
        }
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
        if (!$version_registered) {
            $version_registered = $this->module->version;
        }

        if (version_compare($version_registered, '1.1.0', '<')) {

            // Register new hook
            if (version_compare(_PS_VERSION_, '1.5') >= 0) {
                $this->module->registerHook('actionValidateOrder');
            } else {
                $this->module->registerHook('newOrder');
            }
        }
        if (Configuration::get('SM_IMEI_MKPS') == '') {
            Configuration::updateValue('SM_IMEI_MKPS', json_encode(array('BACKMARKET','QUELBONPLAN','REFURBED')));
        }
        if (Configuration::get('SM_TRACKING_URL_MKPS') == '') {
            Configuration::updateValue('SM_TRACKING_URL_MKPS', json_encode(array('RAKUTEN','REFURBED')));
        }
        if (Configuration::get('SM_LOGISTIC_WEIGHT_MKPS') == '') {
            Configuration::updateValue('SM_LOGISTIC_WEIGHT_MKPS', json_encode(array('AMAZONVENDOR')));
        }
        if (Configuration::get('SM_MKPS_MERCHANTID') == '') {
            Configuration::updateValue('SM_MKPS_MERCHANTID', json_encode(array('SHOPPINGACTIONS','AMAZONVENDOR','ZALANDO')));
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
        if (Configuration::get('SM_EXPORT_STAY_NB_DAYS') == '') {
           // Configuration::updateValue('SM_EXPORT_STAY_NB_DAYS', 7);
        }
        
        if (Configuration::get('SM_EXPORT_STAY_NB_DAYS') != '' ) {
            if (Configuration::get('SM_EXPORT_STAY_NB_DAYS') > 0) {
                Configuration::updateValue('SM_LAST_DAYS_TO_INCLUDE_IN_FEED', Configuration::get('SM_EXPORT_STAY_NB_DAYS'));
                Configuration::updateValue('SM_PRODUCT_TO_INCLUDE_IN_FEED', 'without_oos');
                Configuration::updateValue('SM_EXPORT_STAY_NB_DAYS', '');
            } else {
                Configuration::updateValue('SM_PRODUCT_TO_INCLUDE_IN_FEED', 'all');
            }
        }
        if (Configuration::get('SM_PRODUCT_TO_INCLUDE_IN_FEED') == '') {
            Configuration::updateValue('SM_PRODUCT_TO_INCLUDE_IN_FEED', 'all');
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
            if (!$this->isModuleRegisteredOnHook($this->module, 'actionAdminOrdersListingFieldsModifier', Context::getContext()->shop->id)) {
                $this->module->registerHook('actionAdminOrdersListingFieldsModifier');
            }
        }

        if (version_compare(_PS_VERSION_, '1.7') >= 0) {
            if (!$this->isModuleRegisteredOnHook($this->module, 'ActionOrderGridQueryBuilderModifier', Context::getContext()->shop->id)) {
                $this->module->registerHook('ActionOrderGridQueryBuilderModifier');
            }
            if (!$this->isModuleRegisteredOnHook($this->module, 'ActionOrderGridDefinitionModifier', Context::getContext()->shop->id)) {
                $this->module->registerHook('ActionOrderGridDefinitionModifier');
            }
        }

        if (Configuration::get('SM_API_VERSION') != 'v3') {
            Configuration::updateValue('SM_API_VERSION', 'v3');
            $this->migrateMarketplacesHistory();
        }

        if (version_compare($version_registered, '2.6.0.9', '<') && Configuration::get('SM_API_VERSION') == 'v3') {
            $this->migrateMarketplacesHistory();
        }

        if (version_compare($version_registered, '2.6.2', '<')) {
            Configuration::updateValue('SM_INVENTORY_ENDPOINT', '');
        }

        if (version_compare($version_registered, '2.6.3', '<')) {
            Configuration::updateValue('SM_ORDER_ENDPOINT', 'https://api.sellermania.com/v3/OrdersAPIS?wsdl');
        }

        if (Configuration::get('SM_IMPORT_DEFAULT_COUNTRY_CODE') == '' || Configuration::get('SM_SHIPMENT_DEFAULT_COUNTRY_CODE') == '') {
            $default_country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
            Configuration::updateValue('SM_IMPORT_DEFAULT_COUNTRY_CODE', $default_country->iso_code);
            Configuration::updateValue('SM_SHIPMENT_DEFAULT_COUNTRY_CODE', $default_country->iso_code);
        }

        if (version_compare($version_registered, '2.6.9', '<') && version_compare(_PS_VERSION_, '1.6') < 0) {
            $this->updateConfigurationFieldSize( 64);
        }

        if (version_compare($version_registered, '2.6.10', '<')) {
            Configuration::updateValue('SM_VERSION', $this->module->version); // Add update version here to avoid infinite loop with hook ActionUpdateQuantity
            $product = new Product((int)$this->module->getDefaultProductID());
            $product->quantity = 999999;
            $product->update();
            if (version_compare(_PS_VERSION_, '1.5') >= 0) {
                StockAvailable::setProductOutOfStock((int)$product->id, 1);
                StockAvailable::setQuantity((int)$product->id, 0, 999999);
            }
        }

        if (version_compare($version_registered, '2.7.0', '<')) {
            Configuration::updateValue('SM_PRODUCT_MATCH', 'automatic');
        }

        if (version_compare($version_registered, '2.9.0', '<')) {
            if (!Db::getInstance()->execute('SELECT order_imei from '._DB_PREFIX_.'sellermania_order')) {
                $sql = 'ALTER TABLE `'._DB_PREFIX_.'sellermania_order` ADD order_imei text NOT NULL AFTER amount_total';
                Db::getInstance()->execute($sql);
            }
        }

        if (version_compare($version_registered, '3.0.0', '<')) {
            Configuration::updateValue('SM_WIZARD_LAUNCHED', 0);
            Configuration::updateValue('SM_SECRET_KEY', $this->generateSecretKey());
            $sql = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."sellermania_marketplace` (
                `id_sellermania_marketplace` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `code` varchar(255) NOT NULL,
                `enabled` int(11) NOT NULL,
                `available` int(11) NOT NULL,
                PRIMARY KEY (`id_sellermania_marketplace`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
            Db::getInstance()->execute($sql);

            $sql = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."sellermania_field_error` (
                `id_sellermania_field_error` int(11) NOT NULL AUTO_INCREMENT,
                `field_name` varchar(255) DEFAULT NULL,
                `error_message` text,
                `section` varchar(255) DEFAULT NULL,
                `is_active` tinyint(4) NOT NULL,
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_sellermania_field_error`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
            Db::getInstance()->execute($sql);
        }

        if (Configuration::get('SM_VERSION') != $this->module->version) {
            Configuration::updateValue('SM_VERSION', $this->module->version);
        }


        if (!$this->isTabsInstalled()) {
            $this->installTabs();
        }
    }

    private function isTabsInstalled ()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'tab` WHERE `module` = \'sellermania\'';
        $tabs = Db::getInstance()->executeS($sql);
        if (empty($tabs)) {
            return false;
        } else {
            $parent_not_installed = false;
            foreach ($tabs as $tab) {
                if (isset($tab['id_parent']) && $tab['id_parent'] != 0) {
                    $parent_not_installed = true;
                }
            }
            return $parent_not_installed;
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
                $order_state->module_name = $this->module->name;
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
                    copy(dirname(__FILE__).'/../logo.gif', dirname(__FILE__).'/../../../img/os/'.$order_state->id.'.gif');
                    copy(dirname(__FILE__).'/../logo.gif', dirname(__FILE__).'/../../../img/tmp/order_state_mini_'.$order_state->id.'.gif');
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
        // Check if sellermania product exists
        $sellermania_default_product_id = $this->module->getDefaultProductID();
        $id_shop = Context::getContext()->shop->id;
        if(empty(Context::getContext()->shop->id)){
            $id_shop = 0;
        }
       
        if ($sellermania_default_product_id > 0) {
            if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 1) {
                $product = new Product($sellermania_default_product_id, false, Configuration::get('PS_LANG_DEFAULT'), $id_shop);
            } else {
                $product = new Product($sellermania_default_product_id);
            }
            if ($product->id > 0 && ($product->id_shop_default == $id_shop)) {
                return true;
            }
        }

        $label = 'Sellermania product';

        $product = new Product();
        $product->name = array((int)Configuration::get('PS_LANG_DEFAULT') => pSQL($label));
        $product->link_rewrite = array((int)Configuration::get('PS_LANG_DEFAULT') => 'sellermania-product');
        $product->id_tax_rules_group = 0;
        $product->id_supplier = 0;
        $product->id_manufacturer = 0;
        $product->id_category_default = 0;
        $product->quantity = 999999;
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

        if (version_compare(_PS_VERSION_, '1.5') >= 0) {
            StockAvailable::setProductOutOfStock((int)$product->id, 1);
            StockAvailable::updateQuantity((int)$product->id, 0, 999999);
        }

        // Saving product ID
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 1) {
            Configuration::updateValue('SM_DEFAULT_PRODUCT_ID', (int)$product->id, null, null, Context::getContext()->shop->id);
        } else {
            Configuration::updateValue('SM_DEFAULT_PRODUCT_ID', (int)$product->id);
        }

        return true;
    }


    public function migrateMarketplacesHistory()
    {
        if (is_array($this->module->sellermania_marketplaces_migration)) {
            foreach ($this->module->sellermania_marketplaces_migration as $old_marketplace => $new_marketplace) {
                $this->update(
                    'sellermania_order',
                    array('marketplace' => $new_marketplace),
                    '`marketplace` = \''.$old_marketplace.'\''
                );
            }
        }
    }


    public function update($table, $sql_data, $where)
    {
        // If PS 1.6 or greater, we use update instead of autoexecute
        if (version_compare(_PS_VERSION_, '1.6.0') >= 0) {
            Db::getInstance()->update($table, $sql_data, $where);
        } else {
            Db::getInstance()->autoExecute(_DB_PREFIX_.$table, $sql_data, 'UPDATE', $where);
        }
    }

    public function insert($table, $sql_data)
    {
        // If PS 1.6 or greater, we use update instead of autoexecute
        if (version_compare(_PS_VERSION_, '1.6.0') >= 0) {
            Db::getInstance()->insert($table, $sql_data);
        } else {
            Db::getInstance()->autoExecute(_DB_PREFIX_.$table, $sql_data, 'INSERT');
        }
    }

    public function updateConfigurationFieldSize($wanted_size)
    {
        // Check fields
        $test = Db::getInstance()->executeS('SHOW FULL COLUMNS FROM `'._DB_PREFIX_.'configuration`');
        foreach ($test as $field) {

            // For field name
            if ($field['Field'] == 'name') {

                // Retrieve size
                $field_size = (int)str_replace('varchar(', '', $field['Type']);
                if ($field_size > 0 && $field_size < $wanted_size) {

                    // Alter table
                    Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'configuration` MODIFY `name` VARCHAR('.(int)$wanted_size.')');

                    // Alter Configuration class (dirty fix but it's only for PS 1.5, an override would have been heavy for this)
                    $configuration_class_path_file = __DIR__.'/../../../classes/Configuration.php';
                    $configuration_class_search_replace = "'type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => ";
                    $content = file_get_contents(__DIR__.'/../../../classes/Configuration.php');
                    $content = str_replace($configuration_class_search_replace.$field_size, $configuration_class_search_replace.$wanted_size, $content);
                    file_put_contents($configuration_class_path_file, $content);
                }
            }
        }
    }
}
