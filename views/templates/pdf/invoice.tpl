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
            </table>
        </td>
    </tr>
</table>


<div style="font-size: 8pt; color: #444">

    <!-- ADDRESSES -->
    <table style="width: 100%" border="0">
        <tr>
            <td style="width: 45%">
<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{$shop_name|escape:'html':'UTF-8'}</span><br />
{if isset($shop_contact.PS_SHOP_ADDR1) && !empty($shop_contact.PS_SHOP_ADDR1)}{$shop_contact.PS_SHOP_ADDR1|escape:'html':'UTF-8'}<br>{/if}
{if isset($shop_contact.PS_SHOP_ADDR2) && !empty($shop_contact.PS_SHOP_ADDR2)}{$shop_contact.PS_SHOP_ADDR2|escape:'html':'UTF-8'}<br>{/if}
{if isset($shop_contact.PS_SHOP_CODE) && !empty($shop_contact.PS_SHOP_CODE)}{$shop_contact.PS_SHOP_CODE|escape:'html':'UTF-8'} {$shop_contact.PS_SHOP_CITY|escape:'html':'UTF-8'}<br>{/if}
{if isset($shop_contact.PS_SHOP_COUNTRY_ID) && !empty($shop_contact.PS_SHOP_COUNTRY_ID)}{$shop_contact.PS_SHOP_COUNTRY->name|escape:'html':'UTF-8'}<br>{/if}
{if isset($shop_contact.PS_SHOP_EMAIL) && !empty($shop_contact.PS_SHOP_EMAIL)}{l s='E-mail:' mod='sellermania'} {$shop_contact.PS_SHOP_EMAIL|escape:'html':'UTF-8'}<br>{/if}
{if isset($shop_contact.PS_SHOP_PHONE) && !empty($shop_contact.PS_SHOP_PHONE)}{l s='Phone:' mod='sellermania'} {$shop_contact.PS_SHOP_PHONE|escape:'html':'UTF-8'}<br>{/if}
            </td>
            <td style="width: 10%">&nbsp;</td>
            <td style="width: 45%">
{assign var="address_index" value="0"}
<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Delivery Address' mod='sellermania'}</span><br />
{if isset($sellermania_order->details.User[$address_index].Name) && !empty($sellermania_order->details.User[$address_index].Name)}{$sellermania_order->details.User[$address_index].Name|escape:'html':'UTF-8'}<br>{/if}
{if isset($sellermania_order->details.User[$address_index].Address.Street1) && !empty($sellermania_order->details.User[$address_index].Address.Street1)}{$sellermania_order->details.User[$address_index].Address.Street1|escape:'html':'UTF-8'}<br>{/if}
{if isset($sellermania_order->details.User[$address_index].Address.Street2) && !empty($sellermania_order->details.User[$address_index].Address.Street2)}{$sellermania_order->details.User[$address_index].Address.Street2|escape:'html':'UTF-8'}<br>{/if}
{if isset($sellermania_order->details.User[$address_index].Address.ZipCode) && !empty($sellermania_order->details.User[$address_index].Address.ZipCode)}{$sellermania_order->details.User[$address_index].Address.ZipCode|escape:'html':'UTF-8'} {$sellermania_order->details.User[$address_index].Address.City|escape:'html':'UTF-8'}<br>{/if}
{if isset($sellermania_order->details.User[$address_index].Address.Country) && !empty($sellermania_order->details.User[$address_index].Address.Country)}{$sellermania_order->details.User[$address_index].Address.Country|escape:'html':'UTF-8'}<br>{/if}
{if isset($sellermania_order->details.User[$address_index].ShippingPhone) && !empty($sellermania_order->details.User[$address_index].ShippingPhone)}{$sellermania_order->details.User[$address_index].ShippingPhone|escape:'html':'UTF-8'}<br>{/if}
{if isset($sellermania_order->details.User[$address_index].UserPhone) && !empty($sellermania_order->details.User[$address_index].UserPhone)}{$sellermania_order->details.User[$address_index].UserPhone|escape:'html':'UTF-8'}<br>{/if}
            </td>
        </tr>
    </table>
    <!-- / ADDRESSES -->

<h2>{$title|escape:'html':'UTF-8'}</h2>

    <!-- DETAILS -->
    <table style="width: 100%" border="0" style="border:1px solid black; padding:5px">
        <tr>
            <td style="width: 45%" align="left">
