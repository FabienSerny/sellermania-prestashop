<?php
/*
* 2010-2022 Sellermania / Froggy Commerce / 23Prod SARL
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade your module to newer
* versions in the future.
*
*  @author         Hedi Akrout from Sellermania <hakrout@sellermania.com>
*  @copyright      2010-2022 Sellermania / Froggy Commerce / 23Prod SARL
*  @version        1.0
*  @license        http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

/*
 * Security
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class SellermaniaFieldError extends ObjectModel
{
    public $id;

    public $field_name;

    public $error_message;

    public $is_active;


    public static $definition = [
        'table' => 'sellermania_field_error',
        'primary' => 'id_sellermania_field_error',
        'multilang' => false,
        'fields' => [
            'field_name' => ['type' => 3, 'required' => true, 'size' => 255],
            'error_message' => ['type' => 3, 'required' => false],
            'is_active' => ['type' => 2, 'required' => true],
        ]
    ];

    public static function getAllFieldErrors($section = null)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . self::$definition['table'].'`';
        if ($section) {
            $sql .= ' WHERE `section` LIKE "'.$section.'"';
        }
        $sql .= ' ORDER BY `date_add` DESC';

        return Db::getInstance()->executeS($sql);
    }

    public static function getAllActiveFieldErrors($section = null)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . self::$definition['table'].'` WHERE `is_active` = 1';
        if ($section) {
            $sql .= ' AND `section` LIKE "'.$section.'"';
        }
        $sql .= ' ORDER BY `date_add` DESC';

        return Db::getInstance()->executeS($sql);
    }

    public static function resetValidations ()
    {
        Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'sellermania_field_error`
            SET `is_active` = 0'
        );
    }

    public static function createFieldError ($field_name, $error_message, $section) {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . self::$definition['table'] . '` VALUES (
            NULL, 
            "'.$field_name.'", 
            "'.$error_message.'",
            "'.$section.'",
            "1",
            "'.date('Y-m-d H:i:s').'"
        )';

        Db::getInstance()->execute($sql);
    }

    public static function disableErrorByFieldName ($field_name) {
        Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . self::$definition['table']. '`
            SET `is_active` = 0
            WHERE `field_name` LIKE "'.$field_name.'"'
        );
    }
}