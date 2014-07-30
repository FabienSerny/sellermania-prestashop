{*
* 2010 - 2014 Sellermania / Froggy Commerce / 23Prod SARL
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
*  @copyright	2010-2014 Sellermania / Froggy Commerce / 23Prod SARL
*  @version		1.0
*  @license		http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<h2>{l s='Sellermania' mod='sellermania'}</h2>

{if isset($smarty.get.see) && $smarty.get.see eq 'orders-error'}

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

        {if isset($smarty.get.reimport)}
            <div class="alert alert-success"><p class="conf"><strong>{l s='The module will try to reimport this order during next importation.' mod='sellermania'}</strong></p></div>
        {/if}

        <p><u><a href="{$module_url}">{l s='Return' mod='sellermania'}</a></u></p>
    </div>
</div>

{else}

<div class="panel">
	<div class="panel-heading">
		<legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />&nbsp;{l s='Sellermania help' mod='sellermania'}</legend>
	</div>
	<div class="margin-form">
		<h4>{l s='You do not know how to configure the module? You don\'t know how it works?' mod='sellermania'}</h4>
		<p><strong>{l s='Please look at the documentation by clicking on the button below.' mod='sellermania'}</strong></p>
		<p><a href="http://www.froggy-commerce.com/docs/sellermania/{$documentation_iso_code}" class="btn btn-default" target="_blank">{l s='See the documentation' mod='sellermania'}</a></p>
	</div>
</div>

    <form action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" method="post">
        <fieldset>
            <div class="panel">
                <div class="panel-heading">
                    <legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />&nbsp;{l s='Sellermania configuration' mod='sellermania'}</legend>
                </div>
                {if isset($no_namespace_compatibility) && $no_namespace_compatibility eq '1'}
                    <div class="form-group clearfix">
                        <p class="col-lg-12">{l s='Your current PHP version is:' mod='sellermania'} {$php_version}</p>
                        <p class="col-lg-12">{l s='Your PHP version is too old, you must have at least PHP 5.3 to work with Sellermania API.' mod='sellermania'}</p>
                        <p class="col-lg-12">{l s='Please ask your hosting provider to update your PHP version.' mod='sellermania'}</p>
                    </div>
                {else}
                    <div class="form-group">
                        <div class="clearfix">
                            <label class="col-lg-4">{l s='Do you want to import Sellermania orders in PrestaShop?' mod='sellermania'}</label>
                            <div class="col-lg-8">
                                <input type="radio" name="sm_import_orders" id="sm_import_orders_yes" value="yes" {if $sm_import_orders eq 'yes'}checked="checked"{/if} />
                                <label for="sm_import_orders_yes">{l s='Yes' mod='sellermania'}</label>&nbsp;&nbsp;
                                <input type="radio" name="sm_import_orders" id="sm_import_orders_no" value="no" {if $sm_import_orders eq 'no' || $sm_import_orders eq ''}checked="checked"{/if} />
                                <label for="sm_import_orders_no">{l s='No' mod='sellermania'}</label>
                            </div>
                        </div>
                        <div id="sm_import_orders_credentials" class="clearfix">
                            <div class="form-group clearfix">
                                <label class="col-lg-12">{l s='Please fill up with the informations Sellermania provide you:' mod='sellermania'}</label>
                            </div>
                            <div class="form-group clearfix">
                                <label class="col-lg-4">{l s='Sellermania e-mail' mod='sellermania'}</label>
                                <div class="col-lg-8">
                                    <input type="text" name="sm_order_email" value="{$sm_order_email}" />
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="col-lg-4">{l s='Token webservices' mod='sellermania'}</label>
                                <div class="col-lg-8">
                                    <input type="text" name="sm_order_token" value="{$sm_order_token}" />
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="col-lg-4">{l s='Order endpoint' mod='sellermania'}</label>
                                <div class="col-lg-8">
                                    <input type="text" name="sm_order_endpoint" value="{$sm_order_endpoint}" />
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="col-lg-4">{l s='Confirm order endpoint' mod='sellermania'}</label>
                                <div class="col-lg-8">
                                    <input type="text" name="sm_confirm_order_endpoint" value="{$sm_confirm_order_endpoint}" />
                                </div>
                            </div>
							<div class="form-group clearfix">
								<label class="col-lg-4">{l s='Inventory endpoint' mod='sellermania'}</label>
								<div class="col-lg-8">
									<input type="text" name="sm_inventory_endpoint" value="{$sm_inventory_endpoint}" />
								</div>
							</div>
                            {if empty($sm_order_email)}
                                <div class="form-group clearfix">
                                    <strong><u>{l s='Note:' mod='sellermania'}</u></strong>
                                    {l s='These four credentials are provided by Sellermania, if you don\'t have them, please contact Sellermania.' mod='sellermania'}
                                </div>
                            {/if}
                        </div>
                    </div>
                    <div class="panel-footer">
                        <input type="submit" name="import_orders" value="{l s='Validate' mod='sellermania'}" class="btn btn-default pull-right" />
                    </div>
                    {if isset($sm_error_credentials)}<div class="alert alert-danger"><p class="error"><strong>{$sm_error_credentials|strip_tags}</strong></p></div>{/if}
                    {if isset($sm_confirm_credentials)}<div class="alert alert-success"><p class="conf"><strong>{l s='Configuration is valid' mod='sellermania'}</strong></p></div>{/if}

                    {if $sm_next_import ne ''}
                        <br>
                        <div class="margin-form">
                            <p>{l s='The last order importation was done:' mod='sellermania'} <b>{$sm_last_import}</b></p>
                            <p>{l s='Next order importation won\'t be done until:' mod='sellermania'} <b>{$sm_next_import}</b></p>
                            {if $nb_orders_in_error gt 0}
                                <br>
                                <h4>{l s='Importation errors:' mod='sellermania'}</h4>
                                <p><b>{$nb_orders_in_error}</b> {l s='orders could not be imported' mod='sellermania'} - <a href="{$module_url}&see=orders-error">{l s='See details' mod='sellermania'}</a></p>
                            {/if}
                        </div>
                    {/if}
                {/if}
            </div>
        </fieldset>
    </form>


<br />

<div class="panel">
    <div class="panel-heading">
        <legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />&nbsp;{l s='Sellermania export' mod='sellermania'}</legend>
    </div>
    <div class="margin-form">
        <p>{l s='You have two solutions to send your catalog to Sellermania' mod='sellermania'}</p><br>
        <p><b>1) {l s='Set a cron task' mod='sellermania'}</b></p>
        <p>{l s='Script path:' mod='sellermania'} {$script_path}/export.php {$sellermania_key}</p>
        <p>{l s='Generated files will be available at these urls:' mod='sellermania'}</p>
        <p>
        {foreach from=$files_list item=file key=iso_code}
            <strong>{$iso_code|strtoupper} :</strong> {$file.file} ({if isset($file.generated)}{l s='Generated on' mod='sellermania'} {$file.generated}{else}{l s='Not generated yet' mod='sellermania'}{/if})  <br>
        {/foreach}
        </p>
        {if $export_directory_writable ne 1}
        <div class="alert alert-danger"><p class="error"><strong>{l s='Beware, the following directory is not writable:' mod='sellermania'} {$script_path}/export/</strong></p></div>
        {/if}

        <br><p><b><u>{l s='OR' mod='sellermania'}</u></b></p><br>

        <p><b>2) {l s='Send these links to Sellermania' mod='sellermania'}</b></p>
        <p>
        {foreach from=$languages_list item=language}
            <strong>{$language.iso_code|strtoupper} :</strong> {$module_web_path}export.php?l={$language.iso_code|strtolower}&k={$sellermania_key} <br>
        {/foreach}
        </p>
    </div>
</div>


<script type="text/javascript" src="{$sellermania_module_path}views/js/displayGetContent.js"></script>

{/if}