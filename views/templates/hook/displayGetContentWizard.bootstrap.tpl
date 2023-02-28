<form action="" method="POST">
    <div id="sm-wizard-login" data-module-web-path="{$module_web_path}" data-lang="{$lang_iso}" data-key="{$sm_secret_key}">
        <div class="mt-3">
            <div class="form-group">
                <p><strong>{l s="Connect your Sellermania account" mod='sellermania'}</strong></p>
                <p>
                    {l s='Please provide the requested information below. Your e-mail and token are accessible under "Your account / Sellermania Account Information" in Sellermania platform.' mod='sellermania'}<br>
                    {l s="If you can't find them, please contact Sellermania's Support" mod='sellermania'}
                </p>
            </div>
            <div class="form-group clearfix">
                <div class="col-lg-4">
                    <label class="required">{l s='Sellermania e-mail' mod='sellermania'}</label>
                </div>
                <div class="col-lg-8">
                    <input class="api-connection-field" type="text" name="sm_order_email" value="{$sm_order_email}" />
                </div>
            </div>
            <div class="form-group clearfix">
                <div class="col-lg-4">
                    <label class="required">{l s='Token webservices' mod='sellermania'}</label>
                </div>
                <div class="col-lg-8">
                    <input class="api-connection-field" type="text" name="sm_order_token" value="{$sm_order_token}" />
                </div>
            </div>
            <div class="form-group clearfix">
                <div class="col-lg-4">
                    <label class="required">{l s='Order endpoint' mod='sellermania'}</label>
                </div>
                <div class="col-lg-8">
                    <input class="api-connection-field" type="text" name="sm_order_endpoint" value="{$sm_order_endpoint}" />
                </div>
            </div>
            <div class="form-group clearfix">
                <div class="col-lg-4">
                    <label class="required">{l s='Confirm order endpoint' mod='sellermania'}</label>
                </div>
                <div class="col-lg-8">
                    <input class="api-connection-field" type="text" name="sm_confirm_order_endpoint" value="{$sm_confirm_order_endpoint}" />
                </div>
            </div>

            <div class="form-group clearix">
                <div class="row">
                    <div class="col-lg-4"></div>
                    <div class="col-lg-8">
                        <div class="btn-connectivity-wrapper">
                            <div class="text-right">
                                <a href="#" id="btn-test-api" class="btn btn-primary" style="margin-bottom: 7px;">{l s="Test API connection" mod='sellermania'}</a>
                            </div>
                        </div>
                        <div id="test-api-connectivity-result"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="sellermania-module-wizard" style="display: none;">
        <div id="multi-step-form-container">
            <!-- Form Steps / Progress Bar -->
            <ul class="form-stepper form-stepper-horizontal text-center mx-auto pl-0">
                <!-- Step 1 -->
                <li class="form-stepper-active text-center form-stepper-list" step="1">
                    <a class="mx-2">
                        <span class="form-stepper-circle">
                            <span>1</span>
                        </span>
                        <div class="label">{l s='General Settings' mod='sellermania'}</div>
                    </a>
                </li>
                <!-- Step 2 -->
                <li class="form-stepper-unfinished text-center form-stepper-list" step="2">
                    <a class="mx-2">
                        <span class="form-stepper-circle text-muted">
                            <span>2</span>
                        </span>
                        <div class="label text-muted">{l s='Marketplaces Configuration' mod='sellermania'}</div>
                    </a>
                </li>
                <!-- Step 3 -->
                <li class="form-stepper-unfinished text-center form-stepper-list" step="3">
                    <a class="mx-2">
                        <span class="form-stepper-circle text-muted">
                            <span>3</span>
                        </span>
                        <div class="label text-muted">{l s='Carriers Configuration' mod='sellermania'}</div>
                    </a>
                </li>
                <!-- Step 4 -->
                <li class="form-stepper-unfinished text-center form-stepper-list" step="4">
                    <a class="mx-2">
                        <span class="form-stepper-circle text-muted">
                            <span>4</span>
                        </span>
                        <div class="label text-muted">{l s='Feed export configuration' mod='sellermania'}</div>
                    </a>
                </li>
                <!-- Step 5 -->
                <li class="form-stepper-unfinished text-center form-stepper-list" step="5">
                    <a class="mx-2">
                        <span class="form-stepper-circle text-muted">
                            <span>5</span>
                        </span>
                        <div class="label text-muted">{l s='Status Configuration' mod='sellermania'}</div>
                    </a>
                </li>
            </ul>

            <!-- Step Wise Form Content -->
            <!-- Step 1 Content -->
            <section id="step-1" class="form-step fadeInLeft">
                <h2 class="font-normal">{l s='General Settings' mod='sellermania'}</h2>
                <p>{l s='Connect your Prestashop to your Sellermania account by providing the requested credentials below' mod='sellermania'}</p>
                <!-- Step 1 input fields -->
                <div class="mt-3">

                    <div class="form-group clearfix">
                        <div class="col-lg-4">
                            <label>{l s='Import order of last X days' mod='sellermania'}</label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" name="sm_order_import_past_days" value="{$sm_order_import_past_days}" />
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <div class="col-lg-4">
                            <label>{l s='Import X orders during an importation request' mod='sellermania'}</label>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" name="sm_order_import_limit" value="{$sm_order_import_limit}" />
                        </div>
                    </div>


                    <div class="form-group clearfix">
                        <div class="col-lg-4">
                            <label>{l s='Importation method' mod='sellermania'}</label>
                        </div>
                        <div class="col-lg-8">
                            <input type="radio" name="sm_import_method" id="sm_import_method_automatic" value="automatic" {if $sm_import_method eq 'automatic' || $sm_import_method eq ''}checked="checked"{/if} />
                            <label for="sm_import_method_automatic">{l s='Automatic' mod='sellermania'}</label>
                            <span style="display: inline-block;width: 5px;"></span>
                            <input type="radio" name="sm_import_method" id="sm_import_method_cron" value="cron" {if $sm_import_method eq 'cron'}checked="checked"{/if} />
                            <label for="sm_import_method_cron">{l s='Cron' mod='sellermania'}</label>&nbsp;&nbsp;

                            <p>
                                <b><u>{l s='Note:' mod='sellermania'}</u></b><br>
                            </p>
                            <ul>
                                <li>{l s='Automatic importation is easier to configure (you have nothing to do), but it can cause some issues (slow down back office during importation, import some orders in double if two people are working on the back office, or stock issues if nobody is using the back office during the day).' mod='sellermania'}</li>
                                <li>{l s='So, if you have the possibility, use cron importation (just contact your hosting provider, he should be able to help you).' mod='sellermania'}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group clearfix" id="sm_import_method_cron_configuration">
                        <div class="col-lg-4">
                            <label>{l s='Cron script to call' mod='sellermania'}</label>
                        </div>
                        <div class="col-lg-8">
                            <u>{l s='Via command line (classic cron)' mod='sellermania'} :</u> <code>php -f {$script_path}/import.php</code><br><br>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="button btn-navigate-form-step" type="button" step_number="2">{l s="Next" mod='sellermania'}</button>
                </div>
            </section>
            <!-- Step 2 Content, default hidden on page load. -->
            <section id="step-2" class="form-step d-none fadeInLeft">
                <h2 class="font-normal">{l s='Marketplaces Configuration' mod='sellermania'}</h2>
                <p>{l s='Choose the orders importation mode for each marketplace available in your Sellermania subscription' mod='sellermania'}</p>
                <!-- Step 2 input fields -->
                <div class="mt-3">
                    <div class="form-group clearfix">
                        <label class="col-lg-4">{l s='Sellermania marketplaces configuration' mod='sellermania'}</label>
                        <div class="col-lg-8 form-group clearfix">
                            <div id="wz-marketplaces-list"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="button btn-navigate-form-step" type="button" step_number="1">{l s="Prev" mod='sellermania'}</button>
                    <button class="button btn-navigate-form-step" type="button" step_number="3">{l s="Next" mod='sellermania'}</button>
                </div>
            </section>
            <!-- Step 3 Content, default hidden on page load. -->
            <section id="step-3" class="form-step d-none fadeInLeft">
                <h2 class="font-normal">{l s='Carriers Matching' mod='sellermania'}</h2>
                <p>{l s='Match between your Prestashop\'s carriers and the carriers of each marketplace' mod='sellermania'}</p>
                <!-- Step 3 input fields -->
                <div class="mt-3">
                    <div class="form-group clearfix">
                        <label class="col-lg-3">{l s='Sellermania marketplaces configuration' mod='sellermania'}</label>
                        <div class="col-lg-9 form-group clearfix">
                            <div id="wz-carriers-for-marketplaces-list"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="button btn-navigate-form-step" type="button" step_number="2">{l s="Prev" mod='sellermania'}</button>
                    <button class="button btn-navigate-form-step" type="button" step_number="4">{l s="Next" mod='sellermania'}</button>
                </div>
            </section>

            <!-- Step 4 Content, default hidden on page load. -->
            <section id="step-4" class="form-step d-none fadeInLeft">
                <h2 class="font-normal">{l s='Feed export configuration' mod='sellermania'}</h2>
                <p>{l s='Configure the way you want to export your catalog feed' mod='sellermania'}</p>
                <!-- Step 5 input fields -->
                <div class="mt-3">
                    <label class="col-lg-4">{l s='Products to include in the catalog feed' mod='sellermania'}</label>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <input type="radio" id="sm_product_to_include_in_feed_all" name="sm_product_to_include_in_feed" value="all"{if $sm_product_to_include_in_feed eq "all" or $sm_product_to_include_in_feed eq ''} checked{/if}>
                            <label for="sm_product_to_include_in_feed_all">{l s="Include all products in stock + out of stock" mod='sellermania'}</label>
                        </div>
                        <div class="form-group">
                            <input type="radio" id="sm_product_to_include_in_feed_without_oos" name="sm_product_to_include_in_feed" value="without_oos"{if $sm_product_to_include_in_feed eq "without_oos"} checked{/if}>
                            <label for="sm_product_to_include_in_feed_without_oos">{l s="Include all products in stock + products that have been out of stock in the last X days" mod='sellermania'}</label>
                            <input type="text" id="sm_last_days_to_include_in_feed" name="sm_last_days_to_include_in_feed" style="display: inline-block; width: 100px;"{if $sm_last_days_to_include_in_feed neq "" and $sm_product_to_include_in_feed eq "without_oos"} value="{$sm_last_days_to_include_in_feed}"{/if}>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="button btn-navigate-form-step" type="button" step_number="3">{l s="Prev" mod='sellermania'}</button>
                    <button class="button btn-navigate-form-step" type="button" step_number="5">{l s="Next" mod='sellermania'}</button>
                </div>
            </section>

            <!-- Step 5 Content, default hidden on page load. -->
            <section id="step-5" class="form-step d-none fadeInLeft">
                <h2 class="font-normal">{l s='Status Configuration' mod='sellermania'}</h2>
                {l s='Match between your Prestashop\'s order status and the order status you need to send to Sellermania to synchronize with the marketplaces' mod='sellermania'}
                <!-- Step 5 input fields -->
                <div class="mt-3">
                    <div class="form-group clearfix">
                        <div class="col-lg-4">
                            <label>{l s='Sellermania order state configuration' mod='sellermania'}</label>
                            <div class="alert alert-info" style="margin-right: 20px;">
                                <h4 class="os-alert-title">{l s="Mandatory order status to match" mod='sellermania'}</h4>
                                <ul>
                                    {foreach from=$sm_order_states key=sm_order_state_key item=sm_order_state}
                                        {if !isset($sm_order_state.is_marketplace_specific) or $sm_order_state.is_marketplace_specific eq false}
                                            <li>
                                                {$sm_order_state.label[$documentation_iso_code]}
                                                <span class="sm-order-description">
                                                    <i class="sm-icon sm-info"></i>
                                                    <span>{$sm_order_state.definition[$documentation_iso_code]}</span>
                                                </span>
                                            </li>
                                        {/if}
                                    {/foreach}
                                </ul>
                                <h4 class="os-alert-title">{l s="Marketplace specific order status" mod='sellermania'}</h4>
                                <ul>
                                    {foreach from=$sm_order_states key=sm_order_state_key item=sm_order_state}
                                        {if isset($sm_order_state.is_marketplace_specific) and $sm_order_state.is_marketplace_specific eq true}
                                            <li>
                                                {$sm_order_state.label[$documentation_iso_code]}
                                                <span class="sm-order-description">
                                                    <i class="sm-icon sm-info"></i>
                                                    <span>{$sm_order_state.definition[$documentation_iso_code]}</span>
                                                </span>
                                            </li>
                                        {/if}
                                    {/foreach}
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <label>{l s="Order states matching" mod='sellermania'}</label>
                            <div id="custom-status-creation-wrapper">
                                <p class="font-weight-bold">
                                    {l s='You can choose form the dropdown lists the Sellermania\'s order state you want to match to each Prestasop\'s order state. Else, clicking on the button below will create and match specific Sellermania order states if you don\'t know which status you should match.' mod='sellermania'}
                                </p>
                                <p style="margin-top: 10px;">
                                    <a href="#" id="create-custom-status" class="btn btn-default">{l s="Create specific Sellermania order status" mod='sellermania'}</a>
                                </p>
                            </div>
                            <table class="table" id="order_states_table">
                                <tr>
                                    <th><u>{l s='PrestaShop order state' mod='sellermania'}</u></th>
                                    <th></th>
                                    <th><u>{l s='Sellermania order state' mod='sellermania'}</u></th>
                                </tr>
                                {foreach from=$ps_order_states item=ps_order_state}
                                    <tr>
                                        <td><label>{$ps_order_state.name}</label></td>
                                        <td>=</td>
                                        <td>
                                            <select name="SM_PS_ORDER_MAP_{$ps_order_state.id_order_state}" id="SM_PS_ORDER_MAP_{$ps_order_state.id_order_state}">
                                                <option value=""></option>
                                                {foreach from=$sm_order_states key=sm_order_state_key item=sm_order_state}
                                                    {assign var=selected value=''}
                                                    {if isset($sm_status_mapping[$ps_order_state.id_order_state])}
                                                        {foreach from=$sm_status_mapping[$ps_order_state.id_order_state] item=status_map}
                                                            {if $status_map eq $sm_order_state.sm_status}
                                                                {assign var=selected value='selected'}
                                                            {/if}
                                                        {/foreach}
                                                    {/if}
                                                    <option {$selected} value="{$sm_order_state.sm_status}">{$sm_order_state.label[$documentation_iso_code]}</option>
                                                {/foreach}
                                            </select>
                                        </td>
                                    </tr>
                                {/foreach}
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="button btn-navigate-form-step" type="button" step_number="4">{l s="Prev" mod='sellermania'}</button>
                    <button class="button submit-btn" name="wizard_button" type="submit">{l s="Save" mod='sellermania'}</button>
                </div>
            </section>
        </div>
    </div>
</form>