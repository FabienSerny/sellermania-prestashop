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
	public $data;

	public $customer;
	public $address;
	public $cart;
	public $order;

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
	 * Import order
	 * @param $data
	 */
	public function run($data)
	{
		$this->data = $data;
		$this->preprocessData();
		$this->createCustomer();
		$this->createAddress();
		$this->createCart();
		$this->createOrder();
	}


	/**
	 * Preprocess data array
	 */
	public function preprocessData()
	{
		// Retrieve firstname and lastname
		$names = explode(' ', $this->data['User'][0]['Name']);
		$firstname = $names[0];
		if (isset($names[1]) && !empty($names[1]) && count($names) == 2)
			$lastname = $names[1];
		else
		{
			$lastname = $this->data['User'][0]['Name'];
			$lastname = str_replace($firstname.' ', '', $lastname);
		}

		// Retrieve shipping phone
		$shipping_phone = '0100000000';
		if (isset($this->data['User'][0]['Address']['ShippingPhone']) && !empty($this->data['User'][0]['Address']['ShippingPhone']))
			$shipping_phone = $this->data['User'][0]['Address']['ShippingPhone'];

		// Retrieve currency
		$currency_iso_code = 'EUR';
		if (isset($this->data['OrderInfo']['Amount']['Currency']))
			$currency_iso_code = $this->data['OrderInfo']['Amount']['Currency'];

		// Refill data
		$this->data['User'][0]['FirstName'] = $firstname;
		$this->data['User'][0]['LastName'] = $lastname;
		$this->data['User'][0]['Address']['ShippingPhone'] = $shipping_phone;
		$this->data['OrderInfo']['Amount']['Currency'] = $currency_iso_code;

		// Set match with exception reservations
		$country_exceptionnal_iso_code = array('FX' => 'FR');
		if (isset($country_exceptionnal_iso_code[$this->data['User'][0]['Address']['Country']]))
			$this->data['User'][0]['Address']['Country'] = $country_exceptionnal_iso_code[$this->data['User'][0]['Address']['Country']];

		// Fix data (when only one product, array is not the same)
		if (!isset($this->data['OrderInfo']['Product'][0]))
			$this->data['OrderInfo']['Product'] = array($this->data['OrderInfo']['Product']);
	}


	/**
	 * Create customer
	 */
	public function createCustomer()
	{
		// Create customer as guest
		$this->customer = new Customer();
		$this->customer->id_gender = 9;
		$this->customer->firstname = $this->data['User'][0]['FirstName'];
		$this->customer->lastname = $this->data['User'][0]['LastName'];
		$this->customer->email = Configuration::get('PS_SHOP_EMAIL');
		$this->customer->passwd = md5(pSQL(_COOKIE_KEY_.rand()));
		$this->customer->is_guest = 1;
		$this->customer->active = 1;
		$this->customer->add();

		// Set context
		$this->context->customer = $this->customer;
	}


	/**
	 * Create Address
	 */
	public function createAddress()
	{
		// Create address
		$this->address = new Address();
		$this->address->alias = 'Sellermania';
		$this->address->firstname = $this->data['User'][0]['FirstName'];
		$this->address->lastname = $this->data['User'][0]['LastName'];
		$this->address->address1 = $this->data['User'][0]['Address']['Street1'];
		$this->address->address2 = $this->data['User'][0]['Address']['Street2'];
		$this->address->postcode = $this->data['User'][0]['Address']['ZipCode'];
		$this->address->city = $this->data['User'][0]['Address']['City'];
		$this->address->id_country = Country::getByIso($this->data['User'][0]['Address']['Country']);
		$this->address->phone = $this->data['User'][0]['Address']['ShippingPhone'];
		$this->address->id_customer = $this->customer->id;
		$this->address->active = 1;
		$this->address->add();
	}


	/**
	 * Create Cart
	 */
	public function createCart()
	{
		// Create Cart
		$this->cart = new Cart();
		$this->cart->id_customer = $this->customer->id;
		$this->cart->id_address_invoice = $this->address->id;
		$this->cart->id_address_delivery = $this->address->id;
		$this->cart->id_carrier = 0;
		$this->cart->id_lang = $this->customer->id_lang;
		$this->cart->id_currency = Currency::getIdByIsoCode($this->data['OrderInfo']['Amount']['Currency']);
		$this->cart->recyclable = 0;
		$this->cart->gift = 0;
		$this->cart->add();

		// Update cart with products
		$cart_nb_products = 0;
		foreach ($this->data['OrderInfo']['Product'] as $kp => $product)
		{
			// Get Product Identifiers
			$product = $this->getProductIdentifier($product);
			$this->data['OrderInfo']['Product'][$kp] = $product;

			// Add to cart
			$quantity = (int)$product['QuantityPurchased'];
			$id_product = (int)$product['id_product'];
			$id_product_attribute = (int)$product['id_product_attribute'];
			if ($this->cart->updateQty($quantity, $id_product, $id_product_attribute))
				$cart_nb_products++;
		}

		// Cart update
		$this->cart->update();
	}

	/**
	 * Create order
	 */
	public function createOrder()
	{
		// Remove customer e-mail to avoid email sending
		$customer_email = $this->context->customer->email;
		Db::getInstance()->autoExecute(_DB_PREFIX_.'customer', array('email' => 'NOSEND-SM'), 'UPDATE', '`id_customer` = '.(int)$this->customer->id);
		$this->context->customer->email = 'NOSEND-SM';

		// Create order
		$amount_paid = (float)$this->data['OrderInfo']['TotalAmount']['Amount']['Price'];
		$payment_method = 'SM '.$this->data['OrderInfo']['MarketPlace'].' - '.$this->data['OrderInfo']['OrderId'];
		$payment_module = new SellermaniaPaymentModule();
		$payment_module->name = $this->module->name;
		$payment_module->validateOrder((int)$this->cart->id, Configuration::get('PS_OS_SM_AWAITING'), $amount_paid, $payment_method, NULL, array(), (int)$this->cart->id_currency);
		$id_order = $payment_module->currentOrder;

		// Calcul total product without tax
		$total_products_without_tax = 0;
		foreach ($this->data['OrderInfo']['Product'] as $kp => $product)
		{
			// Calcul total product without tax
			$product_price = $product['Amount']['Price'];
			$vat_rate = 1 + ($product['VatRate'] / 10000);
			$product_price = $product_price / $vat_rate;
			$total_products_without_tax += $product_price;
		}


		// Fix on order (use of autoExecute instead of Insert to be compliant PS 1.4)
		$update = array(
			'total_paid' => (float)$this->data['OrderInfo']['TotalAmount']['Amount']['Price'],
			'total_paid_real' => (float)$this->data['OrderInfo']['TotalAmount']['Amount']['Price'],
			'total_products' => (float)$total_products_without_tax,
			'total_products_wt' => (float)$this->data['OrderInfo']['Amount']['Price'],
			'total_shipping' => (float)$this->data['OrderInfo']['Transport']['Amount']['Price'],
			'date_add' => pSQL(substr($this->data['Paiement']['Date'], 0, 21)),
		);
		Db::getInstance()->autoExecute(_DB_PREFIX_.'orders', $update, 'UPDATE', '`id_order` = '.(int)$id_order);

		// Restore customer e-mail
		Db::getInstance()->autoExecute(_DB_PREFIX_.'customer', array('email' => pSQL($customer_email)), 'UPDATE', '`id_customer` = '.(int)$this->customer->id);
		$this->context->customer->email = $customer_email;
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
}

