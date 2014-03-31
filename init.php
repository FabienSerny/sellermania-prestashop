<?php
/*
* 2010 - 2013 Sellermania / 23Prod SARL
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to fabien@23prod.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade your module to newer
* versions in the future.
*
*  @author Fabien Serny - 23Prod <fabien@23prod.com>
*  @copyright	2010-2013 23Prod SARL
*  @version		1.0
*  @license		http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_'))
	exit;

// Load Pear
if (!class_exists('PEAR'))
	require_once(dirname(__FILE__).'/../../tools/pear/PEAR.php');

// Require Db requests class
$db_requests_class_file = 'SellerManiaProduct15.php';
if (version_compare(_PS_VERSION_, '1.5') < 0)
	$db_requests_class_file = 'SellerManiaProduct14.php';
require_once(dirname(__FILE__).'/classes/'.$db_requests_class_file);

// Load ObjectModel and PaymentModule classes
require_once(dirname(__FILE__).'/classes/SellermaniaOrder.php');
require_once(dirname(__FILE__).'/classes/SellermaniaPaymentModule.php');

// Load the Sellermania API Client
if (version_compare(PHP_VERSION, '5.3.0') >= 0)
	require_once(dirname(__FILE__).'/lib/Sellermania.php');

// Set time limit
set_time_limit(1200);