<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Order details:' mod='sellermania'}</span><br>
{l s='Order date:' mod='sellermania'} {$sellermania_order->details.OrderInfo.Date}<br>
{l s='Order reference:' mod='sellermania'} {$sellermania_order->ref_order}<br>
{l s='Order placed on:' mod='sellermania'} {$sellermania_order->marketplace}<br>
{l s='Delivery method:' mod='sellermania'} {$sellermania_order->details.OrderInfo.Transport.ShippingType}<br>
{l s='Order date:' mod='sellermania'} {$date|escape:'html':'UTF-8'}
            </td>
            <td style="width: 10%">&nbsp;</td>
            <td style="width: 45%" align="right">
{if isset($sellermania_order->details.User[1].Name) && !empty($sellermania_order->details.User[1].Name)}{assign var="address_index" value="1"}{/if}
<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Invoice Address' mod='sellermania'}</span><br />
{if isset($sellermania_order->details.User[$address_index].Name) && !empty($sellermania_order->details.User[$address_index].Name)}{$sellermania_order->details.User[$address_index].Name|escape:'html':'UTF-8'}<br>{/if}
{if isset($sellermania_order->details.User[$address_index].Address.Street1) && !empty($sellermania_order->details.User[$address_index].Address.Street1)}{$sellermania_order->details.User[$address_index].Address.Street1|escape:'html':'UTF-8'}<br>{/if}
{if isset($sellermania_order->details.User[$address_index].Address.Street2) && !empty($sellermania_order->details.User[$address_index].Address.Street2)}{$sellermania_order->details.User[$address_index].Address.Street2|escape:'html':'UTF-8'}<br>{/if}
{if isset($sellermania_order->details.User[$address_index].Address.ZipCode) && !empty($sellermania_order->details.User[$address_index].Address.ZipCode)}{$sellermania_order->details.User[$address_index].Address.ZipCode|escape:'html':'UTF-8'} {$sellermania_order->details.User[$address_index].Address.City|escape:'html':'UTF-8'}<br>{/if}
{if isset($sellermania_order->details.User[$address_index].Address.Country) && !empty($sellermania_order->details.User[$address_index].Address.Country)}{$sellermania_order->details.User[$address_index].Address.Country|escape:'html':'UTF-8'}<br>{/if}
{if isset($sellermania_order->details.User[$address_index].ShippingPhone) && !empty($sellermania_order->details.User[$address_index].ShippingPhone)}{$sellermania_order->details.User[$address_index].ShippingPhone|escape:'html':'UTF-8'}<br>{/if}
{if isset($sellermania_order->details.User[$address_index].UserPhone) && !empty($sellermania_order->details.User[$address_index].UserPhone)}{$sellermania_order->details.User[$address_index].UserPhone|escape:'html':'UTF-8'}<br>{/if}
            </td>
        </tr>
    </table>
    <!-- /DETAILS -->

    <div style="line-height: 1pt">&nbsp;</div>

    <!-- PRODUCTS -->
    {assign var="currency_sign" value=$sellermania_order->details.OrderInfo.Amount.CurrencySign}
    <table style="width: 100%" border="1" style="border:1px solid black; padding:5px">
        <tr align="center">
            <td style="width:10%">{l s='Quantity' mod='sellermania'}</td>
            <td style="width:55%">{l s='Product' mod='sellermania'}</td>
            <td style="width:15%">{l s='VAT' mod='sellermania'}</td>
            <td style="width:20%">{l s='Total' mod='sellermania'}</td>
        </tr>
        {foreach from=$sellermania_order->details.OrderInfo.Product item=product}
            <tr>
                <td align="center">{$product.QuantityPurchased}</td>
                <td align="left">
{$product.ItemName}<br><br>
{if isset($product.OrderItemId) && !empty($product.OrderItemId)}{l s='Order item ID:' mod='sellermania'} {$product.OrderItemId}<br>{/if}
{if isset($product.ItemCondition) && !empty($product.ItemCondition)}{l s='State:' mod='sellermania'} {if isset($product.ItemCondition) && isset($sellermania_conditions_list[$product.ItemCondition])}{$sellermania_conditions_list[$product.ItemCondition]|addslashes}{/if}<br>{/if}
{if isset($product.Ean) && !empty($product.Ean)}EAN13: {$product.Ean}<br>{/if}
{if isset($product.Sku) && !empty($product.Sku)}SKU: {$product.Sku}<br>{/if}
{if isset($product.Upc) && !empty($product.Upc)}UPC: {$product.Upc}<br>{/if}
                </td>
                <td align="center">{$product.ProductVAT.VATPercent}%</td>
                <td align="center">{$product.Amount.PriceWithoutVAT|round:2} {$currency_sign} {l s='without VAT' mod='sellermania'}</td>
            </tr>
            {if isset($product.ShippingFee.Amount.Price)}
                <tr>
                    <td align="center">{$product.QuantityPurchased}</td>
                    <td align="left">{l s='Packing fees and delivery' mod='sellermania'}</td>
                    <td align="center">{$product.ShippingFee.Amount.VATPercent}%</td>
                    <td align="center">{$product.ShippingFee.Amount.PriceWithoutVAT|round:2} {$currency_sign} {l s='without VAT' mod='sellermania'}</td>
                </tr>
            {/if}
        {/foreach}


        <tr>
            <td colspan="2" align="right">{l s='Total without VAT' mod='sellermania'}</td>
            <td align="center">&nbsp;</td>
            <td align="center">{($sellermania_order->details.OrderInfo.TotalProductsWithoutVAT + $sellermania_order->details.OrderInfo.Transport.Amount.PriceWithoutVAT)|round:2} {$currency_sign}</td>
        </tr>
        {foreach from=$sellermania_order->details.OrderInfo.SubtotalVAT key=vat_percent item=vat_value}
            <tr>
                <td colspan="2" align="right">{l s='VAT' mod='sellermania'}</td>
                <td align="center">{$vat_percent}%</td>
                <td align="center">{$vat_value|round:2} {$currency_sign}</td>
            </tr>
        {/foreach}
        {if $sellermania_order->details.OrderInfo.TotalInsurance gt 0}
        <tr>
            <td colspan="2" align="right">{l s='Total insurance' mod='sellermania'}</td>
            <td align="center">&nbsp;</td>
            <td align="center">{$sellermania_order->details.OrderInfo.TotalInsurance} {$currency_sign}</td>
        </tr>
        {/if}
        {if $sellermania_order->details.OrderInfo.OptionalFeaturePrice gt 0}
        <tr>
            <td colspan="2" align="right">{l s='Gestion fees' mod='sellermania'}</td>
            <td align="center">&nbsp;</td>
            <td align="center">{$sellermania_order->details.OrderInfo.OptionalFeaturePrice} {$currency_sign}</td>
        </tr>
        {/if}
        <tr>
            <td colspan="2" align="right">{l s='Total with VAT' mod='sellermania'}</td>
            <td align="center">&nbsp;</td>
            <td align="center">{$sellermania_order->details.OrderInfo.TotalAmount.Amount.Price} {$currency_sign}</td>
        </tr>
    </table>

    <p align="center"><b>Merci de votre commande</b>: N'oubliez pas de nous laisser une évaluation sur {$sellermania_order->marketplace}</p>
    <p align="center"><b>AVANT TOUTE EVALUATION</b>, si votre commande présente une anomalie, notre SAV mettra tout en oeuvre pour vous satisfaire.</p>

