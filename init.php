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

/*
 * Security
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

// Load Pear
if (!class_exists('PEAR')) {
    require_once(dirname(__FILE__).'/../../tools/pear/PEAR.php');
}

// Require Db requests class
$db_requests_class_file = 'SellermaniaProduct15.php';
if (version_compare(_PS_VERSION_, '1.5') < 0) {
    $db_requests_class_file = 'SellermaniaProduct14.php';
}
require_once(dirname(__FILE__).'/classes/'.$db_requests_class_file);

// Request Froggy lib
require_once(dirname(__FILE__).'/classes/FroggyLib.php');

// Load ObjectModel and PaymentModule classes
require_once(dirname(__FILE__).'/classes/SellermaniaOrder.php');
require_once(dirname(__FILE__).'/classes/SellermaniaPaymentModule.php');

// Load the Sellermania API Client
if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
    require_once(dirname(__FILE__).'/lib/sellermania/Sellermania.php');
}

if (!class_exists('TCPDF')) {
    if (defined('_PS_TCPDF_PATH_')) {
        require_once(_PS_TCPDF_PATH_.'/tcpdf.php');
    } else {
        require_once(dirname(__FILE__).'/lib/tcpdf/tcpdf.php');
    }
}

// Set time limit
@set_time_limit(1200);
@ini_set('memory_limit', '512M');

// Debug Sellermania mode can be enabled only adding _SELLERMANIA_DEBUG_ define to your settings file
if (defined('_SELLERMANIA_DEBUG_')) {
    if (Tools::getValue('sellermania') == 'deleteOrders') {
        Db::getInstance()->execute('TRUNCATE `ps_sellermania_order`');
    }
}