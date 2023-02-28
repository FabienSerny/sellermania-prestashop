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

{if $sm_wizard_launched == 0}
    <div id="sm-wz-welcome-page" class="panel">
        <div class="text-center">
            <img src="{$sellermania_module_path}views/img/sellermania-logo.png" style="width: 450px;" alt="{l s='Sellermania' mod='sellermania'}">
            <h4 class="text-sm">{l s="The feed manager that aligns your prices and synchronizes your stocks" mod='sellermania'}</h4>
        </div>
        <div class="row">
            <div class="col-lg-6 text-center">
                <img src="{$sellermania_module_path}views/img/welcome-page-img.png" style="max-width: 100%;" alt="">
            </div>
            <div class="col-lg-6">
                <div class="sm-welcome-page-content-wrapper">
                    <h2 class="text-primary" style="margin-top: 0 !important;">{l s="Realize your marketplace potential" mod='sellermania'}</h2>
                    <p>
                        {l s="Link your Prestashop with Sellermania and" mod='sellermania'}<br>
                        <strong>{l s="realize your marketplace potential" mod='sellermania'}</strong>
                    </p>
                    <a href="#" id="btn-launch-wizard" class="btn btn-primary">{l s="Launch the installation wizard" mod='sellermania'}</a>
                    <p>
                        {l s="Don't have a Sellermania account?" mod='sellermania'}
                        <a target="_blank" href="https://www.sellermania.com/contact/">{l s="Contact us" mod='sellermania'}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
{/if}

<div class="sellermaniaWrapper panel">

    <h2 align="center"><img src="{$sellermania_module_path}views/img/sellermania-logo.png" style="max-width:100%" alt="{l s='Sellermania' mod='sellermania'}" /></h2>

    {if isset($smarty.get.see) && $smarty.get.see eq 'orders-error'}
        {include file="$templates_dir/displayGetContentOrdersInError.bootstrap.tpl"}
    {else}

        {if $sm_wizard_launched == 0}
            <div class="panel">
                {*<h3 class="card-header">
                    <span class="sm-icon sm-wizard-icon"></span>{l s='Installation Wizard' mod='sellermania'}
                </h3>*}
                {include file="$templates_dir/displayGetContentWizard.bootstrap.tpl"}
            </div>

            <script type="text/javascript" src="{$sellermania_module_path}views/js/app-wizard.js"></script>
            {*<script src="{$sellermania_module_path}views/js/sm-wizard.js"></script>*}
            <link rel="stylesheet" href="{$sellermania_module_path}views/css/sm-wizard.css">
        {else}
            <div id="sellermania-admin-tab">

                <div class="tabwrapper">
                    <ul id="form-nav" class="nav nav-tabs js-nav-tabs">
                        <li class="nav-item"><a class="nav-link" href="#sellermania-module-help"> <span class="sm-icon sm-help-icon"></span> {l s='Help' mod='sellermania'} </a></li>
                        <li class="nav-item"><a class="nav-link" href="#sellermania-module-export"> <span class="sm-icon sm-export-icon"></span> {l s='Export catalog' mod='sellermania'} {if $field_errors_export_catalog|@count > 0}<span class="sm-icon sm-error"></span>{/if}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#sellermania-module-import"> <span class="sm-icon sm-import-icon"></span> {l s='Import orders' mod='sellermania'}  {if $field_errors_import_orders|@count > 0}<span class="sm-icon sm-error"></span>{/if}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#sellermania-module-search"> <span class="sm-icon sm-search-icon"></span> {l s='Search orders' mod='sellermania'} </a></li>
                    </ul>
                </div>
                <br style="clear: left">

                {if isset($sm_config_is_good)}
                    {if $sm_config_is_good eq true and empty($field_errors_import_orders)}
                        <div class="alert alert-success">{l s="Your configuration is successfully saved" mod='sellermania'}</div>
                    {else}
                        <div class="alert alert-danger">
                            {l s="Some errors were detected. We kept a draft for you so you can modify it later." mod='sellermania'}<br>
                            {l s="More details are at the bottom of the concerned section." mod='sellermania'}
                        </div>
                    {/if}
                {/if}

                <div id="sellermania-module-help" class="panel">
                    <h3 class="card-header">
                        <span class="sm-icon sm-help-icon"></span>{l s='Help' mod='sellermania'}
                    </h3>
                    <div class="margin-form">
                        <h4>{l s='You do not know how to configure the module or how it works?' mod='sellermania'}</h4>
                        <p><strong>{l s='Please look at the documentation by clicking on the button below.' mod='sellermania'}</strong></p>
                        <p><a href="http://www.froggy-commerce.com/docs/sellermania/{$documentation_iso_code}" class="btn btn-default" target="_blank">{l s='See the documentation' mod='sellermania'}</a></p>
                    </div>

                    <div class="alert alert-warning" style="margin-top: 30px;">
                        <h4 style="margin-bottom: 5px;">{l s="Having trouble configuring your module ?" mod='sellermania'}</h4>
                        <p style="margin-bottom: 10px;">{l s="You can relaunch the installation wizard so you can have a guided configuration of your module" mod='sellermania'}</p>
                        <form method="POST">
                            <button type="submit" name="relaunch_wizard" class="btn btn-primary">{l s="Relaunch the installation wizard" mod='sellermania'}</button>
                        </form>
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
        {/if}


    {/if}
    <script type="text/javascript" src="{$sellermania_module_path}views/js/displayGetContent.js"></script>
    <link type="text/css" rel="stylesheet" href="{$sellermania_module_path}views/css/displayGetContent.css" />

    <link rel="stylesheet" href="{$sellermania_module_path}lib/select2/select2.min.css">
    <link rel="stylesheet" href="{$sellermania_module_path}lib/select2/select2.bootstrap.min.css">
    <script href="{$sellermania_module_path}lib/select2/select2.min.js"></script>

    <script>
        $(function () {
            $("body").on("domChanged", function () {
                $('.select2').select2();
            });
            $('.select2').select2();
        })
    </script>

    <p style="text-align: center"><small>Sellermania module v{$sm_module_version}</small></p>
</div>
