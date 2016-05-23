<?php
/*
* 2010-2015 Sellermania / Froggy Commerce / 23Prod SARL
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
*  @author Fabien Serny - Froggy Commerce <team@froggy-commerce.com>
*  @copyright     2010-2015 Sellermania / Froggy Commerce / 23Prod SARL
*  @version       1.0
*  @license       http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_'))
    exit;

// Define if not defined
if (!defined('_PS_BASE_URL_'))
    define('_PS_BASE_URL_', Tools::getShopDomain(true));


class SellerManiaInvoiceController
{
    public function generate($id_order)
    {
    }


    /**
     * Run method
     */
    public function run()
    {
        // Set _PS_ADMIN_DIR_ define and set default Shop
        if (!defined('_PS_ADMIN_DIR_'))
            define('_PS_ADMIN_DIR_', getcwd());

        // Check if SellerMania key exists
        if (Configuration::get('SELLERMANIA_KEY') == '') {
            die('ERROR1');
        }
        else if (Tools::getValue('k') == '') {
            die('ERROR2');
        }
        else if (Tools::getValue('k') == Configuration::get('SELLERMANIA_KEY'))  {
            $this->generate(Tools::getValue('id_order'));
        }
        else {
            die('ERROR3');
        }
    }
}

