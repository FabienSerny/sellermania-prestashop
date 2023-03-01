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

class SellermaniaMarketplace extends ObjectModel
{
    public $id;

    public $code;

    public $enabled;

    public $mode;

    public $available;

    public static $definition = [
        'table' => 'sellermania_marketplace',
        'primary' => 'id_sellermania_marketplace',
        'multilang' => false,
        'fields' => [
            'code' => ['type' => 3, 'required' => true, 'size' => 128],
            'enabled' => ['type' => 2, 'required' => true],
            'available' => ['type' => 2, 'required' => true],
        ]
    ];

    public static function getAvailableSellermaniaMarketplaces()
    {
        $result = Db::getInstance()->executeS('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'sellermania_marketplace`
            WHERE `available` = 1
            ORDER BY `code` ASC'
        );

        return $result;
    }

    public static function getAllSellermaniaMarketplaces()
    {
        $result = Db::getInstance()->executeS('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'sellermania_marketplace`
            ORDER BY `code` ASC'
        );

        return $result;
    }

    public static function getMarketplaceByCode ($marketplace_code)
    {
        $result = Db::getInstance()->executeS('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'sellermania_marketplace`
            WHERE `code` LIKE "'.$marketplace_code.'"'
        );

        return $result;
    }

    public static function resetMarketplaceAvailability ()
    {
        Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'sellermania_marketplace`
            SET `available` = 0'
        );
    }

    public static function setMarketplaceAvailabilityByCode ($code, $availability, $enabled = null)
    {
        $sql = '
            UPDATE `' . _DB_PREFIX_ . 'sellermania_marketplace`
            SET `available` = '.$availability
        ;
        if ($enabled !== null) {
            $sql .= ', `enabled` = '.$enabled;
        }
        $sql .= ' WHERE `code` LIKE "'.$code.'"';
        Db::getInstance()->execute($sql);
    }

    public static function createMarketplace ($code, $availability, $enabled) {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'sellermania_marketplace` VALUES (
            NULL, 
            "'.$code.'", 
            "'.$enabled.'",
            "'.$availability.'"
        )';

        Db::getInstance()->execute($sql);
    }
}