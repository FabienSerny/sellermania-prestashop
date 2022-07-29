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

<div id="sellermania-admin-tab">
    <div class="tabwrapper">
        <ul id="form-nav" class="nav nav-tabs js-nav-tabs">
            <li class="nav-item"><a class="nav-link" href="{$module_url}#sellermania-module-help"> <span class="sellermania-icon"></span> {l s='Help' mod='sellermania'} </a></li>
            <li class="nav-item"><a class="nav-link" href="{$module_url}#sellermania-module-export"> <span class="sellermania-icon"></span> {l s='Export catalog' mod='sellermania'} </a></li>
            <li class="nav-item"><a class="nav-link" href="{$module_url}#sellermania-module-import"> <span class="sellermania-icon"></span> {l s='Import orders' mod='sellermania'} </a></li>
            <li class="nav-item"><a class="nav-link" href="{$module_url}#sellermania-module-search"> <span class="sellermania-icon"></span> {l s='Search orders' mod='sellermania'} </a></li>            
        </ul>
    </div>
</div>
<br clear="left"><br>
<div class="panel">
    <h3 class="card-header">
        <span class="sellermania-icon"></span>{l s='Importation errors' mod='sellermania'}
    </h3>
    <div class="margin-form">
        <p><b>{$nb_orders_in_error}</b> {l s='orders could not be imported, for more informations please contact the team' mod='sellermania'} <a href="http://www.froggy-commerce.com" target="_blank">{l s='Froggy Commerce' mod='sellermania'}</a>.</p><br>
        {if isset($orders_in_error) && !empty($orders_in_error)}
            <div class="alert alert-danger" id="import_details_error" style="">
                {l s='Errors occurred' mod='sellermania'}:<br>
        <ul>
            {foreach from=$orders_in_error item=order_in_error}
                <li>
                    {$order_in_error.id_sellermania_order}) {$order_in_error.marketplace} - {$order_in_error.ref_order} ({$order_in_error.amount_total}) : {$order_in_error.customer_name}<br>
                            <b>{$order_in_error.error}</b>
                            <a href="{$module_url}&see=orders-error&reimport={$order_in_error.id_sellermania_order}">{l s='Try to reimport during next importation' mod='sellermania'}</a></li>
            {/foreach}
        </ul>
            </div>
        {/if}
        {if isset($smarty.get.reimport) && !empty($smarty.get.reimport)}
            <div class="alert alert-info" id="import_details_info" style=""><p class="conf"><strong>{l s='The module will try to reimport this order during next importation.' mod='sellermania'}</strong></p></div>
        {/if}

        <p><b><a href="{$module_url}&see=orders-error&reimport=all">{l s='Try to reimport all orders during next importation' mod='sellermania'}</a></b></p>
        <br/>
        <p><a class="btn btn-default" href="{$module_url}">{l s='Return' mod='sellermania'}</a></p>
    </div>
</div>
