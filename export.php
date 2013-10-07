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

// Construct path
$config_path = dirname(__FILE__).'/../../config/config.inc.php';
$module_path = dirname(__FILE__).'/sellermania.php';

// Keep going if config script is found
if (file_exists($config_path))
{
	// Init
	$argument_key = '';
	if (isset($argv[1]))
		$argument_key = $argv[1];

	// Include config file and set default Shop
	define('_PS_ADMIN_DIR_', getcwd());
	include($config_path);
	Context::getContext()->shop->setContext(Shop::CONTEXT_ALL);

	// Check if SellerMania key exists
	if (Configuration::get('SELLERMANIA_KEY') == '')
		die('ERROR1');
	if (Tools::getValue('k') == '' && $argument_key == '')
		die('ERROR2');
	if (Tools::getValue('k') == Configuration::get('SELLERMANIA_KEY') || $argument_key == Configuration::get('SELLERMANIA_KEY'))
	{
		include($module_path);
		$sellermania = new SellerMania();
		$sellermania->export((empty($argument_key) ? 'display' : 'file'), Tools::getValue('l'), Tools::getValue('s'), Tools::getValue('e'));
	}
	else
		die('ERROR3');
}
else
	die('ERROR');

