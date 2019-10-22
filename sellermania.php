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

// Include all class needed
require_once(dirname(__FILE__).'/init.php');

class Sellermania extends Module
{
    public $sellermania_order_states;
    public $sellermania_conditions_list;
    public $sellermania_marketplaces = array(
        'AMAZON.DE', 'AMAZON.ES', 'AMAZON.FR', 'AMAZON.IT', 'AMAZON.UK',
        'ATLAS4MEN.FR', 'AUCHAN.FR', 'BOULANGER.FR', 'CDISCOUNT.COM', 'COMPTOIRSANTE.FR', 'DARTY.FR',
        'DELAMAISON.FR', 'DOCTIPHARMA.FR', 'EBAY.FR', 'ELCORTEINGLES.FR', 'EPRICE.IT',
        'FNAC.COM', 'GALLERIESLAFAYETTE.FR', 'GAME.FR', 'LEQUIPE.FR', 'MACWAY.COM', 'MENLOOK.FR', 'NATUREETDECOUVERTE.FR',
        'PIXMANIA.DE', 'PIXMANIA.ES', 'PIXMANIA.FR', 'PIXMANIA.IT', 'PIXMANIA.UK',
        'PRICEMINISTER.FR', 'PRIVALIA.FR', 'RETIF.FR', 'RUEDUCOMMERCE.FR', 'SAINTGOBAIN.COM',
        'THEBEAUTISTE.FR', 'TRUFFAUT.FR', 'GOSPORT.FR',
    );

    /**
     * Module Constructor
     */
    public function __construct()
    {
        $this->name = 'sellermania';
        $this->tab = 'advertising_marketing';
        $this->author = 'Froggy Commerce';
        $this->version = '2.4.3';
        $this->need_instance = 0;

        parent::__construct();

        // If PS 1.4, we use backward compatibility
        if (version_compare(_PS_VERSION_, '1.5') < 0)
            require(dirname(__FILE__).'/backward/backward.php');

        // If PS 1.6 or greater, we enable bootstrap
        if (version_compare(_PS_VERSION_, '1.6.0') >= 0)
            $this->bootstrap = true;

        $this->displayName = $this->l('Sellermania');
        $this->description = $this->l('Connect your PrestaShop with Sellermania webservices');

        $this->loadSellermaniaOrderStates();
        $this->loadSellermaniaConditionsList();
        $this->upgrade();
    }


