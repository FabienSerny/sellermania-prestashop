<?php
/*
* 2010 - 2014 Sellermania / Froggy Commerce / 23Prod SARL
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

if (!defined('_PS_VERSION_'))
	exit;

// Load ImportOrder Controller and DisplayAdminOrderController
require_once(dirname(__FILE__).'/SellerManiaImportOrder.php');
require_once(dirname(__FILE__).'/SellerManiaDisplayAdminOrder.php');

class SellerManiaActionValidateOrderController
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
		$this->ps_version = str_replace('.', '', substr(_PS_VERSION_, 0, 3));
	}

	/**
	 * Run method
	 * @return string $html
	 */
	public function run()
	{
		$skus = array();
		$skus_quantities = array();
		$products = $this->params['order']->getProducts();
		foreach ($products as $product)
		{
			$skus[] = $product['product_reference'];
			$skus_quantities[$product['product_reference']] = $product['product_quantity'];
		}

		$this->syncStock('ORDER', $this->params['order']->id, $skus, $skus_quantities);
	}

	public function syncStock($type, $id, $skus, $skus_quantities)
	{
		try
		{
			// Creating an instance of InventoryClient
			$client = new Sellermania\InventoryClient();
			$client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
			$client->setToken(Configuration::get('SM_ORDER_TOKEN'));
			$client->setEndpoint(Configuration::get('SM_INVENTORY_ENDPOINT'));
			$getResult = $client->getSkuQuantity($skus);

			$xml = '';
			foreach ($getResult['SellermaniaWs']['Results']['GetSkuQuantityResponse']['Sku'] as $sku_line)
				if ($sku_line['Status'] == 'SUCCESS' && isset($skus_quantities[$sku_line['Id']]))
				{
					$quantity = (int)$sku_line['Quantity'] - (int)$skus_quantities[$sku_line['Id']];
					$xml .= '<UpdateInventory><Sku>'.$sku_line['Id'].'</Sku><Quantity>'.$quantity.'</Quantity></UpdateInventory>';
				}
			if (!empty($xml))
			{
				$xml = '<?xml version="1.0" encoding="UTF-8"?><SellermaniaWs>'.$xml.'</SellermaniaWs>';
				$tmpfname = tempnam('/tmp', 'ps_sellermania_');
				file_put_contents($tmpfname, $xml);
				$result = $client->updateInventory($tmpfname);
				unlink($tmpfname);
			}
		}
		catch (\Exception $e)
		{
			// Log error
			$log = '['.$type.' '.$id.'] - '.date('Y-m-d H:i:s').': '.$e->getMessage()."\n";
			$log .= var_export($order, true)."\n";
			file_put_contents(dirname(__FILE__).'/../../log/inventory-error-'.Configuration::get('SELLERMANIA_KEY').'.txt', $log, FILE_APPEND);
		}
	}
}

