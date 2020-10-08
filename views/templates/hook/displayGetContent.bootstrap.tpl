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

<h2 align="center"><img src="{$sellermania_module_path}views/img/sellermania-logo.png" style="max-width:100%" alt="{l s='Sellermania' mod='sellermania'}" /></h2>

{if isset($smarty.get.see) && $smarty.get.see eq 'orders-error'}
    {include file="$templates_dir/displayGetContentOrdersInError.bootstrap.tpl"}
{else}

    <div id="sellermania-admin-tab">

        <ul id="sellermania-admin-tab-ul">
            <li><a href="#sellermania-module-help"> <img src="{$sellermania_module_path}logo.gif" alt="" title=""> {l s='Help' mod='sellermania'} </a></li>
            <li><a href="#sellermania-module-export"> <img src="{$sellermania_module_path}logo.gif" alt="" title=""> {l s='Export catalog' mod='sellermania'} </a></li>
            <li><a href="#sellermania-module-import"> <img src="{$sellermania_module_path}logo.gif" alt="" title=""> {l s='Import orders' mod='sellermania'} </a></li>
            <li><a href="#sellermania-module-search"> <img src="{$sellermania_module_path}logo.gif" alt="" title=""> {l s='Search orders' mod='sellermania'} </a></li>
        </ul>
        <br clear="left"><br>

        <div id="sellermania-module-help" class="panel">
            <div class="panel-heading">
                <legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />&nbsp;{l s='Help' mod='sellermania'}</legend>
            </div>
            <div class="margin-form">
                <h4>{l s='You do not know how to configure the module? You don\'t know how it works?' mod='sellermania'}</h4>
                <p><strong>{l s='Please look at the documentation by clicking on the button below.' mod='sellermania'}</strong></p>
                <p><a href="http://www.froggy-commerce.com/docs/sellermania/{$documentation_iso_code}" class="btn btn-default" target="_blank">{l s='See the documentation' mod='sellermania'}</a></p>
            </div>
        </div>

        <div id="sellermania-module-export" class="panel hidden">
            {include file="$templates_dir/displayGetContentExportCatalog.bootstrap.tpl"}
        </div>

        <div id="sellermania-module-import" class="panel hidden">
            {include file="$templates_dir/displayGetContentImportOrders.bootstrap.tpl"}
        </div>

        <div id="sellermania-module-search" class="panel hidden">
            {include file="$templates_dir/displayGetContentSearchOrders.bootstrap.tpl"}
        </div>
    </div>

    <script type="text/javascript" src="{$sellermania_module_path}views/js/displayGetContent.js"></script>
    <link type="text/css" rel="stylesheet" href="{$sellermania_module_path}views/css/displayGetContent.css" />

{/if}
