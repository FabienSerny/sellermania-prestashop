<?php
/*
* 2010 - 2015 Sellermania / Froggy Commerce / 23Prod SARL
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
*  @copyright	2010-2014 Sellermania / Froggy Commerce / 23Prod SARL
*  @version		1.0
*  @license		http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

// Construct path
$config_path = dirname(__FILE__).'/../../config/config.inc.php';
$module_path = dirname(__FILE__).'/sellermania.php';

// Set _PS_ADMIN_DIR_ define
define('_PS_ADMIN_DIR_', getcwd());

// Keep going if config script is found
if (file_exists($config_path))
{
	include($config_path);
	include($module_path);
	$sellermania = new SellerMania();
	$sellermania->export();
}
else
	die('ERROR');

