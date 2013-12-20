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


class SellerManiaImportOrderController
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
	 * Get Product Identifier
	 * @param array $product
	 * @return array $product
	 */
	public function getProductIdentifier($product)
	{
		$fields = array('reference' => 'Sku', 'ean13' => 'Ean');
		$tables = array('product_attribute', 'product');

		// Check fields sku and ean13 on table product_attribute and product
		// If a match is found, we return it
		foreach ($fields as $field_ps => $fields_sm)
			foreach ($tables as $table)
				if (isset($product[$fields_sm]) && strlen($product[$fields_sm]) > 2)
				{
					// Check product attribute
					$pr = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.$table.'` WHERE `'.$field_ps.'` = \''.pSQL($product[$fields_sm]).'\'');
					if ($pr['id_product'] > 0)
					{
						$product['id_product'] = $pr['id_product'];
						$product['id_product_attribute'] = 0;
						if (isset($pr['id_product_attribute']))
							$product['id_product_attribute'] = $pr['id_product_attribute'];
						return $product;
					}
				}

		$product['id_product'] = Configuration::get('SM_DEFAULT_PRODUCT_ID');
		$product['id_product_attribute'] = 0;

		return $product;
	}


	/**
	 * Import order
	 * @param $order
	 */
	public function run($order)
	{
		// Set firstname and lastname
		$names = explode(' ', $order['User'][0]['Name']);
		$firstname = $names[0];
		if (isset($names[1]) && !empty($names[1]) && count($names) == 2)
			$lastname = $names[1];
		else
		{
			$lastname = $order['User'][0]['Name'];
			$lastname = str_replace($firstname.' ', '', $lastname);
		}

		// Set shipping phone
		$shipping_phone = '0100000000';
		if (isset($order['User'][0]['Address']['ShippingPhone']) && !empty($order['User'][0]['Address']['ShippingPhone']))
			$shipping_phone = $order['User'][0]['Address']['ShippingPhone'];

		// Set currency
		$currency_iso_code = 'EUR';
		if (isset($order['OrderInfo']['Amount']['Currency']))
			$currency_iso_code = $order['OrderInfo']['Amount']['Currency'];

		// Set match with exception reservations
		$country_exceptionnal_iso_code = array('FX' => 'FR');
		if (isset($country_exceptionnal_iso_code[$order['User'][0]['Address']['Country']]))
			$order['User'][0]['Address']['Country'] = $country_exceptionnal_iso_code[$order['User'][0]['Address']['Country']];


		// Create customer as guest
		$customer = new Customer();
		$customer->id_gender = 9;
		$customer->firstname = $firstname;
		$customer->lastname = $lastname;
		$customer->email = Configuration::get('PS_SHOP_EMAIL');
		$customer->passwd = md5(pSQL(_COOKIE_KEY_.rand()));
		$customer->is_guest = 1;
		$customer->active = 1;
		$customer->add();

		// Set context
		$this->context->customer = $customer;

		// Create address
		$address = new Address();
		$address->alias = 'Sellermania';
		$address->firstname = $firstname;
		$address->lastname = $lastname;
		$address->address1 = $order['User'][0]['Address']['Street1'];
		$address->address2 = $order['User'][0]['Address']['Street2'];
		$address->postcode = $order['User'][0]['Address']['ZipCode'];
		$address->city = $order['User'][0]['Address']['City'];
		$address->id_country = Country::getByIso($order['User'][0]['Address']['Country']);
		$address->phone = $shipping_phone;
		$address->id_customer = $customer->id;
		$address->active = 1;
		$address->add();

		// Create Cart
		$customer_cart = new Cart();
		$customer_cart->id_customer = $customer->id;
		$customer_cart->id_address_invoice = $address->id;
		$customer_cart->id_address_delivery = $address->id;
		$customer_cart->id_carrier = 0;
		$customer_cart->id_lang = $customer->id_lang;
		$customer_cart->id_currency = Currency::getIdByIsoCode($currency_iso_code);
		$customer_cart->recyclable = 0;
		$customer_cart->gift = 0;
		$customer_cart->add();

		// Init values
		$cart_nb_products = 0;
		$total_products_without_tax = 0;

		// Fix data (when only one product, array is not the same
		if (!isset($order['OrderInfo']['Product'][0]))
			$order['OrderInfo']['Product'] = array($order['OrderInfo']['Product']);

		// Update cart with products
		foreach ($order['OrderInfo']['Product'] as $product)
		{
			// Add to cart
			$product = $this->getProductIdentifier($product);
			$quantity = (int)$product['QuantityPurchased'];
			$id_product = (int)$product['id_product'];
			$id_product_attribute = (int)$product['id_product_attribute'];
			if ($customer_cart->updateQty($quantity, $id_product, $id_product_attribute))
				$cart_nb_products++;

			// Calcul total product without tax
			$product_price = $product['Amount']['Price'];
			$vat_rate = 1 + ($product['VatRate'] / 10000);
			$product_price = $product_price / $vat_rate;
			$total_products_without_tax += $product_price;
		}
		$customer_cart->update();

		// Create order
		$amount_paid = (float)$order['OrderInfo']['TotalAmount']['Amount']['Price'];
		$payment_method = 'SM '.$order['OrderInfo']['MarketPlace'].' - '.$order['OrderInfo']['OrderId'];
		$payment_module = new SellermaniaPaymentModule();
		$payment_module->name = $this->module->name;
		$payment_module->validateOrder((int)$customer_cart->id, Configuration::get('PS_OS_SM_AWAITING'), $amount_paid, $payment_method, NULL, array(), (int)$customer_cart->id_currency);
		$id_order = $payment_module->currentOrder;

		// Fix on order
		$update = array(
			'total_paid' => (float)$order['OrderInfo']['TotalAmount']['Amount']['Price'],
			'total_paid_real' => (float)$order['OrderInfo']['TotalAmount']['Amount']['Price'],
			'total_products' => (float)$total_products_without_tax,
			'total_products_wt' => (float)$order['OrderInfo']['Amount']['Price'],
			'total_shipping' => (float)$order['OrderInfo']['Transport']['Amount']['Price'],
			'date_add' => pSQL(substr($order['Paiement']['Date'], 0, 21)),
		);
		Db::getInstance()->update('orders', $update, '`id_order` = '.(int)$id_order);
	}
}

