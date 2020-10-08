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

class SellermaniaDebugController
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

        if (empty($this->context->shop->id))
            $this->context->shop->setContext(4);

        // Check if Sellermania key exists
        if (Configuration::get('SELLERMANIA_KEY') == '')
            die('ERROR1');
        if (Tools::getValue('k') == '' && $argument_key == '')
            die('ERROR2');
        if (Tools::getValue('k') == Configuration::get('SELLERMANIA_KEY') || $argument_key == Configuration::get('SELLERMANIA_KEY'))
        {
            // Up time and memory limit
            set_time_limit(600);
            ini_set('memory_limit', '256M');

            if (Tools::getValue('action') == 'test-time') {
                $this->testTime();
            } elseif (Tools::getValue('action') == 'test-api') {
                $this->testAPI();
            } elseif (Tools::getValue('action') == 'test-product') {
                $this->testProduct();
            } else {
                die('Nope');
            }
        }
        else
            die('ERROR3');
    }

    public function testTime()
    {
        echo '<pre>';
        $timezone = date_default_timezone_get();
        echo "Date of system       : ".date("h:i:sa").PHP_EOL;
        date_default_timezone_set('Europe/Paris');
        $tm_created = gmdate('Y-m-d\TH:i:s\Z', time());
        $tm_expires = gmdate('Y-m-d\TH:i:s\Z', time() + 180);
        echo "Default Timezone     : ".$timezone.PHP_EOL;
        echo "Timestamp Creation   : ".$tm_created.PHP_EOL;
        echo "Timestamp Expiration : ".$tm_expires.PHP_EOL;
        date_default_timezone_set($timezone);
        echo '</pre>';
    }

    public function testAPI()
    {
        if (!class_exists('PEAR')) {
            echo 'Class Pear does not exists<br>';
            echo 'The end';
            exit;
        }

        // Creating an instance of OrderClient
        $client = new Sellermania\OrderClient();
        $client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
        $client->setToken(Configuration::get('SM_ORDER_TOKEN'));
        $client->setEndpoint(Configuration::get('SM_ORDER_ENDPOINT'));

        try
        {
            // Recovering orders of the last two days
            $two_days_ago = date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d'))));
            $result = $client->getOrderByDate(new \DateTime($two_days_ago), new \DateTime(date('Y-m-d')), 0);

            // Displaying results
            $this->d($result);
        }
        catch (\Exception $e)
        {
            echo "Caught exception :\n";
            echo $e->getMessage();
            echo "\n";
        }
    }

    public function testProduct()
    {
        if (Tools::getValue('id_product') > 0) {
            $product = Db::getInstance()->getRow('SELECT `id_product`, `date_upd` FROM `'._DB_PREFIX_.'product` WHERE `id_product` = '.Tools::getValue('id_product'));
            $this->d($product);
        }

        if (Tools::getValue('reference') != '') {
            $products = array();

            $products[] = Db::getInstance()->executeS('
            SELECT `id_product`, `date_upd`
            FROM `'._DB_PREFIX_.'product`
            WHERE `reference` = \''.pSQL(Tools::getValue('reference')).'\'
            OR `ean13` = \''.pSQL(Tools::getValue('reference')).'\' 
            OR `upc`  = \''.pSQL(Tools::getValue('reference')).'\'');

            $products[] = Db::getInstance()->executeS('
            SELECT p.`id_product`, p.`date_upd`
            FROM `'._DB_PREFIX_.'product_attribute` pa
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = pa.`id_product`)
            WHERE pa.`reference` = \''.pSQL(Tools::getValue('reference')).'\'
            OR pa.`ean13` = \''.pSQL(Tools::getValue('reference')).'\' 
            OR pa.`upc`  = \''.pSQL(Tools::getValue('reference')).'\'');

            if (isset($products[0][0]['id_product'])) {
                $products[] = Db::getInstance()->executeS('
				SELECT `id_product`, `date_upd`
    	        FROM `'._DB_PREFIX_.'product_shop`
	            WHERE `id_product` = '.(int)$products[0][0]['id_product']);
            }

            $this->d($products);
        }
    }

    public function d($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        exit;
    }
}

