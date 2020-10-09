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

class SellermaniaTestAPI
{
    public    function run()
    {
        // Creating an instance of OrderClient
        $client = new Sellermania\OrderClient();
        $client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
        $client->setToken(Configuration::get('SM_ORDER_TOKEN'));
        $client->setEndpoint(Configuration::get('SM_ORDER_ENDPOINT'));

        // Recovering dispatched orders for the last 30 days
        $client->getOrderByStatus(
            Sellermania\OrderClient::STATUS_TO_BE_CONFIRMED,
            Sellermania\OrderClient::MKP_AMAZON_FR,
            new \DateTime(date('Y-m-d')),
            new \DateTime(date('Y-m-d'))
        );
    }
}
