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

// Define if not defined
if (!defined('_PS_BASE_URL_'))
    define('_PS_BASE_URL_', Tools::getShopDomain(true));


class SellermaniaInvoiceController
{
    /**
     * SellermaniaInvoiceController constructor.
     * @param object $module
     * @param string $dir_path
     * @param string $web_path
     */
    public function __construct($module, $dir_path, $web_path)
    {
        $this->module = $module;
        $this->web_path = $web_path;
        $this->dir_path = $dir_path;
        $this->context = Context::getContext();
        $this->ps_version = str_replace('.', '', substr(_PS_VERSION_, 0, 3));
    }

    public function generate($id_order)
    {
        // Display Sellermania invoice
        $sellermania_order = SellermaniaOrder::getSellermaniaOrderFromOrderId($id_order);
        if (isset($sellermania_order->details) && ($sellermania_order->details['User'][0]['InvoiceUrl'] || $sellermania_order->details['User'][1]['InvoiceUrl'])) {
            $invoice_url = '';
            if (isset($sellermania_order->details['User'][0]['InvoiceUrl'])) {
                $invoice_url = $sellermania_order->details['User'][0]['InvoiceUrl'];
            }
            if (isset($sellermania_order->details['User'][1]['InvoiceUrl'])) {
                $invoice_url = $sellermania_order->details['User'][1]['InvoiceUrl'];
            }
            if (!empty($invoice_url)) {
                header('location:'.$invoice_url);
                exit;
            }
        }
        die('No invoice available yet');
    }

    /**
     * Run method
     */
    public function run()
    {
        // Set _PS_ADMIN_DIR_ define and set default Shop
        if (!defined('_PS_ADMIN_DIR_'))
            define('_PS_ADMIN_DIR_', getcwd());

        // Check if Order ID
        if (Tools::getValue('id_order') < 1) {
            die('ERROR: No Order ID');
        }

        // Generate invoice
        $this->generate(Tools::getValue('id_order'));
    }
}

