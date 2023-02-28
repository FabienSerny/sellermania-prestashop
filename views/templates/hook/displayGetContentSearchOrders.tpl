{*
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
*}

<h3 class="card-header">
    <span class="sm-icon sm-search-icon"></span>{l s='Search orders' mod='sellermania'}
</h3>
<div class="margin-form">
    <form action="" method="post">
        <fieldset>
            <div class="form-group clearfix">
                <div class="row">
                    <div class="col-lg-4">
                        <label>{l s='Search marketplace orders by reference' mod='sellermania'}</label>
                    </div>
                    <div class="col-lg-8">
                        <input type="text" name="marketplace_order_reference" />
                    </div>
                </div>
            </div>

            <div class="panel-footer clearfix">
                <input type="submit" name="search_orders" value="{l s='Validate' mod='sellermania'}" class="btn btn-default pull-right" />
            </div>

            {if isset($sm_orders_found)}
                {if empty($sm_orders_found)}
                    <p>{l s='No orders was found with an order reference containing' mod='sellermania'} <b>{$smarty.post.marketplace_order_reference}</b></p>
                {else}
                    <p><b>{$sm_orders_found|@count}</b> {l s='was/were found with an order reference containing' mod='sellermania'} <b>{$smarty.post.marketplace_order_reference}</b></p>
                    <br/>
                    <table class="table sellermania_search_order">
                        <tr><th scope="col">ID</th><th scope="col">Marketplace</th><th scope="col">Order Reference</th><th scope="col">Name</th><th scope="col">Actions</th></tr>

                        {foreach from=$sm_orders_found item=sm_order}
                            <tr>
                                <td scope="row"><b>#{$sm_order.id_order}</b></td>
                                <td>{$sm_order.marketplace}</td>
                                <td>{$sm_order.ref_order}</td>
                                <td>{$sm_order.customer_name}</td>
                                <td><a target="_blank" href="index.php?controller=AdminOrders&id_order={$sm_order.id_order}&vieworder&token={$order_token_tab}"><i class="material-icons">zoom_in</i><span class="icon-text"></span>View</a></td>
                            </tr>
                        {/foreach}
                    </table>
                {/if}
            {/if}
        </fieldset>
    </form>
</div>
