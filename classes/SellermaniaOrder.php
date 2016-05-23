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
*  @author Fabien Serny - Froggy Commerce <team@froggy-commerce.com>
*  @copyright	2010-2016 Sellermania / Froggy Commerce / 23Prod SARL
*  @version		1.0
*  @license		http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

// Security
if (!defined('_PS_VERSION_'))
	exit;

class SellermaniaOrder extends ObjectModel
{
	public $id;

	/** @var string Marketplace */
	public $marketplace;

	/** @var string Customer Name */
	public $customer_name;

	/** @var string Ref Order */
	public $ref_order;

	/** @var string Total Amount */
	public $amount_total;

	/** @var string Info */
	public $info;

	/** @var string Info */
	public $error;

	/** @var integer Order ID */
	public $id_order;

	/** @var integer Employee ID who accepted the order */
	public $id_employee_accepted;

	/** @var string Date Payment */
	public $date_payment;

	/** @var string Date Import */
	public $date_accepted;

	/** @var string Date Import */
	public $date_add;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'sellermania_order',
		'primary' => 'id_sellermania_order',
		'multilang' => false,
		'fields' => array(
			'marketplace' => 				array('type' => 3, 'required' => true, 'size' => 128),
			'customer_name' => 				array('type' => 3, 'required' => true, 'size' => 256),
			'ref_order' => 					array('type' => 3, 'required' => true, 'size' => 128),
			'amount_total' => 				array('type' => 3, 'required' => true, 'size' => 16),
			'info' => 						array('type' => 3, 'required' => true),
			'error' => 						array('type' => 3),
			'id_order' => 					array('type' => 1, 'validate' => 'isUnsignedId', 'required' => true),
			'id_employee_accepted' =>		array('type' => 1, 'validate' => 'isUnsignedId', 'required' => true),
			'date_payment' =>				array('type' => 3, 'validate' => 'isDate'),
			'date_accepted' =>				array('type' => 3, 'validate' => 'isDate'),
			'date_add' => 					array('type' => 5, 'validate' => 'isDate', 'copy_post' => false),
		),
	);
	/*	Can't use constant if we want to be compliant with PS 1.4
	 * 	const TYPE_INT = 1;
	 * 	const TYPE_BOOL = 2;
	 * 	const TYPE_STRING = 3;
	 * 	const TYPE_FLOAT = 4;
	 * 	const TYPE_DATE = 5;
	 * 	const TYPE_HTML = 6;
	 * 	const TYPE_NOTHING = 7;
	 */


	/**
	 * Check if order has already been imported
	 * @param $order
	 */
	public static function getSellermaniaOrderId($marketplace, $ref_order)
	{
		return (int)Db::getInstance()->getValue('
		SELECT `id_sellermania_order`
		FROM `'._DB_PREFIX_.'sellermania_order`
		WHERE `marketplace` = \''.pSQL(trim($marketplace)).'\'
		AND `ref_order` = \''.pSQL(trim($ref_order)).'\'');
	}

	/**
	 * Get Nb Sellermania orders in error
	 * @return int
	 */
	public static function getNbSellermaniaOrdersInError()
	{
		return (int)Db::getInstance()->getValue('
		SELECT COUNT(`id_sellermania_order`)
		FROM `'._DB_PREFIX_.'sellermania_order`
		WHERE `id_order` = 0
		AND `date_add` > \''.pSQL(date("Y-m-d H:i:s", strtotime('-15 days'))).'\'');
	}

	/**
	 * Get Sellermania orders in error
	 * @return array
	 */
	public static function getSellermaniaOrdersInError()
	{
		return Db::getInstance()->ExecuteS('
		SELECT * FROM `'._DB_PREFIX_.'sellermania_order`
		WHERE `id_order` = 0
		AND `date_add` > \''.pSQL(date("Y-m-d H:i:s", strtotime('-15 days'))).'\'');
	}

	/**
	 * Delete Sellermania order
	 * @return bool
	 */
	public static function deleteSellermaniaOrderInError($id_sellermania_order)
	{
		return Db::getInstance()->getValue('
		DELETE FROM `'._DB_PREFIX_.'sellermania_order`
		WHERE `id_order` = 0
		AND `id_sellermania_order` = '.(int)$id_sellermania_order);
	}
	public static function deleteAllSellermaniaOrdersInError()
	{
		return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'sellermania_order`
		WHERE `id_order` = 0');
	}

	/*** Retrocompatibility 1.4 ***/

	protected 	$fieldsRequired = array('marketplace', 'ref_order', 'id_order');
	protected 	$fieldsSize = array('marketplace' => 128, 'ref_order' => 128, 'id_order' => 32);
	protected 	$fieldsValidate = array('id_order' => 'isUnsignedInt');

	protected 	$table = 'sellermania_order';
	protected 	$identifier = 'id_sellermania_order';

	public	function getFields()
	{
		if (version_compare(_PS_VERSION_, '1.5') >= 0)
			return parent::getFields();

		parent::validateFields();

		$fields['marketplace'] = pSQL($this->marketplace);
		$fields['customer_name'] = pSQL($this->customer_name);
		$fields['ref_order'] = pSQL($this->ref_order);
		$fields['amount_total'] = pSQL($this->amount_total);
		$fields['info'] = pSQL($this->info);
		$fields['error'] = pSQL($this->error);
		$fields['id_order'] = (int)$this->id_order;
		$fields['id_employee_accepted'] = (int)$this->id_employee_accepted;
		$fields['date_payment'] = pSQL($this->date_payment);
		$fields['date_accepted'] = pSQL($this->date_accepted);
		$fields['date_add'] = pSQL($this->date_add);

		return $fields;
	}
}