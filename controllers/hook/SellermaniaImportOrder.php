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

class SellermaniaImportOrderController
{
    public $data;

    public $id_lang;
    public $customer;
    public $address;
    public $cart;
    public $order;

    public $country_iso_match_cache = array();

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
        $this->saveSellermaniaOrder();
    }


    /**
     * Preprocess user data array
     */
    public function preprocessUserData($index)
    {
        // Forbidden characters
        $forbidden_characters = array('_', '/', '(', ')', '*', ';', ':', '=', ',', '!', '?', '.', '+', '*', '$', '%', '&', '#', '@');

        // Fix name
        if (!isset($this->data['User'][$index]['Name'])) {
            $this->data['User'][$index]['Name'] = 'Not provided';
        }
        $this->data['User'][$index]['OriginalName'] = $this->data['User'][$index]['Name'];
        $this->data['User'][$index]['Name'] = str_replace($forbidden_characters, ' ', $this->data['User'][$index]['Name']);
        $this->data['User'][$index]['Name'] = preg_replace('/[0-9]+/', '', $this->data['User'][$index]['Name']);
        $this->data['User'][$index]['Name'] = trim($this->data['User'][$index]['Name']);
        if (strlen($this->data['User'][$index]['Name']) < 2)
            $this->data['User'][$index]['Name'] = 'Not provided';
        if (strpos($this->data['User'][$index]['Name'], '/'))
        {
            $name = explode('/', $this->data['User'][$index]['Name']);
            $name[1] = trim($name[1]);
            if (!empty($name[1]))
                $this->data['User'][$index]['Name'] = $name[1];
        }

        // Retrieve firstname and lastname
        $names = explode(' ', trim($this->data['User'][$index]['Name']));
        $firstname = $names[0];
        if (isset($names[1]) && !empty($names[1]) && count($names) == 2)
            $lastname = $names[1];
        else
        {
            $lastname = $this->data['User'][$index]['Name'];
            $lastname = str_replace($firstname.' ', '', $lastname);
        }

        // Retrieve shipping phone
        $shipping_phone = '';
        if (isset($this->data['User'][$index]['Address']['ShippingPhone']) && !empty($this->data['User'][$index]['Address']['ShippingPhone']))
            $shipping_phone = $this->data['User'][$index]['Address']['ShippingPhone'];
        if (isset($this->data['User'][$index]['ShippingPhone']) && !empty($this->data['User'][$index]['ShippingPhone']))
            $shipping_phone = $this->data['User'][$index]['ShippingPhone'];
        if (isset($this->data['User'][$index]['UserPhone']) && !empty($this->data['User'][$index]['UserPhone']))
            $shipping_phone = $this->data['User'][$index]['UserPhone'];
        $shipping_phone = substr(str_replace($forbidden_characters, ' ', $shipping_phone), 0, 16);

        // Retrieve currency
        $currency_iso_code = 'EUR';
        if (isset($this->data['OrderInfo']['Amount']['Currency']) && Currency::getIdByIsoCode($this->data['OrderInfo']['Amount']['Currency']) > 0)
            $currency_iso_code = $this->data['OrderInfo']['Amount']['Currency'];

        // Refill data
        $this->data['User'][$index]['FirstName'] = substr($firstname, 0, 32);
        $this->data['User'][$index]['LastName'] = substr($lastname, 0, 32);
        $this->data['User'][$index]['Address']['ShippingPhonePrestaShop'] = '0100000000';
        if (!empty($shipping_phone) && Validate::isPhoneNumber($shipping_phone))
            $this->data['User'][$index]['Address']['ShippingPhonePrestaShop'] = $shipping_phone;
        $this->data['OrderInfo']['Amount']['Currency'] = $currency_iso_code;

        // Set currency sign
        $id_currency = (int)Currency::getIdByIsoCode($this->data['OrderInfo']['Amount']['Currency']);
        $currency_object = new Currency($id_currency);
        $this->data['OrderInfo']['Amount']['CurrencySign'] = $currency_object->sign;

        // Retrieve from cache
        $country_key = 'FR';
        if (isset($this->data['User'][$index]['Address']['Country']))
        {
            $country_key = $this->data['User'][$index]['Address']['Country'];
            if (isset($this->country_iso_match_cache[$country_key]))
                $this->data['User'][$index]['Address']['Country'] = $this->country_iso_match_cache[$country_key];
            else
            {
                // Set match with exception reservations
                $country_exceptionnal_iso_code = array('FX' => 'FR', 'FRA' => 'FR', 'France' => 'FR');
                if (isset($country_exceptionnal_iso_code[$this->data['User'][$index]['Address']['Country']]))
                    $this->data['User'][$index]['Address']['Country'] = $country_exceptionnal_iso_code[$this->data['User'][$index]['Address']['Country']];
                else
                {
                    // Check if there is a match with a country
                    $id_country = Country::getIdByName(null, $this->data['User'][$index]['Address']['Country']);
                    if ($id_country > 0)
                        $this->data['User'][$index]['Address']['Country'] = Country::getIsoById($id_country);

                    // If Iso is not known, we set FR
                    if (!Validate::isLanguageIsoCode($this->data['User'][$index]['Address']['Country']) || Country::getByIso($this->data['User'][$index]['Address']['Country']) < 1)
                        $this->data['User'][$index]['Address']['Country'] = 'FR';
                }

                // Set cache
                $this->country_iso_match_cache[$country_key] = $this->data['User'][$index]['Address']['Country'];
            }
        }


        // Fix address
        $this->data['User'][$index]['Company'] = substr(str_replace($forbidden_characters, ' ', $this->data['User'][$index]['Company']), 0, 32);
        $this->data['User'][$index]['Address']['Street1'] = str_replace($forbidden_characters, ' ', $this->data['User'][$index]['Address']['Street1']);
        $this->data['User'][$index]['Address']['Street2'] = str_replace($forbidden_characters, ' ', $this->data['User'][$index]['Address']['Street2']);
        $this->data['User'][$index]['Address']['City'] = str_replace($forbidden_characters, ' ', $this->data['User'][$index]['Address']['City']);
        if (empty($this->data['User'][$index]['Address']['Street1']) && !empty($this->data['User'][$index]['Address']['Street2']))
        {
            $this->data['User'][$index]['Address']['Street1'] = $this->data['User'][$index]['Address']['Street2'];
            $this->data['User'][$index]['Address']['Street2'] = '';
        }
        $checkNotProvided = array('Street1' => 'Not provided', 'ZipCode' => '00000', 'City' => 'Not provided', 'Country' => 'FR');
        foreach ($checkNotProvided as $key => $value)
            if (empty($this->data['User'][$index]['Address'][$key]))
                $this->data['User'][$index]['Address'][$key] = $value;
        if (!Validate::isPostCode($this->data['User'][$index]['Address']['ZipCode']))
            $this->data['User'][$index]['Address']['ZipCode'] = '00000';
        $this->data['User'][$index]['Address']['ZipCode'] = substr($this->data['User'][$index]['Address']['ZipCode'], 0, 12);
    }

    /**
     * Preprocess data array
     */
    public function preprocessData()
    {
        // Preprocess User Data
        $this->preprocessUserData(0);
        $this->preprocessUserData(1);

        $this->initializeProductList();

        // Calcul total product without tax
        $existing_ref = array();
        $this->data['OrderInfo']['TotalProductsWithVAT'] = 0;
        $this->data['OrderInfo']['TotalProductsWithoutVAT'] = 0;
        $this->data['OrderInfo']['TotalInsurance'] = 0;
        $this->data['OrderInfo']['RefundedAmount'] = 0;
        $this->data['OrderInfo']['OptionalFeaturePrice'] = 0;
        $this->data['OrderInfo']['TotalPromotionDiscount'] = 0;
        foreach ($this->data['OrderInfo']['Product'] as $kp => $product)
        {
            // If it's not a cancelled product
            if ($product['Status'] != \Sellermania\OrderConfirmClient::STATUS_CANCELLED_CUSTOMER && $product['Status'] != \Sellermania\OrderConfirmClient::STATUS_CANCELLED_SELLER)
            {
                // Calcul total product without tax
                $product_price = $product['Amount']['Price'];
                $vat_rate = 1;
                if (isset($product['VatRate']))
                    $vat_rate = 1 + ($product['VatRate'] / 10000);
                $product_tax = ($product_price / $vat_rate) * ($vat_rate - 1);
                $this->data['OrderInfo']['TotalProductsWithoutVAT'] += (($product_price / $vat_rate) * $product['QuantityPurchased']);
                $this->data['OrderInfo']['TotalProductsWithVAT'] += ($product_price * $product['QuantityPurchased']);

                // Calcul total Insurance
                if (isset($product['InsurancePrice']['Amount']['Price']))
                    $this->data['OrderInfo']['TotalInsurance'] += ($product['InsurancePrice']['Amount']['Price'] * $product['QuantityPurchased']);

                // Calcul total Promotion Discount
                if (isset($product['ItemPromotionDiscount']['Amount']['Price']))
                    $this->data['OrderInfo']['TotalPromotionDiscount'] += $product['ItemPromotionDiscount']['Amount']['Price'];

                // Calcul total refunded
                if (isset($product['RefundedAmount']['Amount']['Price']))
                    $this->data['OrderInfo']['RefundedAmount'] += $product['RefundedAmount']['Amount']['Price'];

                // Calcul total optional feature price
                if (isset($product['OptionalFeaturePrice']['Amount']['Price']))
                    $this->data['OrderInfo']['OptionalFeaturePrice'] += $product['OptionalFeaturePrice']['Amount']['Price'] * $product['QuantityPurchased'];

                // Create order detail (only create order detail for unmatched product)
                $this->data['OrderInfo']['Product'][$kp]['ProductVAT'] = array('unit' => $product_tax / $product['QuantityPurchased'], 'total' => $product_tax, 'rate' => $vat_rate);
            }

            // Fix Ean
            if (!isset($this->data['OrderInfo']['Product'][$kp]['Ean']))
                $this->data['OrderInfo']['Product'][$kp]['Ean'] = '';

            // Fix Sku
            if (!isset($this->data['OrderInfo']['Product'][$kp]['Sku']))
                $this->data['OrderInfo']['Product'][$kp]['Sku'] = '';

            // Fix name
            $search = array('(', ')');
            $this->data['OrderInfo']['Product'][$kp]['ItemName'] = html_entity_decode($this->data['OrderInfo']['Product'][$kp]['ItemName']);
            $this->data['OrderInfo']['Product'][$kp]['ItemName'] = str_replace($search, '', $this->data['OrderInfo']['Product'][$kp]['ItemName']);

            // Fix non existing variable
            if (!isset($this->data['OrderInfo']['Product'][$kp]['ProductVAT']['total']))
                $this->data['OrderInfo']['Product'][$kp]['ProductVAT']['total'] = 0;
            if (!isset($this->data['OrderInfo']['Product'][$kp]['ProductVAT']['unit']))
                $this->data['OrderInfo']['Product'][$kp]['ProductVAT']['unit'] = 0;
            if (!isset($this->data['OrderInfo']['Product'][$kp]['Amount']['Price']))
                $this->data['OrderInfo']['Product'][$kp]['Amount']['Price'] = 0;

            // Get Product Identifiers
            $this->data['OrderInfo']['Product'][$kp] = $this->getProductIdentifier($this->data['OrderInfo']['Product'][$kp]);

            // If reference is not found
            $alert_email = Configuration::get('SM_ALERT_MISSING_REF_MAIL');
            if ($this->data['OrderInfo']['Product'][$kp]['id_product'] == Configuration::get('SM_DEFAULT_PRODUCT_ID') &&
                Configuration::get('SM_ALERT_MISSING_REF_OPTION') == 'yes' &&
                !empty($alert_email) && Validate::isEmail($alert_email))
            {
                $id_lang = Configuration::get('PS_LANG_DEFAULT');
                $alert_directory_mail = dirname(__FILE__).'/../../mails/';
                $templateVars = array(
                    '{sku}' => $this->data['OrderInfo']['Product'][$kp]['Sku'],
                    '{ean13}' => $this->data['OrderInfo']['Product'][$kp]['Ean'],
                    '{marketplace}' => $this->data['OrderInfo']['MarketPlace'],
                    '{order_reference}' => $this->data['OrderInfo']['OrderId'],
                );
                Mail::Send($id_lang, 'missing_ref', sprintf(Mail::l('Sellermania - Missing reference on PrestaShop: %s', (int)$id_lang), $this->data['OrderInfo']['Product'][$kp]['Sku']), $templateVars, $alert_email, null, null, null, null, null, $alert_directory_mail);
            }

            // If product already exists
            $sku = $this->data['OrderInfo']['Product'][$kp]['Sku'];
            $ean = $this->data['OrderInfo']['Product'][$kp]['Ean'];
            if (isset($existing_ref[$sku.'-'.$ean]))
            {
                $pointer = $existing_ref[$sku.'-'.$ean];
                $this->data['OrderInfo']['Product'][$pointer]['QuantityPurchased'] += $this->data['OrderInfo']['Product'][$kp]['QuantityPurchased'];
                $this->data['OrderInfo']['Product'][$pointer]['ShippingFee']['Amount']['Price'] += $this->data['OrderInfo']['Product'][$kp]['ShippingFee']['Amount']['Price'];
                $this->data['OrderInfo']['Product'][$pointer]['ProductVAT']['total'] += $this->data['OrderInfo']['Product'][$kp]['ProductVAT']['total'];
                unset($this->data['OrderInfo']['Product'][$kp]);
            }
            else
                $existing_ref[$sku.'-'.$ean] = $kp;
        }

        // Fix paiement date
        if (!isset($this->data['Paiement']['Date']))
            $this->data['Paiement']['Date'] = date('Y-m-d H:i:s');
        $this->data['Paiement']['Date'] = substr($this->data['Paiement']['Date'], 0, 19);
        $this->data['OrderInfo']['Date'] = substr($this->data['OrderInfo']['Date'], 0, 19);
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
        $this->customer->email = Configuration::get('SM_CATCH_ALL_MAIL_ADDRESS');
        $this->customer->passwd = md5(pSQL(_COOKIE_KEY_.rand()));
        $this->customer->is_guest = 1;
        $this->customer->active = 1;
        if (Configuration::get('SM_IMPORT_DEFAULT_CUSTOMER_GROUP') > 0) {
            $this->customer->groupBox = [ (int)Configuration::get('SM_IMPORT_DEFAULT_CUSTOMER_GROUP') ];
        }
        if (substr(_PS_VERSION_, 0, 3) == '1.4')
        {
            $return = $this->customer->validateFields(false, true);
            if ($return !== true)
                throw new Exception('Error on customer creation: '.$return);
        }
        $this->customer->add();

        // Fix lang for PS 1.4
        $this->id_lang = Configuration::get('PS_LANG_DEFAULT');
        if (property_exists($this->customer, 'id_lang'))
        {
            $rp = new ReflectionProperty($this->customer,'id_lang');
            if (version_compare(_PS_VERSION_, '1.5') >= 0 && !$rp->isProtected())
                $this->id_lang = $this->customer->id_lang;
        }

        // Set context
        $this->context->customer = $this->customer;
    }


    /**
     * Create Address
     */
    public function createAddress($type = 'Shipping', $data = array())
    {
        // If data is not set, we set it with shipping address
        if (empty($data))
            $data = $this->data['User'][0];

        // Create address
        $this->address = new Address();
        $this->address->alias = 'Sellermania '.$type;
        $this->address->company = $data['Company'];
        $this->address->firstname = $data['FirstName'];
        $this->address->lastname = $data['LastName'];
        $this->address->address1 = $data['Address']['Street1'];
        $this->address->address2 = $data['Address']['Street2'];
        $this->address->postcode = $data['Address']['ZipCode'];
        $this->address->city = $data['Address']['City'];
        $this->address->id_country = Country::getByIso($data['Address']['Country']);
        $this->address->phone = $data['Address']['ShippingPhonePrestaShop'];
        $this->address->phone_mobile = $data['Address']['ShippingPhonePrestaShop'];
        $this->address->id_customer = $this->customer->id;
        if ($type == 'Shipping' && isset($this->data['OrderInfo']['DeliveryInstructions'])) {
            $this->address->other = $this->data['OrderInfo']['DeliveryInstructions'];
        }
        $this->address->active = 1;
        if (substr(_PS_VERSION_, 0, 3) == '1.4')
        {
            $return = $this->address->validateFields(false, true);
            if ($return !== true)
                throw new Exception('Error on address creation: '.$return);
        }

        // Enable country if not active
        $country = new Country($this->address->id_country);
        if ($country->active != 1)
        {
            $country->active = 1;
            $country->update();
        }

        // Then we create the address
        $this->address->add();
        return $this->address->id;
    }


    /**
     * Create Cart
     */
    public function createCart()
    {
        // Retrieve a carrier
        $id_carrier = Db::getInstance()->getValue('SELECT `id_carrier` FROM `'._DB_PREFIX_.'carrier` WHERE `id_carrier` = '.(int)Configuration::get('SM_IMPORT_DEFAULT_CARRIER').' AND `active` = 1 AND `deleted` = 0');
        if ($id_carrier < 1) {
            $id_carrier = Db::getInstance()->getValue('SELECT `id_carrier` FROM `'._DB_PREFIX_.'carrier` WHERE `active` = 1 AND `deleted` = 0');
        }

        // Load currency in context
        $this->context->currency = new Currency(Currency::getIdByIsoCode($this->data['OrderInfo']['Amount']['Currency']));

        if (!ValidateCore::isLoadedObject($this->context->currency)) {
            throw new Exception('Currency not found with iso code: '.$this->data['OrderInfo']['Amount']['Currency']);
        }

        // Create Cart
        $this->cart = new Cart();
        $this->cart->id_customer = $this->customer->id;
        $this->cart->id_address_invoice = $this->address->id;
        $this->cart->id_address_delivery = $this->address->id;
        $this->cart->id_carrier = $id_carrier;
        $this->cart->id_lang = $this->id_lang;
        $this->cart->id_currency = $this->context->currency->id;
        $this->cart->recyclable = 0;
        $this->cart->gift = 0;
        $this->cart->add();

        // Update cart with products
        foreach ($this->data['OrderInfo']['Product'] as $kp => $product)
        {
            // Add to cart
            $quantity = (int)$product['QuantityPurchased'];
            $id_product = (int)$product['id_product'];
            $id_product_attribute = (int)$product['id_product_attribute'];
            if (!$this->cart->updateQty($quantity, $id_product, $id_product_attribute))
                $this->cart->updateQty($quantity, Configuration::get('SM_DEFAULT_PRODUCT_ID'), 0);
        }

        // Cart update
        $this->cart->update();

        // Check if cart is not empty (in case of Sellermania order with only one product and a quantity of 0)
        $cart_products = $this->cart->getProducts();
        if (!$cart_products || empty($cart_products))
            throw new Exception('Cart seems empty, check the details of the order if the quantity is superior to 0');

        // Flush cart delivery cache
        if (version_compare(_PS_VERSION_, '1.5') >= 0)
        {
            $this->cart->getDeliveryOptionList(null, true);
            $this->cart->getDeliveryOption(null, false, false);
        }
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
        $this->context->customer->clearCache();

        // Retrieve amount paid
        $amount_paid = (float)$this->data['OrderInfo']['TotalAmount']['Amount']['Price'];

        // Fix for PS 1.4 to avoid PS_OS_ERROR status, amount paid will be fixed after order creation anyway
        if (version_compare(_PS_VERSION_, '1.5') < 0)
            $amount_paid = (float)(Tools::ps_round((float)($this->cart->getOrderTotal(true, Cart::BOTH)), 2));

        // Create order
        $payment_method = $this->data['OrderInfo']['MarketPlace'];
        $payment_module = new SellermaniaPaymentModule();
        $payment_module->name = $this->module->name;
        $payment_module->validateOrder((int)$this->cart->id, Configuration::get('PS_OS_SM_AWAITING'), $amount_paid, $payment_method, NULL, array(), (int)$this->cart->id_currency, false, $this->customer->secure_key);
        $id_order = $payment_module->currentOrder;
        $this->order = new Order((int)$id_order);

        // Restore customer e-mail
        Db::getInstance()->autoExecute(_DB_PREFIX_.'customer', array('email' => pSQL($customer_email)), 'UPDATE', '`id_customer` = '.(int)$this->customer->id);
        $this->context->customer->email = $customer_email;
        $this->context->customer->clearCache();

        // If last order status is not PS_OS_SM_AWAITING, we update it
        if ($this->order->getCurrentState() != Configuration::get('PS_OS_SM_AWAITING'))
        {
            // Create new OrderHistory
            $history = new OrderHistory();
            $history->id_order = $this->order->id;
            $history->id_employee = (int)$this->context->employee->id;
            $history->id_order_state = (int)Configuration::get('PS_OS_SM_AWAITING');
            $history->changeIdOrderState((int)Configuration::get('PS_OS_SM_AWAITING'), $this->order->id);
            $history->add();
        }

        // Fix order depending on version
        $this->fixOrder(true);

        // Since we update the order values by direct SQL request, we need to flush the Object cache
        // "changeIdOrderState" method uses order "update" method (old values were set again)
        $this->order->clearCache();
    }

    /**
     * Save Sellermania order
     */
    public function saveSellermaniaOrder($error = '')
    {
        $id_currency = Currency::getIdByIsoCode($this->data['OrderInfo']['Amount']['Currency']);
        $amount_total = $this->data['OrderInfo']['TotalAmount']['Amount']['Price'];

        $sellermania_order = new SellermaniaOrder();
        $sellermania_order->marketplace = trim($this->data['OrderInfo']['MarketPlace']);
        $sellermania_order->customer_name = $this->data['User'][0]['Name'];
        $sellermania_order->ref_order = trim($this->data['OrderInfo']['OrderId']);
        $sellermania_order->amount_total = Tools::displayPrice($amount_total, $id_currency);
        $sellermania_order->info = json_encode($this->data);
        $sellermania_order->error = $error;
        $sellermania_order->id_order = $this->order->id;
        $sellermania_order->id_employee_accepted = 0;
        $sellermania_order->date_payment = substr($this->data['Paiement']['Date'], 0, 19);
        $sellermania_order->date_add = date('Y-m-d H:i:s');
        $sellermania_order->add();
    }


    /**
     * Get Product Identifier
     * @param array $product
     * @return array $product
     */
    public function getProductIdentifier(&$product)
    {
        $fields = array('reference' => 'Sku', 'upc' => 'Sku', 'ean13' => 'Ean');
        $tables = array('product_attribute', 'product');

        // Check fields sku and ean13 on table product_attribute and product
        // If a match is found, we return it
        foreach ($fields as $field_ps => $fields_sm)
            foreach ($tables as $table)
                if (isset($product[$fields_sm]) && strlen($product[$fields_sm]) > 2)
                {
                    // Check product attribute
                    $pr = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.$table.'` WHERE `'.$field_ps.'` = \''.pSQL($product[$fields_sm]).'\'');

                    // Check with chain option
                    if (!isset($pr['id_product']) || $pr['id_product'] < 1)
                        if (Configuration::get('SM_STOCK_SYNC_OPTION_2') == 'yes' && (int)Configuration::get('SM_STOCK_SYNC_NB_CHAR') > 0)
                        {
                            // Search product by matching first or last digit
                            $search_filter = substr($product[$fields_sm], 0, (int)Configuration::get('SM_STOCK_SYNC_NB_CHAR')).'%';
                            if (Configuration::get('SM_STOCK_SYNC_POSITION') == 'last')
                                $search_filter = '%'.substr($product[$fields_sm], - (int)Configuration::get('SM_STOCK_SYNC_NB_CHAR'));
                            $pr = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.$table.'` WHERE `'.$field_ps.'` LIKE \''.pSQL($search_filter).'\'');

                            // Alter Sku if matched
                            if (isset($pr['id_product']) && $pr['id_product'] > 0)
                                $product[$fields_sm] = $pr[$field_ps];
                        }

                    // If product is matched
                    if (isset($pr['id_product']) && $pr['id_product'] > 0)
                    {
                        $product['id_product'] = $pr['id_product'];
                        $product['id_product_attribute'] = 0;
                        if (isset($pr['id_product_attribute']))
                            $product['id_product_attribute'] = $pr['id_product_attribute'];

                        // If product is disabled, we return the default product
                        $active = Db::getInstance()->getValue('SELECT `active` FROM `'._DB_PREFIX_.'product` WHERE `id_product` = '.(int)$product['id_product']);
                        if ($active != 1)
                        {
                            $product['id_product'] = Configuration::get('SM_DEFAULT_PRODUCT_ID');
                            $product['id_product_attribute'] = 0;
                        }

                        return $product;
                    }
                }

        // Check if Sku is id_unique
        $ps_ids = preg_split("/[\s,;#_\-\/|]+/", $product['Sku']);
        if (count($ps_ids) == 2) {

            $idp = (int)$ps_ids[0];
            $idpa = (int)$ps_ids[1];

            // Search for product attribute
            $row = Db::getInstance()->getRow('
            SELECT `id_product`, `id_product_attribute`, `reference`, `ean13`
            FROM `'._DB_PREFIX_.'product_attribute`
            WHERE `id_product` = '.(int)$idp.'
            AND `id_product_attribute` = '.(int)$idpa);

            // If not found, search for product
            if ($row['id_product_attribute'] < 1) {
                $row = Db::getInstance()->getRow('
                SELECT `id_product`, `reference`, `ean13`
                FROM `'._DB_PREFIX_.'product`
                WHERE `id_product` = '.(int)$idp);
            }

            // If found we set ids and SKU and reference
            if ($row['id_product'] > 0) {
                $product['id_product'] = (int)$idp;
                $product['id_product_attribute'] = (int)$idpa;
                $product['Sku'] = $row['reference'];
                $product['ean'] = $row['ean13'];
                return $product;
            }
        }

        // If product unmatch, we return the default Sellermania product, method createOrderDetail will fix this
        $product['id_product'] = Configuration::get('SM_DEFAULT_PRODUCT_ID');
        $product['id_product_attribute'] = 0;

        return $product;
    }


    /************** FIX ORDER **************/

    /**
     * Fix customer on PrestaShop
     */
    public function fixCustomerAddress()
    {
        // Update customer
        $this->customer = new Customer($this->order->id_customer);
        $this->customer->firstname = $this->data['User'][0]['FirstName'];
        $this->customer->lastname = $this->data['User'][0]['LastName'];
        $this->customer->update();

        // If two differents addresses and only one registered, we create the other address
        if ($this->order->id_address_delivery == $this->order->id_address_invoice
            && isset($this->data['User'][1]['Address']['Street1']) && !empty($this->data['User'][1]['Address']['Street1'])
            && $this->data['User'][0]['Address']['Street1'] != $this->data['User'][1]['Address']['Street1']
        ) {
            $id_address_invoice = $this->createAddress('Billing', $this->data['User'][1]);
            $this->order->id_address_invoice = $id_address_invoice;
            $this->order->update();
        }

        // Update delivery address
        $this->address = new Address($this->order->id_address_delivery);
        $this->address->company = $this->data['User'][0]['Company'];
        $this->address->firstname = $this->data['User'][0]['FirstName'];
        $this->address->lastname = $this->data['User'][0]['LastName'];
        $this->address->address1 = $this->data['User'][0]['Address']['Street1'];
        $this->address->address2 = $this->data['User'][0]['Address']['Street2'];
        $this->address->postcode = $this->data['User'][0]['Address']['ZipCode'];
        $this->address->city = $this->data['User'][0]['Address']['City'];
        $this->address->id_country = Country::getByIso($this->data['User'][0]['Address']['Country']);
        $this->address->phone = $this->data['User'][0]['Address']['ShippingPhonePrestaShop'];
        $this->address->update();

        // If different, update billing address
        if ($this->order->id_address_delivery != $this->order->id_address_invoice
            && isset($this->data['User'][1]['Address']['Street1']) && !empty($this->data['User'][1]['Address']['Street1'])
        ) {
            $this->address = new Address($this->order->id_address_invoice);
            $this->address->company = $this->data['User'][1]['Company'];
            $this->address->firstname = $this->data['User'][1]['FirstName'];
            $this->address->lastname = $this->data['User'][1]['LastName'];
            $this->address->address1 = $this->data['User'][1]['Address']['Street1'];
            $this->address->address2 = $this->data['User'][1]['Address']['Street2'];
            $this->address->postcode = $this->data['User'][1]['Address']['ZipCode'];
            $this->address->city = $this->data['User'][1]['Address']['City'];
            $this->address->id_country = Country::getByIso($this->data['User'][1]['Address']['Country']);
            $this->address->phone = $this->data['User'][1]['Address']['ShippingPhonePrestaShop'];
            $this->address->update();
        }
    }

    /**
     * Fix order on PrestaShop
     */
    public function fixOrder($fix_details = true)
    {
        $this->fixCustomerAddress();

        // Handle optionnal feature price (handling fees)
        if (isset($this->data['OrderInfo']['OptionalFeaturePrice']) && $this->data['OrderInfo']['OptionalFeaturePrice'] > 0) {
            $this->data['OrderInfo']['Product'][] = array(
                'ItemName' => 'Frais de gestion',
                'Sku' => 'Frais de gestion',
                'QuantityPurchased' => 1,
                'Amount' => array('Price' => $this->data['OrderInfo']['OptionalFeaturePrice']),
                'VatRate' => 0,
            );
            $this->data['OrderInfo']['TotalProductsWithoutVAT'] += $this->data['OrderInfo']['OptionalFeaturePrice'];
        }

        // Executing different actions depending on PS Version
        if (version_compare(_PS_VERSION_, '1.5') >= 0)
            $this->fixOrder15($fix_details);
        else
            $this->fixOrder14($fix_details);
    }

    /************** FIX ORDER 1.4 **************/


    /**
     * Fix order on PrestaShop 1.4
     */
    public function fixOrder14($fix_details = true)
    {
        // Fix order detail
        if ($fix_details)
            foreach ($this->data['OrderInfo']['Product'] as $kp => $product)
                $this->fixOrderDetail14($this->order->id, $product);

        // Fix on order (use of autoExecute instead of Insert to be compliant PS 1.4)
        $update = array(
            'total_paid' => (float)$this->data['OrderInfo']['TotalAmount']['Amount']['Price'],
            'total_paid_real' => (float)$this->data['OrderInfo']['TotalAmount']['Amount']['Price'],
            'total_products' => (float)$this->data['OrderInfo']['TotalProductsWithoutVAT'],
            'total_products_wt' => (float)$this->data['OrderInfo']['TotalProductsWithVAT'],
            'total_shipping' => (float)$this->data['OrderInfo']['Transport']['Amount']['Price'],
            'date_add' => pSQL(substr($this->data['OrderInfo']['Date'], 0, 19)),
        );
        Db::getInstance()->autoExecute(_DB_PREFIX_.'orders', $update, 'UPDATE', '`id_order` = '.(int)$this->order->id);
    }

    /**
     * Create order detail
     * @param $id_order
     * @param $product
     */
    public function fixOrderDetail14($id_order, $product)
    {
        // Calcul price without tax
        $product_price_with_tax = $product['Amount']['Price'];
        $vat_rate = 1 + ($product['VatRate'] / 10000);
        $product_price_without_tax = $product_price_with_tax / $vat_rate;

        // SQL data
        $sql_data = array(
            'id_order' => (int)$id_order,
            'product_id' => $product['id_product'],
            'product_attribute_id' => $product['id_product_attribute'],
            'product_name' => pSQL($product['ItemName']),
            'product_quantity' => (int)$product['QuantityPurchased'],
            'product_quantity_in_stock' => 0,
            'product_price' => (float)$product_price_without_tax,
            'tax_rate' => (float)($product['VatRate'] / 100),
            'tax_name' => ((float)($product['VatRate'] / 100)).'%',
            'product_ean13' => pSQL($product['Ean']),
            'product_reference' => pSQL($product['Sku']),
        );


        // We check if the product has a match
        // If yes, we update it, if not, we continue
        $id_order_detail = (int)Db::getInstance()->getValue('
        SELECT `id_order_detail`
        FROM `'._DB_PREFIX_.'order_detail`
        WHERE `id_order` = '.(int)$id_order.'
        AND `product_reference` = \''.pSQL($product['Sku']).'\'');

        // We check if a default Sellermania product is in Order Detail
        // If yes, we update it, if not, we create a new Order Detail
        if ($id_order_detail < 1)
            $id_order_detail = (int)Db::getInstance()->getValue('
            SELECT `id_order_detail`
            FROM `'._DB_PREFIX_.'order_detail`
            WHERE `id_order` = '.(int)$id_order.'
            AND `product_id` = '.(int)Configuration::get('SM_DEFAULT_PRODUCT_ID').'
            AND `product_name` = \'Sellermania product\'');

        if ($id_order_detail > 0)
        {
            $where = '`id_order` = '.(int)$id_order.' AND `id_order_detail` = '.(int)$id_order_detail;
            Db::getInstance()->autoExecute(_DB_PREFIX_.'order_detail', $sql_data, 'UPDATE', $where);
        }
        else
        {
            Db::getInstance()->autoExecute(_DB_PREFIX_.'order_detail', $sql_data, 'INSERT');
            $id_order_detail = Db::getInstance()->Insert_ID();
        }
    }



    /************** CREATE ORDER 1.5 / 1.6 **************/


    /**
     * Create order on PrestaShop 1.5 / 1.6
     */
    public function fixOrder15($fix_details = true)
    {
        // Fix order detail
        if ($fix_details)
            foreach ($this->data['OrderInfo']['Product'] as $kp => $product)
                $this->fixOrderDetail15($this->order->id, $product);

        // Calcul shipping without tax
        $total_shipping_tax_incl = (float)$this->data['OrderInfo']['Transport']['Amount']['Price'];
        $total_shipping_tax_excl = (float)$this->data['OrderInfo']['Transport']['Amount']['Price'];
        if (isset($this->order->carrier_tax_rate) && !is_null($this->order->carrier_tax_rate) && (float)$this->order->carrier_tax_rate > 0) {
            $total_shipping_tax_excl = round(100 * $total_shipping_tax_excl / ((100 + (float)$this->order->carrier_tax_rate)), 6);
        }

        // Fix on order (use of autoExecute instead of Insert to be compliant PS 1.4)
        $update = array(
            'total_paid' => (float)$this->data['OrderInfo']['TotalAmount']['Amount']['Price'],
            'total_paid_tax_incl' => (float)$this->data['OrderInfo']['TotalAmount']['Amount']['Price'],
            'total_paid_tax_excl' => (float)$this->data['OrderInfo']['TotalProductsWithoutVAT'] + (float)$this->data['OrderInfo']['Transport']['Amount']['Price'],
            'total_paid_real' => (float)$this->data['OrderInfo']['TotalAmount']['Amount']['Price'],
            'total_products' => (float)$this->data['OrderInfo']['TotalProductsWithoutVAT'],
            'total_products_wt' => (float)$this->data['OrderInfo']['TotalProductsWithVAT'],
            'total_shipping' => (float)$this->data['OrderInfo']['Transport']['Amount']['Price'],
            'total_shipping_tax_incl' => (float)$total_shipping_tax_incl,
            'total_shipping_tax_excl' => (float)$total_shipping_tax_excl,
            'date_add' => pSQL(substr($this->data['OrderInfo']['Date'], 0, 19)),
        );
        Db::getInstance()->update('orders', $update, '`id_order` = '.(int)$this->order->id);

        // Fix payment
        $updateTab = array(
            'amount' => $update['total_paid_real'],
            'payment_method' => $this->data['OrderInfo']['MarketPlace'],
        );
        $where = '`order_reference` = \''.pSQL($this->order->reference).'\'';
        Db::getInstance()->update('order_payment', $updateTab, $where);

        // Fix carrier
        $carrier_update = array(
            'shipping_cost_tax_incl' => (float)$this->data['OrderInfo']['Transport']['Amount']['Price'],
            'shipping_cost_tax_excl' => (float)$this->data['OrderInfo']['Transport']['Amount']['Price'],
        );
        $where = '`id_order` = \''.pSQL($this->order->id).'\'';
        Db::getInstance()->update('order_carrier', $carrier_update, $where);

        // Fix invoice
        unset($update['total_paid']);
        unset($update['total_paid_real']);
        unset($update['total_shipping']);
        $where = '`id_order` = '.(int)$this->order->id;
        Db::getInstance()->update('order_invoice', $update, $where);

        // Update Sellermania default product quantity
        Db::getInstance()->update('stock_available', array('quantity' => 0), '`id_product` = '.Configuration::get('SM_DEFAULT_PRODUCT_ID'));
    }


    /**
     * Create order detail
     * @param $id_order
     * @param $product
     */
    public function fixOrderDetail15($id_order, $product)
    {
        // Calcul prices
        if (!isset($product['VatRate']))
            $product['VatRate'] = 0;
        $product_price_with_tax = $product['Amount']['Price'];
        $vat_rate = 1 + ($product['VatRate'] / 10000);
        $product_price_without_tax = $product_price_with_tax / $vat_rate;

        // Get order invoice ID
        $id_order_invoice = Db::getInstance()->getValue('
        SELECT `id_order_invoice` FROM `'._DB_PREFIX_.'order_invoice`
        WHERE `id_order` = '.(int)$id_order);

        // SQL data
        $sql_data = array(
            'id_order' => (int)$id_order,
            'product_id' => $product['id_product'],
            'product_attribute_id' => $product['id_product_attribute'],
            'product_name' => pSQL($product['ItemName']),
            'product_quantity' => (int)$product['QuantityPurchased'],
            'product_quantity_in_stock' => 0,
            'product_price' => (float)$product_price_without_tax,
            'tax_rate' => (float)($product['VatRate'] / 100),
            'tax_name' => ((float)($product['VatRate'] / 100)).'%',
            'product_ean13' => pSQL($product['Ean']),
            'product_reference' => pSQL($product['Sku']),

            'id_order_invoice' => $id_order_invoice,
            'id_warehouse' => 0,
            'id_shop' => Context::getContext()->shop->id,
            'total_price_tax_incl' => (float)($product_price_with_tax * (int)$product['QuantityPurchased']),
            'total_price_tax_excl' => (float)($product_price_without_tax * (int)$product['QuantityPurchased']),
            'unit_price_tax_incl' => (float)$product_price_with_tax,
            'unit_price_tax_excl' => (float)$product_price_without_tax,
            'original_product_price' => (float)$product_price_without_tax,
        );

        $id_tax = 0;
        $id_tax_sql = Db::getInstance()->getValue('
        SELECT `id_tax` FROM `'._DB_PREFIX_.'tax`
        WHERE `rate` = \''.((float)($product['VatRate'] / 100)).'\' AND active = 1');
        if ($id_tax_sql > 0) {
            $id_tax = $id_tax_sql;
        }
        $sql_data_tax = array(
            'id_tax' => $id_tax,
            'unit_amount' => (float)$product['ProductVAT']['unit'],
            'total_amount' => (float)$product['ProductVAT']['total'],
        );

        // We check if the product has a match
        // If yes, we update it, if not, we continue
        $id_order_detail = (int)Db::getInstance()->getValue('
        SELECT `id_order_detail`
        FROM `'._DB_PREFIX_.'order_detail`
        WHERE `id_order` = '.(int)$id_order.'
        AND (
            `product_reference` = \''.pSQL($product['Sku']).'\' OR
            (`product_ean13` = \''.pSQL($product['Ean']).'\' AND `product_ean13` != \'\')
        )');

        // We check if a default Sellermania product is in Order Detail
        // If yes, we update it, if not, we create a new Order Detail
        if ($id_order_detail < 1)
            $id_order_detail = Db::getInstance()->getValue('
            SELECT `id_order_detail`
            FROM `'._DB_PREFIX_.'order_detail`
            WHERE `id_order` = '.(int)$id_order.'
            AND `product_id` = '.(int)Configuration::get('SM_DEFAULT_PRODUCT_ID').'
            AND `product_name` = \'Sellermania product\'');

        if ($id_order_detail > 0)
        {
            $where = '`id_order` = '.(int)$id_order.' AND `id_order_detail` = '.(int)$id_order_detail;
            Db::getInstance()->update('order_detail', $sql_data, $where);

            $where = '`id_order_detail` = '.(int)$id_order_detail;
            Db::getInstance()->update('order_detail_tax', $sql_data_tax, $where);
        }
        else
        {
            Db::getInstance()->insert('order_detail', $sql_data);
            $id_order_detail = Db::getInstance()->Insert_ID();

            $sql_data_tax['id_order_detail'] = (int)$id_order_detail;
            Db::getInstance()->insert('order_detail_tax', $sql_data_tax);
        }
    }

    private function initializeProductList()
    {
        // Fix data (when only one product, array is not the same)
        if (!isset($this->data['OrderInfo']['Product'][0])) {
            $this->data['OrderInfo']['Product'] = array($this->data['OrderInfo']['Product']);
        }

        return $this;
    }

    /**
     * @param SellermaniaOrder $sellermaniaOrder
     * @param array $sellermaniaOrderInfo
     */
    public function refreshOrder(SellermaniaOrder $sellermaniaOrder, Array $sellermaniaOrderInfo)
    {
        $this->data = $sellermaniaOrderInfo;
        $this->order = new Order($sellermaniaOrder->id_order);

        // Prepare the products list / merge quantities
        $this->preprocessData();

        $psProducts = $this->order->getProducts();
        foreach ($psProducts as $productIndex => $productDetail) {
            // Remove all product called "Frais de gestion"
            if ($productDetail['product_id'] == 0) {
                unset($psProducts[$productIndex]);
            }
        }

        foreach ($this->data['OrderInfo']['Product'] as &$sellermaniaProduct) {
            $sellermaniaProduct = $this->getProductIdentifier($sellermaniaProduct);
            $found = false;
            foreach ($psProducts as $psProduct) {
                $found = $sellermaniaProduct['id_product'] == $psProduct['product_id'] &&
                    $sellermaniaProduct['id_product_attribute'] == $psProduct['product_attribute_id']
                ;
                if ($found) {
                    if ($sellermaniaProduct['QuantityPurchased'] != $psProduct['product_quantity']) {
                        $orderDetail = new OrderDetail($psProduct['id_order_detail']);
                        $orderDetail->product_quantity = $sellermaniaProduct['QuantityPurchased'];
                        $orderDetail->product_name = $sellermaniaProduct['ItemName'];
                        $orderDetail->update();

                        $quantity = -abs(($sellermaniaProduct['QuantityPurchased'] - $psProduct['product_quantity']));
                        $this->handleProductStock($orderDetail, $quantity);
                    }
                    break;
                }
            }

            // If the product has not been found, it's a new one
            if (!$found) {
                $orderDetail = $this->createOrderDetail($sellermaniaProduct);
                $this->handleProductStock($orderDetail, -abs($orderDetail->product_quantity));
            }
        }

        // Use directly this part to avoid 'Frais de gestion' creation
        $this->fixCustomerAddress();
        if (version_compare(_PS_VERSION_, '1.5') >= 0) {
            $this->fixOrder15(true);
        }
        else {
            $this->fixOrder14(true);
        }
    }

    /**
     * @param $sellermaniaProduct
     * @return OrderDetail
     */
    private function createOrderDetail($sellermaniaProduct)
    {
        // Calcul price without tax
        $product_price_with_tax = $product['Amount']['Price'];
        $vat_rate = 1 + ($product['VatRate'] / 10000);
        $product_price_without_tax = $product_price_with_tax / $vat_rate;

        $orderDetail = new OrderDetail();
        $orderDetail->id_order = $this->order->id;
        $orderDetail->product_id = $sellermaniaProduct['id_product'];
        $orderDetail->product_attribute_id = $sellermaniaProduct['id_product_attribute'];
        $orderDetail->product_quantity = $sellermaniaProduct['QuantityPurchased'];
        $orderDetail->product_ean13 = $sellermaniaProduct['Ean'];
        $orderDetail->product_reference = $sellermaniaProduct['Sku'];
        $orderDetail->product_name = $sellermaniaProduct['ItemName'];
        $orderDetail->product_quantity_in_stock = 0;
        $orderDetail->product_price = $product_price_without_tax;
        $orderDetail->tax_rate = $sellermaniaProduct['VatRate'] / 100;
        $orderDetail->tax_name = ($sellermaniaProduct['VatRate'] / 100).'%';

        if (version_compare(_PS_VERSION_, '1.4', '>')) {
            $orderDetail->id_warehouse = 0;
            $orderDetail->id_shop = 1;
        }

        $orderDetail->add();

        return $orderDetail;
    }

    /**
     * @param OrderDetail $orderDetail
     * @param $quantity
     */
    private function handleProductStock(OrderDetail $orderDetail, $quantity)
    {
        if ($orderDetail->product_id == Configuration::get('SM_DEFAULT_PRODUCT_ID')) {
            return;
        }

        if (substr(_PS_VERSION_, 0, 3) == '1.4') {
            // Handle product quantities
            $productObj = new Product((int) $orderDetail->product_id, false, (int)_PS_LANG_DEFAULT_);
            $productObj->addStockMvt($quantity, _STOCK_MOVEMENT_ORDER_REASON_, $orderDetail->product_attribute_id, $orderDetail->id_order, NULL);
        } else {
            StockAvailable::updateQuantity($orderDetail->product_id, $orderDetail->product_attribute_id, $quantity);
        }
    }
}

