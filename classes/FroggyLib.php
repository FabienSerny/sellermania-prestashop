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

/*
 * Security
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class FroggyLib
{
    public static function getAdminLink($tab)
    {
        // In 1.5, we use getAdminLink method
        if (version_compare(_PS_VERSION_, '1.5.0') >= 0) {
            if (version_compare(_PS_VERSION_, '1.6.0') >= 0 && $tab == 'AdminHome') {
                $tab = 'AdminDashboard';
            }
            return Context::getContext()->link->getAdminLink($tab);
        }

        // Match compatibility between 1.4 and 1.5
        $match = array(
            'AdminProducts' => 'AdminCatalog',
            'AdminCategories' => 'AdminCatalog',
            'AdminCmsContent' => 'AdminCMSContent',
        );
        if (isset($match[$tab])) {
            $tab = $match[$tab];
        }

        // In 1.4, we build it with cookie for back office or with argument for front office (see froggytoolbar)
        $id_employee = Context::getContext()->employee->id;
        $token = Tools::getAdminToken($tab.(int)Tab::getIdFromClassName($tab).(int)$id_employee);

        // Return link
        return 'index.php?tab='.$tab.'&token='.$token;
    }
}
