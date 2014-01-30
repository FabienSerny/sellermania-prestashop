<div id="sellermania-template">
    <div id="sellermania-template-title">
        <h2>{l s='SellerMania order from the marketplace' mod='sellermania'} {$sellermania_order.OrderInfo.MarketPlace}</h2>
    </div>
    <div id="sellermania-template-customer">
        <legend><img src="../img/admin/tab-customers.gif" /> {l s='Customer information' mod='sellermania'}</legend>
        {$sellermania_order.User[0].FirstName} {$sellermania_order.User[0].LastName}<br>
        <b>{l s='E-mail:' mod='sellermania'}</b> <a href="mailto:{$sellermania_order.User[0].Email}">{$sellermania_order.User[0].Email}</a><br>
        {if isset($sellermania_order.User[0].ShippingPhone) && !empty($sellermania_order.User[0].ShippingPhone)}<b>{l s='Shipping phone:' mod='sellermania'}</b> {$sellermania_order.User[0].ShippingPhone}<br>{/if}
        {if isset($sellermania_order.User[0].UserPhone) && !empty($sellermania_order.User[0].UserPhone)}<b>{l s='User phone:' mod='sellermania'}</b> {$sellermania_order.User[0].UserPhone}<br>{/if}
    </div>
    <div id="sellermania-template-order-summary">
        <fieldset>
            <legend><img src="../img/admin/details.gif"> {l s='Order summary' mod='sellermania'}</legend>
            <table width="100%;" cellspacing="0" cellpadding="0" class="table">
                <tbody>
                    <tr>
                        <td>{l s='Total products:' mod='sellermania'}</td>
                        <td>{$sellermania_order.OrderInfo.Amount.Price|number_format:2} {$sellermania_order.OrderInfo.Amount.Currency}</td>
                    </tr>
                    <tr>
                        <td>{l s='Shipping:' mod='sellermania'}</td>
                        <td>{$sellermania_order.OrderInfo.Transport.Amount.Price|number_format:2} {$sellermania_order.OrderInfo.Amount.Currency}</td>
                    </tr>
                    <tr>
                        <td>{l s='Insurance:' mod='sellermania'}</td>
                        <td>0 {$sellermania_order.OrderInfo.Amount.Currency}</td>
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
        </fieldset>
    </div>
</div>

<script>
    var sellermania_products = new Array();

    {foreach from=$sellermania_order.OrderInfo.Product item=product}

    sellermania_products['{$product.Sku}'] = new Array();
    sellermania_products['{$product.Sku}']['order_item_id'] = '{if isset($product.OrderItemId)}{$product.OrderItemId}{/if}';
    sellermania_products['{$product.Sku}']['status_id'] = '{if isset($product.Status)}{$product.Status}{/if}';
    sellermania_products['{$product.Sku}']['status'] = '{if isset($product.Status) && isset($sellermania_status_list[$product.Status])}{$sellermania_status_list[$product.Status]}{/if}';
    sellermania_products['{$product.Sku}']['ean'] = '{if isset($product.Ean)}{$product.Ean}{/if}';
    sellermania_products['{$product.Sku}']['product_id'] = '{if isset($product.ProductId)}{$product.ProductId}{/if}';
    sellermania_products['{$product.Sku}']['item_condition_id'] = '{if isset($product.ItemCondition)}{$product.ItemCondition}{/if}';
    sellermania_products['{$product.Sku}']['item_condition'] = '{if isset($product.ItemCondition) && isset($sellermania_conditions_list[$product.ItemCondition])}{$sellermania_conditions_list[$product.ItemCondition]}{/if}';

    {/foreach}
</script>
<script type="text/javascript" src="{$sellermania_module_path}views/js/displayAdminOrder.js"></script>
