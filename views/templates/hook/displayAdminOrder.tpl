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
*  @author         Froggy Commerce <team@froggy-commerce.com>
*  @copyright      2010-2016 Sellermania / Froggy Commerce / 23Prod SARL
*  @version        1.0
*  @license        http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div id="sellermania-template">


    {************************************************}
    {*************** TITLE TEMPLATE *****************}
    {************************************************}
    <div id="sellermania-template-title">
        <h2>{l s='Sellermania order from the marketplace' mod='sellermania'} {$sellermania_order.OrderInfo.MarketPlace}</h2>
        <a target="_blank" href="{$sellermania_invoice_url}&id_order={$smarty.get.id_order}" class="btn btn-default">{if $ps_version eq 16}<i class="icon-file"></i>{else}<img src="../img/admin/details.gif">{/if} {l s='Display invoice' mod='sellermania'}</a>
    </div>


    {***************************************************}
    {*************** CUSTOMER TEMPLATE *****************}
    {***************************************************}
    <div id="sellermania-template-customer">
        <legend><img src="../img/admin/tab-customers.gif" /> {l s='Customer information' mod='sellermania'}</legend>
        <b>{l s='Name:' mod='sellermania'}</b> {$sellermania_order.User[0].OriginalName}<br>
        <b>{l s='E-mail:' mod='sellermania'}</b> <a href="mailto:{$sellermania_order.User[0].Email}">{$sellermania_order.User[0].Email}</a><br>
        {if isset($sellermania_order.User[0].ShippingPhone) && !empty($sellermania_order.User[0].ShippingPhone)}<b>{l s='Shipping phone:' mod='sellermania'}</b> {$sellermania_order.User[0].ShippingPhone}<br>{/if}
        {if isset($sellermania_order.User[0].UserPhone) && !empty($sellermania_order.User[0].UserPhone)}<b>{l s='User phone:' mod='sellermania'}</b> {$sellermania_order.User[0].UserPhone}<br>{/if}
        <br>
        <table width="100%">
            <tr>
                <td width="50%" align="left"><b>{l s='Shipping address:' mod='sellermania'}</b></td>
                <td width="50%" align="right"><b>{l s='Billing address:' mod='sellermania'}</b></td>
            </tr>
            <tr>
                <td width="50%" align="left">
                    {$sellermania_order.User[0].OriginalName}<br>
                    {if isset($sellermania_order.User[0].Company) && !empty($sellermania_order.User[0].Company)}{$sellermania_order.User[0].Company}<br>{/if}

                    {if isset($sellermania_order.User[0].Address.Street1) && !empty($sellermania_order.User[0].Address.Street1) && $sellermania_order.User[0].Address.Street1 ne 'Not provided'}{$sellermania_order.User[0].Address.Street1}<br>{/if}
                    {if isset($sellermania_order.User[0].Address.Street2) && !empty($sellermania_order.User[0].Address.Street2) && $sellermania_order.User[0].Address.Street2 ne 'Not provided'}{$sellermania_order.User[0].Address.Street2}<br>{/if}

                    {if isset($sellermania_order.User[0].Address.ZipCode) && !empty($sellermania_order.User[0].Address.ZipCode) && $sellermania_order.User[0].Address.ZipCode ne '00000'}{$sellermania_order.User[0].Address.ZipCode} {/if}
                    {if isset($sellermania_order.User[0].Address.City) && !empty($sellermania_order.User[0].Address.City) && $sellermania_order.User[0].Address.City ne 'Not provided'}{$sellermania_order.User[0].Address.City}<br>{/if}

                    {if isset($sellermania_order.User[0].Address.State) && !empty($sellermania_order.User[0].Address.State)}{$sellermania_order.User[0].Address.State} {/if}
                    {if isset($sellermania_order.User[0].Address.Country) && !empty($sellermania_order.User[0].Address.Country)}{$sellermania_order.User[0].Address.Country}<br>{/if}

                    {if isset($sellermania_order.User[0].Address.ShippingPhone) && !empty($sellermania_order.User[0].Address.ShippingPhone)}{$sellermania_order.User[0].Address.ShippingPhone}<br>{/if}
                </td>
                <td width="50%" align="right">
                    {if isset($sellermania_order.User[1].Name) && !empty($sellermania_order.User[1].Name)}
                        {$sellermania_order.User[1].Name}<br>
                    {else}
                        {$sellermania_order.User[0].Name}<br>
                    {/if}
                    {if isset($sellermania_order.User[1].Company) && !empty($sellermania_order.User[1].Company)}{$sellermania_order.User[1].Company}<br>{/if}

                    {if isset($sellermania_order.User[1].Address.Street1) && !empty($sellermania_order.User[1].Address.Street1)}{$sellermania_order.User[1].Address.Street1}<br>{/if}
                    {if isset($sellermania_order.User[1].Address.Street2) && !empty($sellermania_order.User[1].Address.Street2)}{$sellermania_order.User[1].Address.Street2}<br>{/if}

                    {if isset($sellermania_order.User[1].Address.ZipCode) && !empty($sellermania_order.User[1].Address.ZipCode)}{$sellermania_order.User[1].Address.ZipCode} {/if}
                    {if isset($sellermania_order.User[1].Address.City) && !empty($sellermania_order.User[1].Address.City)}{$sellermania_order.User[1].Address.City}<br>{/if}

                    {if isset($sellermania_order.User[1].Address.State) && !empty($sellermania_order.User[1].Address.State)}{$sellermania_order.User[1].Address.State} {/if}
                    {if isset($sellermania_order.User[1].Address.Country) && !empty($sellermania_order.User[1].Address.Country)}{$sellermania_order.User[1].Address.Country}<br>{/if}

                    {if isset($sellermania_order.User[1].Address.ShippingPhone) && !empty($sellermania_order.User[1].Address.ShippingPhone)}{$sellermania_order.User[1].Address.ShippingPhone}<br>{/if}
                </td>
            </tr>
        </table>
    </div>


    {********************************************************}
    {*************** ORDER SUMMARY TEMPLATE *****************}
    {********************************************************}
    <div id="sellermania-template-order-summary">
        <fieldset>
            <legend><img src="../img/admin/details.gif"> {l s='Order summary' mod='sellermania'}</legend>
            <table width="100%;" cellspacing="0" cellpadding="0" class="table">
                <tbody>
                    <tr>
                        <td>{l s='Total products:' mod='sellermania'}</td>
                        <td>{displayPrice price=($sellermania_order.OrderInfo.TotalProductsWithVAT) currency=$sellermania_currency->id}</td>
                    </tr>
                    <tr>
                        <td>{l s='Shipping:' mod='sellermania'}</td>
                        <td>{displayPrice price=$sellermania_order.OrderInfo.Transport.Amount.Price currency=$sellermania_currency->id}</td>
                    </tr>
                    <tr>
                        <td>{l s='Insurance:' mod='sellermania'}</td>
                        <td>{displayPrice price=$sellermania_order.OrderInfo.TotalInsurance currency=$sellermania_currency->id}</td>
                    </tr>
                    {if isset($sellermania_order.OrderInfo.OptionalFeaturePrice)}
                    <tr>
                        <td>{l s='Management fees:' mod='sellermania'}</td>
                        <td>{displayPrice price=$sellermania_order.OrderInfo.OptionalFeaturePrice currency=$sellermania_currency->id}</td>
                    </tr>
                    {/if}
                    <tr>
                        <td>{l s='Other:' mod='sellermania'}</td>
                        <td>{displayPrice price=$sellermania_order.OrderInfo.TotalPromotionDiscount currency=$sellermania_currency->id}</td>
                    </tr>
                    {if isset($sellermania_order.OrderInfo.RefundedAmount)}
                    <tr>
                        <td>{l s='Refunded amount:' mod='sellermania'}</td>
                        <td>{displayPrice price=$sellermania_order.OrderInfo.RefundedAmount currency=$sellermania_currency->id}</td>
                    </tr>
                    {/if}
                    <tr>
                        <td style="color:red;font-weight:bold">{l s='Total amount:' mod='sellermania'}</td>
                        <td style="color:red;font-weight:bold">{displayPrice price=$sellermania_order.OrderInfo.TotalAmount.Amount.Price currency=$sellermania_currency->id}</td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table width="100%;" cellspacing="0" cellpadding="0" class="table">
                <tbody>
                <tr>
                    <td>{l s='Order date:' mod='sellermania'}</td>
                    <td>{dateFormat date=$sellermania_order.OrderInfo.Date full=true}</td>
                </tr>
                <tr>
                    <td>{l s='Payment date:' mod='sellermania'}</td>
                    <td>{dateFormat date=$sellermania_order.Paiement.Date full=true}</td>
                </tr>
                <tr>
                    <td>{l s='Order ID:' mod='sellermania'}</td>
                    <td>{$sellermania_order.OrderInfo.OrderId}</td>
                </tr>
                </tbody>
            </table>
            <br>
            <form action="" method="POST">
            <table width="100%;" cellspacing="0" cellpadding="0" class="table">
                <tbody>
                <tr>
                    <td>{l s='Shipping carrier:' mod='sellermania'}</td>
                    <td>
                        {if $sellermania_status_to_ship eq 1}
                            <input type="text" name="shipping_name" id="shipping_name" value="{if !empty($sellermania_order.OrderInfo.Transport.Name)}{$sellermania_order.OrderInfo.Transport.Name}{/if}" />
                        {else}
                            {if empty($sellermania_order.OrderInfo.Transport.Name)}-{else}{$sellermania_order.OrderInfo.Transport.Name}{/if}
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td>{l s='Shipping type:' mod='sellermania'}</td>
                    <td>{if empty($sellermania_order.OrderInfo.Transport.ShippingType)}-{else}{$sellermania_order.OrderInfo.Transport.ShippingType}{/if}</td>
                </tr>
                <tr>
                    <td>{l s='Tracking number:' mod='sellermania'}</td>
                    <td>
                        {if empty($sellermania_order.OrderInfo.Transport.TrackingNumber) && $sellermania_status_to_ship eq 1}
                            <input type="text" name="tracking_number" id="tracking_number" />
                        {else}
                            {if empty($sellermania_order.OrderInfo.Transport.TrackingNumber)}-{else}{$sellermania_order.OrderInfo.Transport.TrackingNumber}{/if}
                        {/if}
                    </td>
                </tr>
                </tbody>
            </table>
            {if $sellermania_status_to_ship eq 1}
                <input type="hidden" name="sellermania_tracking_registration" value="yes" />
                <p align="center"><input type="submit" value="{l s='Validate' mod='sellermania'}" class="button btn btn-default" /></p>
            {/if}
            {if is_array($sellermania_shipping_status_update)}
                    <br clear="left" /><br />
                    <div class="{if $sellermania_shipping_status_update.Status eq 'SUCCESS'}conf alert alert-success{else}error alert alert-danger{/if}" style="float:left">
                        {l s='Status change result:' mod='sellermania'}<br>
                        <ul>
                            {foreach from=$sellermania_shipping_status_update.OrderItemConfirmationStatus item=result}
                                <li>
                                    - {l s='Order line status update for sku' mod='sellermania'} "{$result.sku}" : {$result.Status}
                                    {if isset($result.Message)}<br><i>{$result.Message}</i>{/if}
                                </li>
                            {/foreach}
                        </ul>
                    </div>
            {/if}
            </form>
        </fieldset>


        {********************************************************}
        {**************** FORM STATUS TEMPLATE ******************}
        {********************************************************}
        <form id="sellermania_status_form" name="sellermania_status_form" action="" method="POST"></form>

    </div>


    {***************************************************************}
    {*************** RESULT STATUS UPDATE TEMPLATE *****************}
    {***************************************************************}
    {if is_array($sellermania_status_update)}
    <div id="sellermania-template-status-update">
        <br clear="left" /><br />
        <div class="{if $sellermania_status_update.Status eq 'SUCCESS'}conf alert alert-success{else}error alert alert-danger{/if}" style="float:left">
            {l s='Status change result:' mod='sellermania'}<br>
            <ul>
                {foreach from=$sellermania_status_update.OrderItemConfirmationStatus item=result}
                    <li>
                        - {l s='Order line status update for sku' mod='sellermania'} "{$result.sku}" : {$result.Status}
                        {if isset($result.Message)}<br><i>{$result.Message}</i>{/if}
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
    {/if}

    {if isset($sellermania_error)}
    <div id="sellermania-template-error">
        <br clear="left" /><br />
        <div class="error alert alert-danger" style="float:left">
            {$sellermania_error}
        </div>
    </div>
    {/if}

