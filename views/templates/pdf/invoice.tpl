{*
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
*  @copyright    2010-2016 Sellermania / Froggy Commerce / 23Prod SARL
*  @version      1.0
*  @license      http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<table style="width: 100%">
    <tr>
        <td style="width: 50%">
            {if $logo_path}
                <img src="{$logo_path}" style="width:100px;" />
            {/if}
        </td>
        <td style="width: 50%; text-align: right;">
            <table style="width: 100%">
                <tr>
                    <td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%">{$shop_name|escape:'html':'UTF-8'}</td>
                </tr>
                <tr>
                    <td style="font-size: 14pt; color: #9E9F9E">{$date|escape:'html':'UTF-8'}</td>
                </tr>
                <tr>
                    <td style="font-size: 14pt; color: #9E9F9E">{$title|escape:'html':'UTF-8'}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>


<div style="font-size: 8pt; color: #444">

    <table>
        <tr><td>&nbsp;</td></tr>
    </table>

    <!-- ADDRESSES -->
    <table style="width: 100%" border="0">
        <tr>
            <td style="width: 45%">
<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{$shop_name|escape:'html':'UTF-8'}</span><br />
{if isset($shop_contact.PS_SHOP_ADDR1) && !empty($shop_contact.PS_SHOP_ADDR1)}{$shop_contact.PS_SHOP_ADDR1|escape:'html':'UTF-8'}<br>{/if}
{if isset($shop_contact.PS_SHOP_ADDR2) && !empty($shop_contact.PS_SHOP_ADDR2)}{$shop_contact.PS_SHOP_ADDR2|escape:'html':'UTF-8'}<br>{/if}
{if isset($shop_contact.PS_SHOP_CODE) && !empty($shop_contact.PS_SHOP_CODE)}{$shop_contact.PS_SHOP_CODE|escape:'html':'UTF-8'} {$shop_contact.PS_SHOP_CITY|escape:'html':'UTF-8'}<br>{/if}
{if isset($shop_contact.PS_SHOP_COUNTRY_ID) && !empty($shop_contact.PS_SHOP_COUNTRY_ID)}{$shop_contact.PS_SHOP_COUNTRY->name|escape:'html':'UTF-8'}<br>{/if}
{if isset($shop_contact.PS_SHOP_EMAIL) && !empty($shop_contact.PS_SHOP_EMAIL)}{l s='E-mail:' pdf='true'} {$shop_contact.PS_SHOP_EMAIL|escape:'html':'UTF-8'}<br>{/if}
{if isset($shop_contact.PS_SHOP_PHONE) && !empty($shop_contact.PS_SHOP_PHONE)}{l s='Phone:' pdf='true'} {$shop_contact.PS_SHOP_PHONE|escape:'html':'UTF-8'}<br>{/if}
            </td>
            <td style="width: 10%">&nbsp;</td>
            <td style="width: 45%">
                <span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Delivery Address' pdf='true'}</span><br />
            </td>
        </tr>
    </table>
    <!-- / ADDRESSES -->

    <div style="line-height: 1pt">&nbsp;</div>
</div>
