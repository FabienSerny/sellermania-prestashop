<?php
/*
* 2010-2020 Sellermania / Froggy Commerce / 23Prod SARL
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
*  @copyright      2010-2020 Sellermania / Froggy Commerce / 23Prod SARL
*  @version        1.0
*  @license        http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

/**
 * Class Shop for Backward compatibility
 */
class ShopBackwardModuleUpdated extends Shop
{
    const CONTEXT_ALL = 1;

    public $id = 1;
    public $id_shop_group = 1;
    public $physical_uri = __PS_BASE_URI__;
    
    
    public function getContextType()
    {
        return ShopBackwardModule::CONTEXT_ALL;
    }

    public function setContext($var)
    {
        return true;
    }

    // Simulate shop for 1.3 / 1.4
    public function getID()
    {
        return 1;
    }
    
    /**
     * Get shop theme name
     *
     * @return string
     */
    public function getTheme()
    {
        return _THEME_NAME_;
    }

    public function isFeatureActive()
    {
        return false;
    }
}

