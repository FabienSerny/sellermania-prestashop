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

// Load ImportOrder Controller
require_once(dirname(__FILE__).'/../hook/SellermaniaDisplayBackOfficeHeader.php');

class SellermaniaImportController
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
    }

    /**
     * Run method
     */
    public function run()
    {
        // Init
        global $argv;
        $argument_key = '';
        if (isset($argv[0]))
            $argument_key = Configuration::get('SELLERMANIA_KEY');

        // Set _PS_ADMIN_DIR_ define and set default Shop
        if (!defined('_PS_ADMIN_DIR_'))
            define('_PS_ADMIN_DIR_', getcwd());

        // Check if Sellermania key exists
        if (Configuration::get('SELLERMANIA_KEY') == '') {
            die('ERROR1');
        }
        if (Tools::getValue('k') == '' && $argument_key == '') {
            die('ERROR2');
        }
        // Check if credentials are ok
        if (Configuration::get('SM_CREDENTIALS_CHECK') != 'ok' || Configuration::get('SM_IMPORT_ORDERS') != 'yes' || $this->module->getDefaultProductID() < 1)
            die('Please check your Module Configuration');

        // Check if key is good
        if (Tools::getValue('k') == Configuration::get('SELLERMANIA_KEY') || $argument_key == Configuration::get('SELLERMANIA_KEY')) {

            // Check import method
            if (Configuration::get('SM_IMPORT_METHOD') != 'cron') {
                die("Wrong method, you have to choose cron method importation in module configuration\n");
            }

            // Up time and memory limit
            set_time_limit(600);
            ini_set('memory_limit', '256M');

            // Update next import (only for display on BO)
            $next_import = date("Y-m-d H:i:s", strtotime('+15 minutes'));
            Configuration::updateValue('SM_NEXT_IMPORT', $next_import);

            // Import orders
            $controller = new SellermaniaDisplayBackOfficeHeaderController($this->module, $this->dir_path, $this->web_path);
            $controller->verbose = true;
            $controller->importOrders();
        }
        else {
            die('ERROR3');
        }
    }
}

