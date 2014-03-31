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

// Include all class needed
require_once(dirname(__FILE__).'/init.php');

class SellerMania extends Module
{
	public $sellermania_order_states;

	/**
	 * Module Constructor
	 */
	function __construct()
	{
		$this->name = 'sellermania';
		$this->tab = 'advertising_marketing';
		$this->author = '23Prod';
		$this->version = '1.0.3';
		$this->need_instance = 0;

		parent::__construct();

		if (version_compare(_PS_VERSION_, '1.5') < 0)
			require(dirname(__FILE__).'/backward/backward.php');

		$this->displayName = $this->l('SellerMania');
		$this->description = $this->l('Connect your PrestaShop with SellerMania webservices');

		$this->loadSellermaniaOrderStates();
		$this->upgrade();
	}


	/**
	 * Load Sellermania order states
	 */
	public function loadSellermaniaOrderStates()
	{
		$this->sellermania_order_states = array(
			'PS_OS_SM_ERR_CONF' => array('sm_status' => 11, 'sm_prior' => 1, 'label' => array('en' => 'Error confirmation', 'fr' => 'En erreur de confirmation'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#fa4b4c'),
			'PS_OS_SM_ERR_CANCEL_CUS' => array('sm_status' => 12, 'sm_prior' => 1, 'label' => array('en' => 'Error cancel by customer', 'fr' => 'En erreur, annulée par client'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#fa4b4c'),
			'PS_OS_SM_ERR_CANCEL_SEL' => array('sm_status' => 13, 'sm_prior' => 1, 'label' => array('en' => 'Error cancel by seller', 'fr' => 'En erreur, annulée par vendeur'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#fa4b4c'),

			'PS_OS_SM_AWAITING' => array('sm_status' => 6, 'sm_prior' => 1, 'label' => array('en' => 'To be confirmed', 'fr' => 'A confirmer'), 'logable' => false, 'invoice' => false, 'shipped' => false, 'paid' => false, 'color' => '#98c3ff'),
			'PS_OS_SM_CONFIRMED' => array('sm_status' => 9, 'sm_prior' => 0, 'label' => array('en' => 'Waiting for payment', 'fr' => 'En attente de paiement'), 'logable' => true, 'invoice' => false, 'shipped' => false, 'paid' => false, 'color' => '#98c3ff'),
			'PS_OS_SM_TO_DISPATCH' => array('sm_status' => 1, 'sm_prior' => 1, 'label' => array('en' => 'To dispatch', 'fr' => 'A expédier'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => true, 'color' => '#98c3ff'),
			'PS_OS_SM_DISPATCHED' => array('sm_status' => 2, 'sm_prior' => 0, 'label' => array('en' => 'Dispatched', 'fr' => 'Expédiée'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => true, 'color' => '#98c3ff'),

			'PS_OS_SM_CANCEL_CUS' => array('sm_status' => 3, 'sm_prior' => 0, 'label' => array('en' => 'Cancel by customer', 'fr' => 'Annulée par client'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#98c3ff'),
			'PS_OS_SM_CANCEL_SEL' => array('sm_status' => 4, 'sm_prior' => 0, 'label' => array('en' => 'Cancel by seller', 'fr' => 'Annulée par vendeur'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#98c3ff'),
		);
	}

	/**
	 *  Module upgrade
	 */
	public function upgrade()
	{
		$version_registered = Configuration::get('SM_VERSION');
		if ($version_registered == '' || version_compare($version_registered, '1.0.0', '<'))
		{
			if ((int)Configuration::get('PS_OS_SM_SEND') > 0)
			{
				// Change configuration name
				Configuration::updateValue('PS_OS_SM_TO_DISPATCH', Configuration::get('PS_OS_SM_SEND'));
				Configuration::updateValue('PS_OS_SM_DISPATCHED', Configuration::get('PS_OS_SM_SENT'));

				// Delete old ones
				Configuration::deleteByName('PS_OS_SM_SEND');
				Configuration::deleteByName('PS_OS_SM_SENT');
			}

			// Update order states
			$this->installOrderStates();

			// Set module version
			Configuration::updateValue('SM_VERSION', $this->version);
		}
	}

	/**
	 * Install method
	 * @return boolean success
	 */
	public function install()
	{
		// Execute module install MySQL commands
		$sql_file = dirname(__FILE__).'/install/install.sql';
		if (!$this->loadSQLFile($sql_file))
			return false;

		// Register hooks
		if (version_compare(_PS_VERSION_, '1.5') >= 0)
		{
			if (!parent::install() || !$this->registerHook('displayAdminOrder') || !$this->registerHook('displayBackOfficeHeader'))
				return false;
		}
		else
		{
			if (!parent::install() || !$this->registerHook('adminOrder') || !$this->registerHook('backOfficeHeader'))
				return false;
		}

		// Install Order States
		$this->installOrderStates();

		// Install Product
		$this->installSellermaniaProduct();

		// Gen SellerMania key
		Configuration::updateValue('SM_VERSION', $this->version);
		Configuration::updateValue('SM_INSTALL_DATE', date('Y-m-d H:i:s'));
		Configuration::updateValue('SELLERMANIA_KEY', md5(rand()._COOKIE_KEY_.date('YmdHis')));

		return true;
	}


	/**
	 * Uninstall method
	 * @return boolean success
	 */
	public function uninstall()
	{
		// Execute module install MySQL commands
		// $sql_file = dirname(__FILE__).'/install/uninstall.sql';
		// if (!$this->loadSQLFile($sql_file))
		//	return false;

		// Delete configuration values
		Configuration::deleteByName('SM_IMPORT_ORDERS');
		Configuration::deleteByName('SM_ORDER_EMAIL');
		Configuration::deleteByName('SM_ORDER_TOKEN');
		Configuration::deleteByName('SM_ORDER_ENDPOINT');
		Configuration::deleteByName('SM_CONFIRM_ORDER_ENDPOINT');
		Configuration::deleteByName('SM_NEXT_IMPORT');
		Configuration::deleteByName('SM_CREDENTIALS_CHECK');
		Configuration::deleteByName('SM_INSTALL_DATE');
		Configuration::deleteByName('SELLERMANIA_KEY');

		return parent::uninstall();
	}


	/**
	 * Load SQL file
	 * @return boolean success
	 */
	public function loadSQLFile($sql_file)
	{
		// Get install MySQL file content
		$sql_content = file_get_contents($sql_file);

		// Replace prefix and store MySQL command in array
		$sql_content = str_replace('PREFIX_', _DB_PREFIX_, $sql_content);
		$sql_requests = preg_split("/;\s*[\r\n]+/", $sql_content);

		// Execute each MySQL command
		$result = true;
		foreach($sql_requests AS $request)
			if (!empty($request))
				$result &= Db::getInstance()->execute(trim($request));

		// Return result
		return $result;
	}

	/**
	 * Install Sellermania Order States
	 */
	public function installOrderStates()
	{
		$languages = array(
			(int)Configuration::get('PS_LANG_DEFAULT') => 'en',
			(int)Language::getIdByIso('fr') => 'fr',
			(int)Language::getIdByIso('en') => 'en',
		);

		foreach ($this->sellermania_order_states as $order_state_key => $order_state_array)
		{
			if (Configuration::get($order_state_key) < 1)
			{
				$order_state = new OrderState();
				$order_state->send_email = false;
				$order_state->module_name = $this->name;
				$order_state->invoice = $order_state_array['invoice'];
				$order_state->color = $order_state_array['color'];
				$order_state->logable = $order_state_array['logable'];
				$order_state->shipped = $order_state_array['shipped'];
				$order_state->unremovable = false;
				$order_state->delivery = $order_state_array['shipped'];
				$order_state->hidden = false;
				$order_state->paid = $order_state_array['invoice'];
				$order_state->deleted = false;

				$order_state->name = array();
				foreach ($languages as $key_lang => $iso_lang)
					if ($key_lang > 0)
						$order_state->name[$key_lang] = pSQL('Marketplace - '.$order_state_array['label'][$iso_lang]);

				if ($order_state->add())
				{
					Configuration::updateValue($order_state_key, $order_state->id);
					copy(dirname(__FILE__).'/logo.gif', dirname(__FILE__).'/../../img/os/'.$order_state->id.'.gif');
					copy(dirname(__FILE__).'/logo.gif', dirname(__FILE__).'/../../img/tmp/order_state_mini_'.$order_state->id.'.gif');
				}
			}
			else
			{
				$order_state = new OrderState((int)Configuration::get($order_state_key));

				$order_state->color = $order_state_array['color'];
				$order_state->name = array();
				foreach ($languages as $key_lang => $iso_lang)
					if ($key_lang > 0)
						$order_state->name[$key_lang] = pSQL('Marketplace - '.$order_state_array['label'][$iso_lang]);

				$order_state->update();
			}
		}
	}


	/**
	 * Install Sellermania Product (in case a product is not recognized)
	 */
	public function installSellermaniaProduct()
	{
		if (Configuration::get('SM_DEFAULT_PRODUCT_ID') > 0)
			return true;

		$label = $this->l('Sellermania product');

		$product = new Product();
		$product->name = array((int)Configuration::get('PS_LANG_DEFAULT') => pSQL($label));
		$product->link_rewrite = array((int)Configuration::get('PS_LANG_DEFAULT') => 'sellermania-product');
		$product->id_tax_rules_group = 0;
		$product->id_supplier = 0;
		$product->id_manufacturer = 0;
		$product->id_category_default = 0;
		$product->quantity = 0;
		$product->minimal_quantity = 1;
		$product->price = 1;
		$product->wholesale_price = 0;
		$product->out_of_stock = 1;
		$product->available_for_order = 1;
		$product->show_price = 1;
		$product->date_add = pSQL(date('Y-m-d H:i:s'));
		$product->date_upd = pSQL(date('Y-m-d H:i:s'));
		$product->active = 1;
		$product->add();

		if (version_compare(_PS_VERSION_, '1.5') >= 0)
			StockAvailable::setProductOutOfStock((int)$product->id, 1);

		// Saving product ID
		Configuration::updateValue('SM_DEFAULT_PRODUCT_ID', (int)$product->id);

		return true;
	}


	/**
	 * Compliant display between 1.4 and 1.5
	 * @param string $template
	 * @return string $html
	 */
	public function compliantDisplay($template)
	{
		if (version_compare(_PS_VERSION_, '1.5') < 0)
			return $this->display(__FILE__, 'views/templates/hook/'.$template);
		else
			return $this->display(__FILE__, $template);
	}


	/**
	 * @param string $hook_name
	 * @return mixed $result
	 */
	public function runController($controller_type, $controller_name)
	{
		// Include the controller file
		require_once(dirname(__FILE__).'/controllers/'.$controller_type.'/SellerMania'.$controller_name.'.php');
		$controller_name = 'SellerMania'.$controller_name.'Controller';
		$controller = new $controller_name($this, dirname(__FILE__), $this->_path);
		return $controller->run();
	}


	/**
	 * Configuration method
	 * @return string $html
	 */
	function getContent()
	{
		if (Tools::getValue('export') == 'true')
			die($this->export());
		return $this->runController('hook', 'GetContent');
	}


	/**
	 * Display BackOffice Header Hook
	 * @return string $html
	 */
	public function hookDisplayBackOfficeHeader($params)
	{
		if (version_compare(PHP_VERSION, '5.3.0') >= 0)
			return $this->runController('hook', 'DisplayBackOfficeHeader');
		return '';
	}
	public function hookBackOfficeHeader($params)
	{
		return $this->hookDisplayBackOfficeHeader($params);
	}

	/**
	 * Display Admin Order
	 * @return string $html
	*/
	public function hookDisplayAdminOrder($params)
	{
		if (version_compare(PHP_VERSION, '5.3.0') >= 0)
			return $this->runController('hook', 'DisplayAdminOrder');
		return '';
	}
	public function hookAdminOrder($params)
	{
		return $this->hookDisplayAdminOrder($params);
	}

	/**
	 * Export method
	 * @return string $export
	 */
	public function export()
	{
		return $this->runController('front', 'Export');
	}


	/**
	 * Log data
	 * @param $string
	 */
	public function log($string)
	{
		file_put_contents(dirname(__FILE__).'/log/log.txt', $string."\n", FILE_APPEND);
	}
}