</div>

{* Fix to avoid JS error on google map load since PS 1.6.0.6 *}
<div id="map-delivery-canvas" style="display:none"></div>
<div id="map-invoice-canvas" style="display:none"></div>

{*****************************************}
{*************** JS DATA *****************}
{*****************************************}
<script>
    var short_sku = '';
    var sellermania_products = new Array();

    {foreach from=$sellermania_order.OrderInfo.Product item=product}

    short_sku = '{if isset($product.Sku)}{$product.Sku|addslashes}{else}no-sku{/if}';
    short_sku = short_sku.substr(0, 32).trim();

    sellermania_products[short_sku] = new Array();
    sellermania_products[short_sku]['order_item_id'] = '{if isset($product.OrderItemId)}{$product.OrderItemId}{/if}';
    sellermania_products[short_sku]['external_order_id'] = '{if isset($product.ExternalOrderId)}{$product.ExternalOrderId}{/if}';
    sellermania_products[short_sku]['status_id'] = '{if isset($product.Status)}{$product.Status}{/if}';
    sellermania_products[short_sku]['status'] = '{if isset($product.Status) && isset($sellermania_status_list[$product.Status])}{$sellermania_status_list[$product.Status]|addslashes}{/if}';
    sellermania_products[short_sku]['sku'] = '{if isset($product.Sku)}{$product.Sku}{/if}';
    sellermania_products[short_sku]['ean'] = '{if isset($product.Ean)}{$product.Ean}{/if}';
    sellermania_products[short_sku]['product_id'] = '{if isset($product.ProductId)}{$product.ProductId}{/if}';
    sellermania_products[short_sku]['item_condition_id'] = '{if isset($product.ItemCondition)}{$product.ItemCondition}{/if}';
    sellermania_products[short_sku]['item_condition'] = '{if isset($product.ItemCondition) && isset($sellermania_conditions_list[$product.ItemCondition])}{$sellermania_conditions_list[$product.ItemCondition]|addslashes}{/if}';
    sellermania_products[short_sku]['insurance_price'] = '{if isset($product.InsurancePrice.Amount.Price) && $product.InsurancePrice.Amount.Price gt 0}{$product.InsurancePrice.Amount.Price}{else}0{/if}';
    sellermania_products[short_sku]['currency'] = '{$sellermania_order.OrderInfo.Amount.Currency}';

    {/foreach}

    var insurance_price_label = '{l s='Insurance price:' mod='sellermania'}';
    var order_item_id_label = '{l s='Order item ID:' mod='sellermania'}';
    var external_order_id_label = '{l s='External order ID:' mod='sellermania'}';
    var sku_label = '{l s='Sku:' mod='sellermania'}';
    var ean_label = '{l s='Ean:' mod='sellermania'}';
    var asin_label = '{l s='ASIN:' mod='sellermania'}';
    var condition_label = '{l s='Condition:' mod='sellermania'}';
    var status_label = '{l s='Status:' mod='sellermania'}';
    var confirm_label = '{l s='Confirm' mod='sellermania'}';
    var cancel_label = '{l s='Cancel' mod='sellermania'}';
    var unknown_label = '{l s='Unknown' mod='sellermania'}';

    {if isset($smarty.get.edit_status)}
        var sellermania_order_edit_status = true;
    {else}
        var sellermania_order_edit_status = false;
    {/if}

    {if $sellermania_enable_native_refund_system eq 'true'}
        var sellermania_enable_native_refund_system = true;
    {else}
        var sellermania_enable_native_refund_system = false;
    {/if}

    {if $sellermania_enable_native_order_interface == 'yes'}
        var sellermania_enable_native_order_interface = true;
    {else}
        var sellermania_enable_native_order_interface = false;
    {/if}




</script>
<script type="text/javascript" src="{$sellermania_module_path}views/js/displayAdminOrder-{$ps_version}.js"></script>
<script type="text/javascript" src="{$sellermania_module_path}views/js/displayAdminOrder.js"></script>