    /**
     * Load Sellermania order states
     */
    public function loadSellermaniaOrderStates()
    {
        $this->sellermania_order_states = array(
            'PS_OS_SM_ERR_CONF' => array('sm_status' => 11, 'sm_prior' => 1, 'label' => array('en' => 'Error confirmation', 'fr' => 'En erreur de confirmation'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#fa4b4c'),
            'PS_OS_SM_ERR_CANCEL_CUS' => array('sm_status' => 12, 'sm_prior' => 1, 'label' => array('en' => 'Error cancel by customer', 'fr' => 'En erreur, annulée par client'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#fa4b4c'),
            'PS_OS_SM_ERR_CANCEL_SEL' => array('sm_status' => 13, 'sm_prior' => 1, 'label' => array('en' => 'Error cancel by seller', 'fr' => 'En erreur, annulée par vendeur'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#fa4b4c'),

            'PS_OS_SM_AWAITING' => array('sm_status' => 6, 'sm_prior' => 1, 'label' => array('en' => 'To be confirmed', 'fr' => 'A confirmer'), 'logable' => false, 'invoice' => false, 'shipped' => false, 'paid' => false, 'color' => '#98c3ff'),
            'PS_OS_SM_CONFIRMED' => array('sm_status' => 9, 'sm_prior' => 0, 'label' => array('en' => 'Waiting for payment', 'fr' => 'En attente de paiement'), 'logable' => true, 'invoice' => false, 'shipped' => false, 'paid' => false, 'color' => '#98c3ff'),
            'PS_OS_SM_TO_DISPATCH' => array('sm_status' => 1, 'sm_prior' => 1, 'label' => array('en' => 'To dispatch', 'fr' => 'A expédier'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => true, 'color' => '#98c3ff'),
            'PS_OS_SM_DISPATCHED' => array('sm_status' => 2, 'sm_prior' => 0, 'label' => array('en' => 'Dispatched', 'fr' => 'Expédiée'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => true, 'color' => '#98c3ff'),

            'PS_OS_SM_CANCEL_CUS' => array('sm_status' => 3, 'sm_prior' => 0, 'label' => array('en' => 'Cancel by customer', 'fr' => 'Annulée par client'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#98c3ff'),
            'PS_OS_SM_CANCEL_SEL' => array('sm_status' => 4, 'sm_prior' => 0, 'label' => array('en' => 'Cancel by seller', 'fr' => 'Annulée par vendeur'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#98c3ff'),
        );
    }

    /**
     * Load Sellermania product conditions
     */
    public function loadSellermaniaConditionsList()
    {
        $this->sellermania_conditions_list = array(
            0 => $this->l('Unknown', 'sellermaniadisplayadminorder'),
            1 => $this->l('Used - Mint', 'sellermaniadisplayadminorder'),
            2 => $this->l('Used - Very good', 'sellermaniadisplayadminorder'),
            3 => $this->l('Used - Good', 'sellermaniadisplayadminorder'),
            4 => $this->l('Used - Acceptable', 'sellermaniadisplayadminorder'),
            5 => $this->l('Collectible - Mint', 'sellermaniadisplayadminorder'),
            6 => $this->l('Collectible - Very good', 'sellermaniadisplayadminorder'),
            7 => $this->l('Collectible - Good', 'sellermaniadisplayadminorder'),
            8 => $this->l('Collectible - Acceptable', 'sellermaniadisplayadminorder'),
            9 => $this->l('Collectible - New', 'sellermaniadisplayadminorder'),
            10 => $this->l('Refurbished - New', 'sellermaniadisplayadminorder'),
            11 => $this->l('New', 'sellermaniadisplayadminorder'),
            12 => $this->l('New OEM', 'sellermaniadisplayadminorder'),
            13 => $this->l('Used - Openbox', 'sellermaniadisplayadminorder'),
            14 => $this->l('Refurbished - Mint', 'sellermaniadisplayadminorder'),
            15 => $this->l('Refurbished - Very good', 'sellermaniadisplayadminorder'),
            16 => $this->l('Used - Poor', 'sellermaniadisplayadminorder'),
            17 => $this->l('Refurbished - Good', 'sellermaniadisplayadminorder'),
            18 => $this->l('Refurbished - Acceptable', 'sellermaniadisplayadminorder'),
        );
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
            Configuration::updateValue('SM_VERSION', $this->version);
        }

        if (version_compare($version_registered, '1.1.0', '<')) {

            // Register new hook
            if (version_compare(_PS_VERSION_, '1.5') >= 0) {
                $this->registerHook('actionValidateOrder');
            } else {
                $this->registerHook('newOrder');
            }

            // Set module version
            Configuration::updateValue('SM_VERSION', $this->version);
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
                $this->registerHook('actionOrderStatusUpdate');
            } else {
                $this->registerHook('updateOrderStatus');
            }
        }


        if (version_compare(_PS_VERSION_, '1.7') >= 0) {
            if (!Hook::isModuleRegisteredOnHook($this, 'actionUpdateQuantity', Context::getContext()->shop->id)) {
                $this->registerHook('actionUpdateQuantity');
            }
        }
    }

    /**
     * Install method
     * @return boolean success
     */
    public function install()
    {
        // Execute module install MySQL commands
        $sql_file = dirname(__FILE__).'/install/install.sql';
        if (!$this->loadSQLFile($sql_file))
            return false;

        // Register hooks
        if (version_compare(_PS_VERSION_, '1.5') >= 0)
        {
            if (!parent::install() || !$this->registerHook('displayAdminOrder') ||
                !$this->registerHook('displayBackOfficeHeader') || !$this->registerHook('actionValidateOrder') || !$this->registerHook('actionOrderStatusUpdate'))
                return false;
        }
        else
        {
            if (!parent::install() || !$this->registerHook('adminOrder') ||
                !$this->registerHook('backOfficeHeader') || !$this->registerHook('newOrder') || !$this->registerHook('updateOrderStatus'))
                return false;
        }

        // Install Order States
        $this->installOrderStates();

        // Install Product
        $this->installSellermaniaProduct();

        // Gen Sellermania key
        Configuration::updateValue('SM_VERSION', $this->version);
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
        // $sql_file = dirname(__FILE__).'/install/uninstall.sql';
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

        return parent::uninstall();
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

        foreach ($this->sellermania_order_states as $order_state_key => $order_state_array)
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


    /**
     * Compliant display between 1.4 and 1.5
     * @param string $template
     * @return string $html
     */
    public function compliantDisplay($template)
    {
        if (version_compare(_PS_VERSION_, '1.5') < 0)
            return $this->display(__FILE__, 'views/templates/hook/'.$template);
        else
            return $this->display(__FILE__, $template);
    }
    public function fcdisplay($file, $template)
    {
        if (isset($this->bootstrap) && $this->bootstrap)
            $template = str_replace('.tpl', '.bootstrap.tpl', $template);
        return $this->compliantDisplay($template);
    }


    /**
     * @param string $hook_name
     * @return mixed $result
     */
    public function runController($controller_type, $controller_name, $params = array())
    {
        // Include the controller file
        require_once(dirname(__FILE__).'/controllers/'.$controller_type.'/Sellermania'.$controller_name.'.php');
        $controller_name = 'Sellermania'.$controller_name.'Controller';
        $controller = new $controller_name($this, dirname(__FILE__), $this->_path);
        $controller->params = $params;

        return $controller->run();
    }


    /**
     * Configuration method
     * @return string $html
     */
    public function getContent()
    {
        if (Tools::getValue('export') == 'true') {
            die($this->export());
        }

        if (Tools::getValue('display') == 'invoice') {
            $this->invoice();
            exit;
        }

        // Will automatically recreate product if it was erased
        $this->installSellermaniaProduct();

        return $this->runController('hook', 'GetContent');
    }


    /**
     * Display BackOffice Header Hook
     * @return string $html
     */
    public function hookDisplayBackOfficeHeader($params)
    {
        if (version_compare(PHP_VERSION, '5.3.0') >= 0)
            return $this->runController('hook', 'DisplayBackOfficeHeader');
        return '';
    }
    public function hookBackOfficeHeader($params)
    {
        return $this->hookDisplayBackOfficeHeader($params);
    }

    /**
     * Display Admin Order
     * @return string $html
    */
    public function hookDisplayAdminOrder($params)
    {
        if (version_compare(PHP_VERSION, '5.3.0') >= 0)
            return $this->runController('hook', 'DisplayAdminOrder');
        return '';
    }
    public function hookAdminOrder($params)
    {
        return $this->hookDisplayAdminOrder($params);
    }

    /**
     * Refresh quantity on orders importation
     * @return string $html
     */
    public function hookActionValidateOrder($params)
    {
        if (version_compare(PHP_VERSION, '5.3.0') >= 0)
            return $this->runController('hook', 'ActionValidateOrder', $params);
        return '';
    }
    public function hookNewOrder($params)
    {
        return $this->hookActionValidateOrder($params);
    }

    /**
     * Send new order status to Sellermania
     * @return string $html
     */
    public function hookActionOrderStatusUpdate($params)
    {
        return $this->runController('hook', 'ActionOrderStatusUpdate', $params);
    }
    public function hookUpdateOrderStatus($params)
    {
        return $this->hookActionOrderStatusUpdate($params);
    }

    /**
     * Update quantity
     * @return bool
     */
    public function hookActionUpdateQuantity($params)
    {
        return $this->runController('hook', 'ActionUpdateQuantity', $params);
    }

    /**
     * Import method
     * @return string $export
     */
    public function import()
    {
        return $this->runController('front', 'Import');
    }

    /**
     * Export method
     * @return string $export
     */
    public function export()
    {
        return $this->runController('front', 'Export');
    }

    /**
     * Invoice method
     */
    public function invoice()
    {
        return $this->runController('front', 'Invoice');
    }

    /**
     * Log data
     * @param $string
     */
    public function log($string)
    {
        file_put_contents(dirname(__FILE__).'/log/log.txt', $string."\n", FILE_APPEND);
    }

    /**
     * Debug Log data
     * @param $string
     */
    public function debugLog($string)
    {
        if (Tools::getValue('debug') == 'import') {
            echo '<!-- '.date('Y-m-d H:i:s').' '.$string.' -->'."\n";
        }
    }
}
