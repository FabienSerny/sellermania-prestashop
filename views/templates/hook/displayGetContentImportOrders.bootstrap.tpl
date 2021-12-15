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

<div class="panel-heading">
    <legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />&nbsp;{l s='Import orders' mod='sellermania'}</legend>
</div>
<div class="margin-form">
    <form action="" method="post">
        <fieldset>

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

                        <br>
                        <h2>1. {l s='Settings' mod='sellermania'}</h2>
                        <hr />

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
                        <div class="form-group clearfix" style="display:none">
                            <label class="col-lg-4">{l s='Inventory endpoint' mod='sellermania'}</label>
                            <div class="col-lg-8">
                                <input type="text" name="sm_inventory_endpoint" value="{$sm_inventory_endpoint}" />
                            </div>
                        </div>

                        {if empty($sm_order_email)}
                            <div class="form-group clearfix">
                                <strong><u>{l s='Note:' mod='sellermania'}</u></strong>
                                {l s='These credentials are provided by Sellermania, if you don\'t have them, please contact Sellermania.' mod='sellermania'}
                            </div>
                        {/if}


                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Import order of last X days' mod='sellermania'}</label>
                            <div class="col-lg-8">
                                <input type="text" name="sm_order_import_past_days" value="{$sm_order_import_past_days}" />
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Import X orders during an importation request' mod='sellermania'}</label>
                            <div class="col-lg-8">
                                <input type="text" name="sm_order_import_limit" value="{$sm_order_import_limit}" />
                            </div>
                        </div>


                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Importation method' mod='sellermania'}</label>
                            <div class="col-lg-4">
                                <input type="radio" name="sm_import_method" id="sm_import_method_cron" value="cron" {if $sm_import_method eq 'cron'}checked="checked"{/if} />
                                <label for="sm_import_method_cron">{l s='Cron' mod='sellermania'}</label>&nbsp;&nbsp;
                                <input type="radio" name="sm_import_method" id="sm_import_method_automatic" value="automatic" {if $sm_import_method eq 'automatic' || $sm_import_method eq ''}checked="checked"{/if} />
                                <label for="sm_import_method_automatic">{l s='Automatic' mod='sellermania'}</label>
                                <p>
                                    <b><u>{l s='Note:' mod='sellermania'}</u></b><br>
                                    - {l s='Automatic importation is easier to configure (you have nothing to do), but it can cause some issues (slow down back office during importation, import some orders in double if two people are working on the back office, or stock issues if nobody is using the back office during the day).' mod='sellermania'}<br>
                                    - {l s='So, if you have the possibility, use cron importation (just contact your hosting provider, he should be able to help you).' mod='sellermania'}
                                </p>
                            </div>
                        </div>
                        <div class="form-group clearfix" id="sm_import_method_cron_configuration">
                            <label class="col-lg-4">{l s='Cron script to call' mod='sellermania'}</label>
                            <div class="col-lg-8">
                                <u>{l s='Via command line (classic cron)' mod='sellermania'} :</u> php -f {$script_path}/import.php<br><br>
                                <u>{l s='Via url (webcron)' mod='sellermania'} :</u> <a href="http://{$smarty.server.SERVER_NAME}/modules/sellermania/import.php?k={$sellermania_key}" target="_blank">http://{$smarty.server.SERVER_NAME}/modules/sellermania/import.php?k={$sellermania_key}</a>
                            </div>
                        </div>


                        <br>
                        <h2>2. {l s='Marketplaces' mod='sellermania'}</h2>
                        <hr />


                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Sellermania marketplaces configuration' mod='sellermania'}</label>
                            <div class="col-lg-8 form-group clearfix">
                                <label class="col-lg-3">&nbsp;</label>
                                <div class="col-lg-3">Action</div>
                                <div class="col-lg-3">Transporteur</div>
                                <div class="col-lg-3">Service livraison</div>
                                <br clear="left">
                                {foreach from=$sm_marketplaces key=sm_marketplace_name item=sm_marketplace}
                                    <label class="col-lg-3">{$sm_marketplace_name|replace:'_':'.'}</label>
                                    <div class="col-lg-3">
                                        <select name="{$sm_marketplace.key}" id="{$sm_marketplace.key}" style="width:100%">
                                            <option value="NO" {if $sm_marketplace.value eq 'NO'}selected{/if}>{l s='Do not import the orders' mod='sellermania'}</option>
                                            <option value="MANUAL" {if $sm_marketplace.value eq 'MANUAL'}selected{/if}>{l s='Import the orders' mod='sellermania'}</option>
                                            <option value="AUTO" {if $sm_marketplace.value eq 'AUTO'}selected{/if}>{l s='Import the orders and auto confirm them' mod='sellermania'}</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3">
                                        {if isset($sm_marketplace.delivery)}
                                            <select name="{$sm_marketplace.key}_DELIVERY" id="{$sm_marketplace.key}_DELIVERY" style="width:100%">
                                                {foreach from=$sm_marketplace.delivery item=sm_marketplace_delivery}
                                                    <option value="{$sm_marketplace_delivery}" {if $sm_marketplace.delivery_value == $sm_marketplace_delivery}selected{/if}>{$sm_marketplace_delivery}</option>
                                                {/foreach}
                                            </select>
                                        {else}
                                            <input name="{$sm_marketplace.key}_DELIVERY" id="{$sm_marketplace.key}_DELIVERY" value="{$sm_marketplace.delivery_value}" style="width:100%;margin-top:3px">
                                        {/if}
                                    </div>
                                    <div class="col-lg-3">
                                        <input name="{$sm_marketplace.key}_SERVICE" id="{$sm_marketplace.key}_SERVICE" value="{$sm_marketplace.service_value}" style="width:100%;margin-top:3px">
                                    </div>
                                    <br clear="left">
                                {/foreach}
                            </div>
                        </div>

                        <br>
                        <h2>3. {l s='Options' mod='sellermania'}</h2>
                        <hr />


                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Synchronization by reference (optional)' mod='sellermania'}</label>
                            <div class="col-lg-4">
                                <input type="radio" name="sm_stock_sync_option" id="sm_stock_sync_option_yes" value="yes" {if $sm_stock_sync_option eq 'yes'}checked="checked"{/if} />
                                <label for="sm_stock_sync_option_yes">{l s='Yes' mod='sellermania'}</label>&nbsp;&nbsp;
                                <input type="radio" name="sm_stock_sync_option" id="sm_stock_sync_option_no" value="no" {if $sm_stock_sync_option eq 'no' || $sm_stock_sync_option eq ''}checked="checked"{/if} />
                                <label for="sm_stock_sync_option_no">{l s='No' mod='sellermania'}</label>
                            </div>
                        </div>
                        <div class="form-group clearfix" id="sm_stock_sync_option_configuration">

                            <label class="col-lg-4" style="text-align:right"><b>{l s='Option 1' mod='sellermania'}</b></label>
                            <div class="col-lg-8">
                                <input type="radio" name="sm_stock_sync_option_1" id="sm_stock_sync_option_1_yes" value="yes" {if $sm_stock_sync_option_1 eq 'yes'}checked="checked"{/if} />
                                <label for="sm_stock_sync_option_1_yes">{l s='Yes' mod='sellermania'}</label>&nbsp;&nbsp;
                                <input type="radio" name="sm_stock_sync_option_1" id="sm_stock_sync_option_1_no" value="no" {if $sm_stock_sync_option_1 eq 'no' || $sm_stock_sync_option_1 eq ''}checked="checked"{/if} />
                                <label for="sm_stock_sync_option_1_no">{l s='No' mod='sellermania'}</label>
                                <p align="left">
                                    {l s='This feature allows you to "chain" some references to each other, which will permit to update the stock of all the chained references at the same time.' mod='sellermania'}<br>
                                    {l s='Eg: You choose to chain with the 5 first characters of the SKU. If the product\'s SKU of an imported order is "D6HSIQKZJD", all products with SKU begins with "D6HSI" will have their stock updated.' mod='sellermania'}
                                </p>
                            </div>

                            <label class="col-lg-4" style="text-align:right"><b>{l s='Option 2' mod='sellermania'}</b></label>
                            <div class="col-lg-8">
                                <input type="radio" name="sm_stock_sync_option_2" id="sm_stock_sync_option_2_yes" value="yes" {if $sm_stock_sync_option_2 eq 'yes'}checked="checked"{/if} />
                                <label for="sm_stock_sync_option_2_yes">{l s='Yes' mod='sellermania'}</label>&nbsp;&nbsp;
                                <input type="radio" name="sm_stock_sync_option_2" id="sm_stock_sync_option_2_no" value="no" {if $sm_stock_sync_option_2 eq 'no' || $sm_stock_sync_option_2 eq ''}checked="checked"{/if} />
                                <label for="sm_stock_sync_option_2_no">{l s='No' mod='sellermania'}</label>
                                <p align="left">
                                    {l s='This feature allows you to match the reference of product\'s SKU of an imported order with one of your PrestaShop.' mod='sellermania'}<br>
                                    {l s='Eg: You choose to chain with the 5 first characters of the SKU. If the product\'s SKU of an imported order is "D6HSIQKZJD", PrestaShop will associated the product with the first one which SKU begins with "D6HSI".' mod='sellermania'}
                                </p>
                            </div>
                            <br>
                            <label class="col-lg-4" style="text-align:right"><b>{l s='Configuration' mod='sellermania'}</b></label>
                            <p>&nbsp;&nbsp;<b>{l s='Define how you want to chain the references, the X first or X last characters of the reference.' mod='sellermania'}</b></p>
                            <label class="col-lg-4">&nbsp;</label>
                            <div class="col-lg-1">
                                {l s='Use the' mod='sellermania'}
                            </div>
                            <div class="col-lg-2"><input type="text" name="sm_stock_sync_nb_char" value="{$sm_stock_sync_nb_char|intval}" /></div>
                            <div class="col-lg-2">
                                <input type="radio" name="sm_stock_sync_position" id="sm_stock_sync_position_first" value="first" {if $sm_stock_sync_position eq 'first'}checked="checked"{/if} />
                                <label for="sm_stock_sync_position_first">{l s='first' mod='sellermania'}</label>&nbsp;&nbsp;
                                <input type="radio" name="sm_stock_sync_position" id="sm_stock_sync_position_last" value="last" {if $sm_stock_sync_position eq 'last' || $sm_stock_sync_position eq ''}checked="checked"{/if} />
                                <label for="sm_stock_sync_position_last">{l s='last' mod='sellermania'}</label>
                            </div>
                            <div class="col-lg-2">
                                {l s='reference\'s characters.' mod='sellermania'}
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Product match' mod='sellermania'}</label>
                            <div class="col-lg-8">
                                <select name="sm_product_match">
                                    <option value="automatic" {if $sm_product_match == 'automatic'}selected{/if}>Automatic</option>
                                    <option value="by_reference_only" {if $sm_product_match == 'by_reference_only'}selected{/if}>By reference only</option>
                                    <option value="by_id_product_only" {if $sm_product_match == 'by_id_product_only'}selected{/if}>By PrestaShop IDs only</option>
                                    <option value="by_upc_only" {if $sm_product_match == 'by_upc_only'}selected{/if}>By UPC only</option>
                                    <option value="by_ean13_only" {if $sm_product_match == 'by_ean13_only'}selected{/if}>By EAN13 only</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Orders imported on shop (do not change if you\'re not sure)' mod='sellermania'}</label>
                            <div class="col-lg-8">
                                <select name="sm_import_orders_shop">
                                    <option value="all">{l s='All' mod='sellermania'}</option>
                                    {foreach from=$shops item=shop}
                                        <option value="{$shop.id_shop}" {if $sm_import_orders_shop eq $shop.id_shop}selected{/if}> {$shop.name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Alert me by e-mail when a reference is not found' mod='sellermania'}</label>
                            <div class="col-lg-4">
                                <input type="radio" name="sm_alert_missing_ref_option" id="sm_alert_missing_ref_option_yes" value="yes" {if $sm_alert_missing_ref_option eq 'yes'}checked="checked"{/if} />
                                <label for="sm_alert_missing_ref_option_yes">{l s='Yes' mod='sellermania'}</label>&nbsp;&nbsp;
                                <input type="radio" name="sm_alert_missing_ref_option" id="sm_alert_missing_ref_option_no" value="no" {if $sm_alert_missing_ref_option eq 'no' || $sm_alert_missing_ref_option eq ''}checked="checked"{/if} />
                                <label for="sm_alert_missing_ref_option_no">{l s='No' mod='sellermania'}</label>
                            </div>
                        </div>
                        <div class="form-group clearfix" id="sm_alert_missing_ref_option_configuration">
                            <label class="col-lg-4">{l s='Your email' mod='sellermania'}</label>
                            <div class="col-lg-2">
                                <input type="text" name="sm_alert_missing_ref_mail" value="{$sm_alert_missing_ref_mail}" />
                            </div>
                        </div>


                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Enable native refund system' mod='sellermania'}</label>
                            <div class="col-lg-4">
                                <input type="radio" name="sm_enable_native_refund_system" id="sm_enable_native_refund_system_yes" value="yes" {if $sm_enable_native_refund_system eq 'yes'}checked="checked"{/if} />
                                <label for="sm_enable_native_refund_system_yes">{l s='Yes' mod='sellermania'}</label>&nbsp;&nbsp;
                                <input type="radio" name="sm_enable_native_refund_system" id="sm_enable_native_refund_system_no" value="no" {if $sm_enable_native_refund_system eq 'no' || $sm_enable_native_refund_system eq ''}checked="checked"{/if} />
                                <label for="sm_enable_native_refund_system_no">{l s='No' mod='sellermania'}</label>
                                <p><b><u>{l s='BEWARE:' mod='sellermania'}</u></b> {l s='Some of the native order actions can send e-mails to customers. You can be blacklist by marketplace if you do not proceed carefully.' mod='sellermania'}</p>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Enable native order interface' mod='sellermania'}</label>
                            <div class="col-lg-4">
                                <input type="radio" name="sm_enable_native_order_interface" id="sm_enable_native_order_interface_yes" value="yes" {if $sm_enable_native_order_interface eq 'yes'}checked="checked"{/if} />
                                <label for="sm_enable_native_order_interface_yes">{l s='Yes' mod='sellermania'}</label>&nbsp;&nbsp;
                                <input type="radio" name="sm_enable_native_order_interface" id="sm_enable_native_order_interface_no" value="no" {if $sm_enable_native_order_interface eq 'no' || $sm_enable_native_order_interface eq ''}checked="checked"{/if} />
                                <label for="sm_enable_native_order_interface_no">{l s='No' mod='sellermania'}</label>
                                <p><b><u>{l s='BEWARE:' mod='sellermania'}</u></b> {l s='Some of the native order actions can send e-mails to customers. You can be blacklist by marketplace if you do not proceed carefully.' mod='sellermania'}</p>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='E-mail associated to customer account created' mod='sellermania'}</label>
                            <div class="col-lg-8">
                                <input type="text" name="sm_catch_all_mail_address" value="{$sm_catch_all_mail_address}" />
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Starting date for orders importation' mod='sellermania'}</label>
                            <div class="col-lg-8">
                                <input type="text" name="sm_install_date" value="{$sm_install_date}" />
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Import orders with client e-mail' mod='sellermania'}</label>
                            <div class="col-lg-8">
                                <input type="checkbox" id="sm_import_orders_with_client_email" name="sm_import_orders_with_client_email" {if $sm_import_orders_with_client_email == 'on'}checked{/if} />
                            </div>
                        </div>
                        <script>
                            var sm_import_orders_with_client_email_label = "{l s='Are you sure to enable this feature ? This can cause marketplace blacklist. Please contact Sellermania support if you\'re not sure.' mod='sellermania'}";
                        </script>

                        <br>
                        <h2>4. {l s='Default customer group, carrier and order states' mod='sellermania'}</h2>
                        <hr />

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Default customer group for order importation' mod='sellermania'}</label>
                            <div class="col-lg-4">
                                {foreach from=$customer_groups item=customer_group}
                                    <input type="radio" value="{$customer_group.id_group}" name="sm_import_default_customer_group" {if $sm_import_default_customer_group eq $customer_group.id_group}checked{/if} /> {$customer_group.name}<br>
                                {/foreach}
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Default carrier for order importation' mod='sellermania'}</label>
                            <div class="col-lg-4">
                                {foreach from=$carriers item=carrier}
                                    <input type="radio" value="{$carrier.id_carrier}" name="sm_import_default_carrier" {if $sm_import_default_carrier eq $carrier.id_carrier}checked{/if} /> {$carrier.name}<br>
                                {/foreach}
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Default shipping service for order importation' mod='sellermania'}</label>
                            <div class="col-lg-4">
                                <input type="text" value="{$sm_import_default_shipping_service}" name="sm_import_default_shipping_service" />
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Default country code for VAT (import origin)' mod='sellermania'}</label>
                            <div class="col-lg-4">
                                <input type="text" value="{$sm_import_default_country_code}" name="sm_import_default_country_code" />
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Default country code for VAT (shipment origin)' mod='sellermania'}</label>
                            <div class="col-lg-4">
                                <input type="text" value="{$sm_shipment_default_country_code}" name="sm_shipment_default_country_code" />
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-lg-4">{l s='Sellermania order state configuration' mod='sellermania'}</label>
                            <div class="col-lg-8">

                                <div class="col-lg-4"><b><u>{l s='Sellermania order state' mod='sellermania'}</u></b></div>
                                <div class="col-lg-8"><b><u>{l s='PrestaShop order state' mod='sellermania'}</u></b></div>
                                <br clear="left"><br>

                                {foreach from=$sm_order_states key=sm_order_state_key item=sm_order_state}
                                    <label class="col-lg-4">{$sm_order_state.label[$documentation_iso_code]}</label>
                                    <div class="col-lg-8">
                                        <select name="{$sm_order_state_key}" id="{$sm_order_state_key}" style="width:100%">
                                        {foreach from=$ps_order_states item=ps_order_state}
                                            <option value="{$ps_order_state.id_order_state}" {if $ps_order_state.id_order_state eq $sm_order_state.ps_conf_value}selected{/if}>{$ps_order_state.name}</option>
                                        {/foreach}
                                        </select>
                                    </div><br clear="left">
                                {/foreach}
                            </div>
                        </div>


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
                    </div>
                {/if}
                {if $nb_orders_in_error gt 0}
                    <br>
                    <h4>{l s='Importation errors:' mod='sellermania'}</h4>
                    <p><b>{$nb_orders_in_error}</b> {l s='orders could not be imported' mod='sellermania'} - <a href="{$module_url}&see=orders-error">{l s='See details' mod='sellermania'}</a></p>
                {/if}
            {/if}
        </fieldset>
    </form>
</div>
