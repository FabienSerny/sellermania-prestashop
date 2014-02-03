<div id="sellermania-template">


    {************************************************}
    {*************** TITLE TEMPLATE *****************}
    {************************************************}
    <div id="sellermania-template-title">
        <h2>{l s='SellerMania order from the marketplace' mod='sellermania'} {$sellermania_order.OrderInfo.MarketPlace}</h2>
    </div>


    {***************************************************}
    {*************** CUSTOMER TEMPLATE *****************}
    {***************************************************}
    <div id="sellermania-template-customer">
        <legend><img src="../img/admin/tab-customers.gif" /> {l s='Customer information' mod='sellermania'}</legend>
        <b>{l s='Name:' mod='sellermania'}</b> {$sellermania_order.User[0].Name}<br>
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
                    {$sellermania_order.User[0].Name}<br>
                    {if isset($sellermania_order.User[0].Company) && !empty($sellermania_order.User[0].Company)}{$sellermania_order.User[0].Company}<br>{/if}

                    {if isset($sellermania_order.User[0].Address.Street1) && !empty($sellermania_order.User[0].Address.Street1)}{$sellermania_order.User[0].Address.Street1}<br>{/if}
                    {if isset($sellermania_order.User[0].Address.Street2) && !empty($sellermania_order.User[0].Address.Street2)}{$sellermania_order.User[0].Address.Street2}<br>{/if}

                    {if isset($sellermania_order.User[0].Address.ZipCode) && !empty($sellermania_order.User[0].Address.ZipCode)}{$sellermania_order.User[0].Address.ZipCode} {/if}
                    {if isset($sellermania_order.User[0].Address.City) && !empty($sellermania_order.User[0].Address.City)}{$sellermania_order.User[0].Address.City}<br>{/if}

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
                        <td>{($sellermania_order.OrderInfo.Amount.Price - $sellermania_order.OrderInfo.TotalInsurance)|number_format:2} {$sellermania_order.OrderInfo.Amount.Currency}</td>
                    </tr>
                    <tr>
                        <td>{l s='Shipping:' mod='sellermania'}</td>
                        <td>{$sellermania_order.OrderInfo.Transport.Amount.Price|number_format:2} {$sellermania_order.OrderInfo.Amount.Currency}</td>
                    </tr>
                    <tr>
                        <td>{l s='Insurance:' mod='sellermania'}</td>
                        <td>{$sellermania_order.OrderInfo.TotalInsurance} {$sellermania_order.OrderInfo.Amount.Currency}</td>
                    </tr>
                    <tr>
                        <td style="color:red;font-weight:bold">{l s='Total amount:' mod='sellermania'}</td>
                        <td style="color:red;font-weight:bold">{$sellermania_order.OrderInfo.TotalAmount.Amount.Price|number_format:2} {$sellermania_order.OrderInfo.Amount.Currency}</td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table width="100%;" cellspacing="0" cellpadding="0" class="table">
                <tbody>
                <tr>
                    <td>{l s='Order date:' mod='sellermania'}</td>
                    <td>{$sellermania_order.OrderInfo.Date}</td>
                </tr>
                <tr>
                    <td>{l s='Payment date:' mod='sellermania'}</td>
                    <td>{$sellermania_order.Paiement.Date}</td>
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
                        {if empty($sellermania_order.OrderInfo.Transport.Name) && $sellermania_status_to_ship eq 1}
                            <input type="text" name="shipping_name" id="shipping_name" />
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
                <p align="center"><input type="submit" value="{l s='Validate' mod='sellermania'}" class="button" /></p>
            {/if}
            {if is_array($sellermania_shipping_status_update)}
                    <br clear="left" /><br />
                    <div class="{if $sellermania_shipping_status_update.Status eq 'SUCCESS'}conf{else}error{/if}" style="float:left">
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
    </div>


    {***************************************************************}
    {*************** RESULT STATUS UPDATE TEMPLATE *****************}
    {***************************************************************}
    {if is_array($sellermania_status_update)}
    <div id="sellermania-template-status-update">
        <br clear="left" /><br />
        <div class="{if $sellermania_status_update.Status eq 'SUCCESS'}conf{else}error{/if}" style="float:left">
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
</div>



{*****************************************}
{*************** JS DATA *****************}
{*****************************************}
<script>
    var sellermania_products = new Array();

    {foreach from=$sellermania_order.OrderInfo.Product item=product}

    sellermania_products['{$product.Sku}'] = new Array();
    sellermania_products['{$product.Sku}']['order_item_id'] = '{if isset($product.OrderItemId)}{$product.OrderItemId}{/if}';
    sellermania_products['{$product.Sku}']['external_order_id'] = '{if isset($product.ExternalOrderId)}{$product.ExternalOrderId}{/if}';
    sellermania_products['{$product.Sku}']['status_id'] = '{if isset($product.Status)}{$product.Status}{/if}';
    sellermania_products['{$product.Sku}']['status'] = '{if isset($product.Status) && isset($sellermania_status_list[$product.Status])}{$sellermania_status_list[$product.Status]}{/if}';
    sellermania_products['{$product.Sku}']['ean'] = '{if isset($product.Ean)}{$product.Ean}{/if}';
    sellermania_products['{$product.Sku}']['product_id'] = '{if isset($product.ProductId)}{$product.ProductId}{/if}';
    sellermania_products['{$product.Sku}']['item_condition_id'] = '{if isset($product.ItemCondition)}{$product.ItemCondition}{/if}';
    sellermania_products['{$product.Sku}']['item_condition'] = '{if isset($product.ItemCondition) && isset($sellermania_conditions_list[$product.ItemCondition])}{$sellermania_conditions_list[$product.ItemCondition]}{/if}';
    sellermania_products['{$product.Sku}']['insurance_price'] = '{if isset($product.InsurancePrice.Amount.Price) && $product.InsurancePrice.Amount.Price gt 0}{$product.InsurancePrice.Amount.Price}{else}0{/if}';
    sellermania_products['{$product.Sku}']['currency'] = '{$sellermania_order.OrderInfo.Amount.Currency}';

    {/foreach}
</script>
<script type="text/javascript" src="{$sellermania_module_path}views/js/displayAdminOrder.js"></script>
