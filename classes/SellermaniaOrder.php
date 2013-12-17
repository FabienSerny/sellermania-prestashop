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

class SellermaniaOrder extends ObjectModel
{
	public $id;

	/** @var string Marketplace */
	public $marketplace;

	/** @var string Ref Order */
	public $ref_order;

	/** @var string Info */
	public $info;

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
			'marketplace' => 				array('type' => self::TYPE_STRING, 'required' => true, 'size' => 20),
			'ref_order' => 					array('type' => self::TYPE_STRING, 'required' => true, 'size' => 20),
			'info' => 					array('type' => self::TYPE_STRING, 'required' => true, 'size' => 20),
			'id_order' => 					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_employee_accepted' =>		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'date_payment' =>				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_accepted' =>				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_add' => 					array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
		),
	);


	/*** Retrocompatibility 1.4 ***/

	protected 	$fieldsRequired = array('marketplace', 'ref_order', 'id_order');
	protected 	$fieldsSize = array('marketplace' => 128, 'ref_order' => 128, 'id_order' => 32);
	protected 	$fieldsValidate = array('id_order' => 'isUnsignedInt');

	protected 	$table = 'sellermania_order';
	protected 	$identifier = 'id_sellermania_order';

	public	function getFields()
	{
		parent::validateFields();

		$fields['marketplace'] = pSQL($this->marketplace);
		$fields['ref_order'] = pSQL($this->ref_order);
		$fields['info'] = pSQL($this->info);
		$fields['id_order'] = (int)$this->id_order;
		$fields['id_employee_accepted'] = (int)$this->id_employee_accepted;
		$fields['date_payment'] = pSQL($this->date_payment);
		$fields['date_accepted'] = pSQL($this->date_accepted);

		return $fields;
	}
}