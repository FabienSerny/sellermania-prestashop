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

// Include all class needed
require_once(dirname(__FILE__).'/init.php');

class Sellermania extends Module
{
    public $loader;
    public $installer;

    public $sellermania_order_states;
    public $sellermania_conditions_list;
    public $sellermania_marketplaces;

    /**
     * Module Constructor
     */
    public function __construct()
    {
        $this->name = 'sellermania';
        $this->tab = 'advertising_marketing';
        $this->author = 'Froggy Commerce';
        $this->version = '2.6.0.0';
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

        $this->installer = new SellermaniaInstaller($this);
        $this->loader = new SellermaniaLoader($this);

        $this->loader->loadOrderStates();
        $this->loader->loadConditionsList();
        $this->loader->loadMarketplaces();

        $this->installer->upgrade();
    }






    /**
     * Install method
     * @return boolean success
     */
    public function install()
    {
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

        return $this->installer->install();
    }


    /**
     * Uninstall method
     * @return boolean success
     */
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        return $this->installer->uninstall();
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
        $this->installer->installSellermaniaProduct();

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
     * Export method
     * @return string $export
     */
    public function debug()
    {
        return $this->runController('front', 'Debug');
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
    public function logger($type, $log)
    {
        if (Configuration::get('SM_STOCK_SYNC_LOG') == 'yes') {
            file_put_contents(dirname(__FILE__).'/log/'.$type.'-'.Configuration::get('SELLERMANIA_KEY').'.txt', $log."\n", FILE_APPEND);
        }
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
