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


<div class="panel">
    <div class="panel-heading">
        <legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />&nbsp;{l s='Importation errors' mod='sellermania'}</legend>
    </div>
    <div class="margin-form">
        <p><b>{$nb_orders_in_error}</b> {l s='orders could not be imported, for more informations please contact the team' mod='sellermania'} <a href="http://www.froggy-commerce.com" target="_blank">{l s='Froggy Commerce' mod='sellermania'}</a>.</p><br>
        <ul>
            {foreach from=$orders_in_error item=order_in_error}
                <li>
                    {$order_in_error.id_sellermania_order}) {$order_in_error.marketplace} - {$order_in_error.ref_order} ({$order_in_error.amount_total}) : {$order_in_error.customer_name}<br>
                    <b>{$order_in_error.error}</b><br>
                    <a href="{$module_url}&see=orders-error&reimport={$order_in_error.id_sellermania_order}">{l s='Try to reimport during next importation' mod='sellermania'}</a>
                    <br><br>
                </li>
            {/foreach}
        </ul>
        <p><a href="{$module_url}&see=orders-error&reimport=all">{l s='Try to reimport all orders during next importation' mod='sellermania'}</a></p>

        {if isset($smarty.get.reimport)}
            <div class="alert alert-success"><p class="conf"><strong>{l s='The module will try to reimport this order during next importation.' mod='sellermania'}</strong></p></div>
        {/if}

        <p><u><a href="{$module_url}">{l s='Return' mod='sellermania'}</a></u></p>
    </div>
</div>
