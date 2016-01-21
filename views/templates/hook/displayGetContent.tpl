{*
* 2010-2015 Sellermania / Froggy Commerce / 23Prod SARL
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
*  @copyright	2010-2015 Sellermania / Froggy Commerce / 23Prod SARL
*  @version		1.0
*  @license		http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<h2>{l s='Sellermania' mod='sellermania'}</h2>

{if isset($smarty.get.see) && $smarty.get.see eq 'orders-error'}

<fieldset>
    <legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />{l s='Importation errors' mod='sellermania'}</legend>
    <div class="margin-form" style="padding-left:15px">
        <p><b>{$nb_orders_in_error}</b> {l s='orders could not be imported, for more information please contact the team' mod='sellermania'} <a href="http://www.froggy-commerce.com" target="_blank">{l s='Froggy Commerce' mod='sellermania'}</a>.</p><br>
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
            <div class="conf">{l s='The module will try to reimport this order during next importation.' mod='sellermania'}</div>
        {/if}

        <p><u><a href="{$module_url}">{l s='Return' mod='sellermania'}</a></u></p>
    </div>
</fieldset>

{else}

<fieldset>
	<legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />{l s='Sellermania help' mod='sellermania'}</legend>
	<div class="margin-form" style="padding-left:15px">
		<h3>{l s='You do not know how to configure the module? You don\'t know how it works?' mod='sellermania'}</h3>
		<p><strong>{l s='Please look at the documentation by clicking on the button below.' mod='sellermania'}</strong></p>
		<p><a href="http://www.froggy-commerce.com/docs/sellermania/{$documentation_iso_code}" target="_blank" id="see-documentation" class="sellermania-button">{l s='See the documentation' mod='sellermania'}</a></p>
    </div>
</fieldset>
<br>

<form action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" method="post">
<fieldset>
	<legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />{l s='Sellermania export' mod='sellermania'}</legend>
	<div class="margin-form" style="padding-left:15px">

		<p><b>{l s='Do you want to export all your catalog to Sellermania?' mod='sellermania'}</b></p><br>
		<div class="margin-form" style="padding-left:15px">
			<input type="radio" name="sm_export_all" id="sm_export_all_yes" value="yes" {if $sm_export_all eq 'yes'}checked="checked"{/if} /> {l s='Yes' mod='sellermania'}
			<input type="radio" name="sm_export_all" id="sm_export_all_no" value="no" {if $sm_export_all eq 'no' || $sm_export_all eq ''}checked="checked"{/if} /> {l s='No' mod='sellermania'}
		</div>
		<div id="sm_export_all_configuration">
			    <p>{l s='Please select the categories you want to export:' mod='sellermania'}</p>
				{$category_tree}
			    <br><br>
		</div>


		<p><b>{l s='Send these links to Sellermania' mod='sellermania'}</b></p>
		<p>
            {foreach from=$languages_list item=language}
				<strong>{$language.iso_code|strtoupper} :</strong> {$module_web_path}export.php?l={$language.iso_code|strtolower}&k={$sellermania_key} <br>
            {/foreach}
		</p>
        <br>
		<p><a href="#" id="see-advanced-export" class="sellermania-button">{l s='Advanced configuration' mod='sellermania'}</a></p>
        <br clear="left">
		<div id="advanced-export">
			<p><b>{l s='Set a cron task' mod='sellermania'}</b></p>
			<p>{l s='Script path:' mod='sellermania'} {$script_path}/export.php {$sellermania_key}</p>
			<p>{l s='Generated files will be available at these urls:' mod='sellermania'}</p>
			<p>
                {foreach from=$files_list item=file key=iso_code}
					<strong>{$iso_code|strtoupper} :</strong> {if isset($file.generated)}{$file.file} ({l s='Generated on' mod='sellermania'} {$file.generated}){else}{l s='Not generated yet' mod='sellermania'}{/if}  <br>
                {/foreach}
			</p>
            {if $export_directory_writable ne 1}<p class="error"><strong>{l s='Beware, the following directory is not writable:' mod='sellermania'} {$script_path}/export/</strong></p>{/if}

            <p>
                <label>{l s='Enable export combination name' mod='sellermania'}</label>
                <input type="radio" name="sm_enable_export_comb_name" id="sm_enable_export_comb_name_yes" value="yes" {if $sm_enable_export_comb_name eq 'yes'}checked="checked"{/if} /> {l s='Yes' mod='sellermania'}
                <input type="radio" name="sm_enable_export_comb_name" id="sm_enable_export_comb_name_no" value="no" {if $sm_enable_export_comb_name eq 'no' || $sm_enable_export_comb_name eq ''}checked="checked"{/if} /> {l s='No' mod='sellermania'}
            </p><br clear="left" />

		</div>

		<br><p><input type="submit" name="export_configuration" value="{l s='Validate' mod='sellermania'}" class="button" /></p>
        {if isset($sm_confirm_export_options)}<br><p class="conf"><strong>{l s='Configuration has been saved' mod='sellermania'}</strong></p>{/if}

	</div>
</fieldset>
</form>
<br />

<form action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" method="post">
    <fieldset>
        <legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />{l s='Sellermania configuration' mod='sellermania'}</legend>

        {if isset($no_namespace_compatibility) && $no_namespace_compatibility eq '1'}
            <p><strong>{l s='Your current PHP version is:' mod='sellermania'} {$php_version}</strong></p>
            <p><strong>{l s='Your PHP version is too old, you must have at least PHP 5.3 to work with Sellermania API.' mod='sellermania'}</strong></p>
            <p><strong>{l s='Please ask your hosting provider to update your PHP version.' mod='sellermania'}</strong></p>
        {else}
            <div class="margin-form" style="padding-left:15px">
                <p><b>{l s='Do you want to import Sellermania orders in PrestaShop?' mod='sellermania'}</b></p><br>
                <input type="radio" name="sm_import_orders" id="sm_import_orders_yes" value="yes" {if $sm_import_orders eq 'yes'}checked="checked"{/if} /> {l s='Yes' mod='sellermania'}
                <input type="radio" name="sm_import_orders" id="sm_import_orders_no" value="no" {if $sm_import_orders eq 'no' || $sm_import_orders eq ''}checked="checked"{/if} /> {l s='No' mod='sellermania'}
            </div>
            <div class="margin-form" style="padding-left:15px" id="sm_import_orders_credentials">
                <p><b>{l s='Please fill up with the informations Sellermania provide you:' mod='sellermania'}</b></p>
                <p><label>{l s='Sellermania e-mail' mod='sellermania'}</label> <input type="text" name="sm_order_email" value="{$sm_order_email}" /></p>
                <p><label>{l s='Token webservices' mod='sellermania'}</label> <input type="text" name="sm_order_token" value="{$sm_order_token}" /></p><br>
                <p><label>{l s='Order endpoint' mod='sellermania'}</label> <input type="text" name="sm_order_endpoint" value="{$sm_order_endpoint}" /></p>
                <p><label>{l s='Confirm order endpoint' mod='sellermania'}</label> <input type="text" name="sm_confirm_order_endpoint" value="{$sm_confirm_order_endpoint}" /></p>
				<p><label>{l s='Inventory endpoint' mod='sellermania'}</label> <input type="text" name="sm_inventory_endpoint" value="{$sm_inventory_endpoint}" /></p>
                {if empty($sm_order_email)}
                    <p><strong><u>{l s='Note:' mod='sellermania'}</u></strong> {l s='These four credentials are provided by Sellermania, if you don\'t have them, please contact Sellermania.' mod='sellermania'}</p>
                {/if}

				<p>
					<label>{l s='Synchronization by reference (optional)' mod='sellermania'}</label>
					<input type="radio" name="sm_stock_sync_option" id="sm_stock_sync_option_yes" value="yes" {if $sm_stock_sync_option eq 'yes'}checked="checked"{/if} /> {l s='Yes' mod='sellermania'}
					<input type="radio" name="sm_stock_sync_option" id="sm_stock_sync_option_no" value="no" {if $sm_stock_sync_option eq 'no' || $sm_stock_sync_option eq ''}checked="checked"{/if} /> {l s='No' mod='sellermania'}
				</p><br clear="left" />
				<p id="sm_stock_sync_option_configuration">

                    <b>{l s='Option 1:' mod='sellermania'}</b><br>
					<input type="radio" name="sm_stock_sync_option_1" id="sm_stock_sync_option_1_yes" value="yes" {if $sm_stock_sync_option_1 eq 'yes'}checked="checked"{/if} /> {l s='Yes' mod='sellermania'}
					<input type="radio" name="sm_stock_sync_option_1" id="sm_stock_sync_option_1_no" value="no" {if $sm_stock_sync_option_1 eq 'no' || $sm_stock_sync_option_1 eq ''}checked="checked"{/if} /> {l s='No' mod='sellermania'}
				    <br>
                    {l s='This feature allows you to "chain" some references to each other, which will permit to update the stock of all the chained references at the same time.' mod='sellermania'}<br>
                    {l s='Eg: You choose to chain with the 5 first characters of the SKU. If the product\'s SKU of an imported order is "D6HSIQKZJD", all products with SKU begins with "D6HSI" will have their stock updated.' mod='sellermania'}
					<br><br>

					<b>{l s='Option 2:' mod='sellermania'}</b><br>
					<input type="radio" name="sm_stock_sync_option_2" id="sm_stock_sync_option_2_yes" value="yes" {if $sm_stock_sync_option_2 eq 'yes'}checked="checked"{/if} /> {l s='Yes' mod='sellermania'}
					<input type="radio" name="sm_stock_sync_option_2" id="sm_stock_sync_option_2_no" value="no" {if $sm_stock_sync_option_2 eq 'no' || $sm_stock_sync_option_2 eq ''}checked="checked"{/if} /> {l s='No' mod='sellermania'}
				    <br>
                    {l s='This feature allows you to match the reference of product\'s SKU of an imported order with one of your PrestaShop.' mod='sellermania'}<br>
                    {l s='Eg: You choose to chain with the 5 first characters of the SKU. If the product\'s SKU of an imported order is "D6HSIQKZJD", PrestaShop will associated the product with the first one which SKU begins with "D6HSI".' mod='sellermania'}
					<br><br>

					<b>{l s='Configuration:' mod='sellermania'}</b><br>
                    {l s='Define how you want to chain the references, the X first or X last characters of the reference.' mod='sellermania'}<br>
                    {l s='Use the' mod='sellermania'} <input type="text" name="sm_stock_sync_nb_char" value="{$sm_stock_sync_nb_char|intval}" />
					<input type="radio" name="sm_stock_sync_position" id="sm_stock_sync_position_first" value="first" {if $sm_stock_sync_position eq 'first'}checked="checked"{/if} /> {l s='first' mod='sellermania'}
					<input type="radio" name="sm_stock_sync_position" id="sm_stock_sync_position_last" value="last" {if $sm_stock_sync_position eq 'last' || $sm_stock_sync_position eq ''}checked="checked"{/if} /> {l s='last' mod='sellermania'}
                    {l s='reference\'s characters.' mod='sellermania'}
					<br>
				</p>

				<p>
					<label>{l s='Alert me by e-mail when a reference is not found' mod='sellermania'}</label>
					<input type="radio" name="sm_alert_missing_ref_option" id="sm_alert_missing_ref_option_yes" value="yes" {if $sm_alert_missing_ref_option eq 'yes'}checked="checked"{/if} /> {l s='Yes' mod='sellermania'}
					<input type="radio" name="sm_alert_missing_ref_option" id="sm_alert_missing_ref_option_no" value="no" {if $sm_alert_missing_ref_option eq 'no' || $sm_alert_missing_ref_option eq ''}checked="checked"{/if} /> {l s='No' mod='sellermania'}
				</p><br clear="left" />
				<p id="sm_alert_missing_ref_option_configuration">
					<label>{l s='Your e-mail' mod='sellermania'}</label>
                    <input type="text" name="sm_alert_missing_ref_mail" value="{$sm_alert_missing_ref_mail}" />
                </p>

                <p>
                    <label>{l s='Enable native refund system' mod='sellermania'}</label>
                    <input type="radio" name="sm_enable_native_refund_system" id="sm_enable_native_refund_system_yes" value="yes" {if $sm_enable_native_refund_system eq 'yes'}checked="checked"{/if} /> {l s='Yes' mod='sellermania'}
                    <input type="radio" name="sm_enable_native_refund_system" id="sm_enable_native_refund_system_no" value="no" {if $sm_enable_native_refund_system eq 'no' || $sm_enable_native_refund_system eq ''}checked="checked"{/if} /> {l s='No' mod='sellermania'}
                </p><br clear="left" />

            </div>
            <p><label><input type="submit" name="import_orders" value="{l s='Validate' mod='sellermania'}" class="button" /></label></p>
            {if isset($sm_error_credentials)}<br><br><p class="error"><strong>{$sm_error_credentials|strip_tags}</strong></p>{/if}
            {if isset($sm_confirm_credentials)}<br><br><p class="conf"><strong>{l s='Configuration is valid' mod='sellermania'}</strong></p>{/if}

            {if $sm_next_import ne ''}
                <br>
                <div class="margin-form" style="padding-left:15px">
                    <p>{l s='The last order importation was done:' mod='sellermania'} <b>{$sm_last_import}</b></p>
                    <p>{l s='Next order importation won\'t be done until:' mod='sellermania'} <b>{$sm_next_import}</b></p>
                    {if $nb_orders_in_error gt 0}
                        <br>
                        <h4>{l s='Importation errors:' mod='sellermania'}</h4>
                        <p><b>{$nb_orders_in_error}</b> {l s='orders could not be imported' mod='sellermania'} - <a href="{$module_url}&see=orders-error">{l s='See details' mod='sellermania'}</a></p>
                    {/if}
                    <p>{l s='Product ID used when no product is matched during an order importation:' mod='sellermania'} <strong>{$sm_default_product_id}</strong>{if $sm_default_product->id lt 1} <strong>({l s='WARNING: Product could not be found!' mod='sellermania'})</strong>{/if}</p>
                </div>
            {/if}
        {/if}
    </fieldset>
</form>

<script type="text/javascript" src="{$sellermania_module_path}views/js/displayGetContent.js"></script>
<link type="text/css" rel="stylesheet" href="{$sellermania_module_path}views/css/displayGetContent.css" />

{/if}