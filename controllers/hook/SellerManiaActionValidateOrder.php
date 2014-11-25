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
		// If we are in the import orders context, we do not update anything
		if (defined('SELLERMANIA_IMPORT_ORDERS_CONTEXT'))
			return false;

		// Else we retrieve the SKU
		$skus = array();
		$skus_quantities = array();
		$products = $this->params['order']->getProducts();
		foreach ($products as $product)
		{
			$skus[] = $product['product_reference'];
			$skus_quantities[$product['product_reference']] = - ($product['product_quantity']);
		}

		// If the merchant use the synchronisation options
		if (Configuration::get('SM_STOCK_SYNC_OPTION') == 'yes' && (int)Configuration::get('SM_STOCK_SYNC_NB_CHAR') > 0)
		{
			// We run again over the products
			foreach ($products as $product)
			{
				// We retrieve the X firt or last characters of the reference of each product
				$refcode = substr($product['product_reference'], 0, (int)Configuration::get('SM_STOCK_SYNC_NB_CHAR'));
				$sql_filter = "`reference` LIKE '".$refcode."%'";
				if (Configuration::get('SM_STOCK_SYNC_POSITION') == 'last')
				{
					$refcode = substr($product['product_reference'], - (int)Configuration::get('SM_STOCK_SYNC_NB_CHAR'));
					$sql_filter = "`reference` LIKE '%".$refcode."'";
				}

				// If reference is not empty
				if (!empty($refcode))
				{
					// We try to find a match in product and product_attribute tables
					$matched_products1 = Db::getInstance()->executeS('SELECT `reference` FROM `'._DB_PREFIX_.'product` WHERE '.$sql_filter);
					$matched_products2 = Db::getInstance()->executeS('SELECT `reference` FROM `'._DB_PREFIX_.'product_attribute` WHERE '.$sql_filter);
					$matched_products = array_merge($matched_products1, $matched_products2);

					// Then we continue to fill the skus arrays
					foreach ($matched_products as $match)
					{
						$skus[] = $match['product_reference'];
						$skus_quantities[$match['product_reference']] = - ($product['product_quantity']);
					}
				}
			}
		}

		// We synchronize the stock
		$this->syncStock('ORDER', $this->params['order']->id, $skus, $skus_quantities);
	}

	public function syncStock($type, $id, $new_skus, $new_skus_quantities)
	{
		if (Configuration::get('SM_INVENTORY_ENDPOINT') == '')
			return false;

		try
		{
			// Sleep to handle Sellermania webservice limitation
			sleep(2);

			// We retrieved the sleeping updates and merge them with the new updates
			list($skus, $skus_quantities) = $this->getSleepingUpdates($new_skus, $new_skus_quantities);

			// Creating an instance of InventoryClient
			$client = new Sellermania\InventoryClient();
			$client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
			$client->setToken(Configuration::get('SM_ORDER_TOKEN'));
			$client->setEndpoint(Configuration::get('SM_INVENTORY_ENDPOINT'));
			$getResult = $client->getSkuQuantity($skus);

			// If webservice failed, we saved the update
			if ($getResult['SellermaniaWs']['Header']['Status'] != 'SUCCESS')
			{
				$this->addSleepingUpdates($skus, $skus_quantities);
				return false;
			}


			// If no sku returned, we get out of here!
			if (!isset($getResult['SellermaniaWs']['Results']['GetSkuQuantityResponse']['Sku']))
				return false;

			// Fix data (when only one product, array is not the same)
			if (!isset($getResult['SellermaniaWs']['Results']['GetSkuQuantityResponse']['Sku'][0]))
				$getResult['SellermaniaWs']['Results']['GetSkuQuantityResponse']['Sku'] = array($getResult['SellermaniaWs']['Results']['GetSkuQuantityResponse']['Sku']);

			// Build Xml
			$xml = '';
			foreach ($getResult['SellermaniaWs']['Results']['GetSkuQuantityResponse']['Sku'] as $sku_line)
				if ($sku_line['Status'] == 'SUCCESS' && isset($skus_quantities[$sku_line['Id']]))
				{
					$quantity = (int)$sku_line['Quantity'] + (int)$skus_quantities[$sku_line['Id']];
					$xml .= '<UpdateInventory><Sku>'.$sku_line['Id'].'</Sku><Quantity>'.$quantity.'</Quantity></UpdateInventory>';
				}

			// Update inventory
			if (!empty($xml))
			{
				// Sleep to handle Sellermania webservice limitation
				sleep(2);

				// Build XML
				$xml = '<?xml version="1.0" encoding="UTF-8"?><SellermaniaWs>'.$xml.'</SellermaniaWs>';
				$tmpfname = tempnam('/tmp', 'ps_sellermania_');
				file_put_contents($tmpfname, $xml);
				$result = $client->updateInventory($tmpfname);
				if ($result['SellermaniaWs']['Header']['Status'] != 'SUCCESS')
				{
					$this->addSleepingUpdates($skus, $skus_quantities);
					throw new Exception($result['SellermaniaWs']['Header']['Status'].' '.$result['SellermaniaWs']['Header']['MessageId'].' : '.$result['SellermaniaWs']['Header']['Message']);
				}
				unlink($tmpfname);
			}
		}
		catch (\Exception $e)
		{
			// Log error
			$this->addSleepingUpdates($skus, $skus_quantities);
			$log = '['.$type.' '.$id.'] - '.date('Y-m-d H:i:s').': '.$e->getMessage()."\n";
			@file_put_contents(dirname(__FILE__).'/../../log/inventory-error-'.Configuration::get('SELLERMANIA_KEY').'.txt', $log, FILE_APPEND);
		}
	}

	public function getSleepingUpdates($skus, $skus_quantities)
	{
		// Retrieve data from configuration table
		$json = Configuration::get('SM_SLEEPING_UPDATES');
		if (!empty($json))
			$sleeping_updates = json_decode($json, true);
		else
			$sleeping_updates = array('skus' => array(), 'skus_quantities' => array());

		// Merge array
		foreach ($skus as $sku)
			if (!in_array($sku, $sleeping_updates['skus']))
				$sleeping_updates['skus'][] = $sku;
		foreach ($skus_quantities as $sku => $sku_quantity)
		{
			if (isset($sleeping_updates['skus_quantities'][$sku]))
				$sleeping_updates['skus_quantities'][$sku] += $sku_quantity;
			else
				$sleeping_updates['skus_quantities'][$sku] = $sku_quantity;
		}

		// We delete data from configuration table
		Configuration::updateValue('SM_SLEEPING_UPDATES', '');

		// Return values
		return array($sleeping_updates['skus'], $sleeping_updates['skus_quantities']);
	}

	public function addSleepingUpdates($skus, $skus_quantities)
	{
		$sleeping_updates = array('skus' => $skus, 'skus_quantities' => $skus_quantities);
		Configuration::updateValue('SM_SLEEPING_UPDATES', json_encode($sleeping_updates));
	}
}