</div>


<table>
    <tr>
        <td style="text-align: center; font-size: 6pt; color: #444">

{$shop_name|escape:'html':'UTF-8'},
{if isset($shop_contact.PS_SHOP_ADDR1) && !empty($shop_contact.PS_SHOP_ADDR1)}{$shop_contact.PS_SHOP_ADDR1|escape:'html':'UTF-8'} {/if}
{if isset($shop_contact.PS_SHOP_ADDR2) && !empty($shop_contact.PS_SHOP_ADDR2)}{$shop_contact.PS_SHOP_ADDR2|escape:'html':'UTF-8'} {/if}
{if isset($shop_contact.PS_SHOP_CODE) && !empty($shop_contact.PS_SHOP_CODE)}{$shop_contact.PS_SHOP_CODE|escape:'html':'UTF-8'} {$shop_contact.PS_SHOP_CITY|escape:'html':'UTF-8'} {/if}
{if isset($shop_contact.PS_SHOP_COUNTRY_ID) && !empty($shop_contact.PS_SHOP_COUNTRY_ID)}{$shop_contact.PS_SHOP_COUNTRY->name|escape:'html':'UTF-8'} {/if}
<br>
{if isset($shop_contact.PS_SHOP_EMAIL) && !empty($shop_contact.PS_SHOP_EMAIL)}{l s='E-mail:' mod='sellermania'} {$shop_contact.PS_SHOP_EMAIL|escape:'html':'UTF-8'} {/if}
{if isset($shop_contact.PS_SHOP_PHONE) && !empty($shop_contact.PS_SHOP_PHONE)}{l s='Phone:' mod='sellermania'} {$shop_contact.PS_SHOP_PHONE|escape:'html':'UTF-8'} {/if}
<br>
{if isset($shop_contact.PS_SHOP_DETAILS)}{$shop_contact.PS_SHOP_DETAILS|escape:'html':'UTF-8'}{/if}
        </td>
    </tr>
</table>
