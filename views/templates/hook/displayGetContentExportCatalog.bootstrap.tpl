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


<div class="panel-heading">
    <legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />&nbsp;{l s='Export catalog' mod='sellermania'}</legend>
</div>
<div class="margin-form">
    <form action="" method="post">
        <fieldset>
            <div class="form-group">
                <div class="clearfix">
                    <label class="col-lg-4">{l s='Do you want to export all your catalog to Sellermania?' mod='sellermania'}</label>
                    <div class="col-lg-8">
                        <input type="radio" name="sm_export_all" id="sm_export_all_yes" value="yes" {if $sm_export_all eq 'yes'}checked="checked"{/if} />
                        <label for="sm_export_all_yes">{l s='Yes' mod='sellermania'}</label>&nbsp;&nbsp;
                        <input type="radio" name="sm_export_all" id="sm_export_all_no" value="no" {if $sm_export_all eq 'no' || $sm_export_all eq ''}checked="checked"{/if} />
                        <label for="sm_export_all_no">{l s='No' mod='sellermania'}</label>
                    </div>
                </div>
                <div id="sm_export_all_configuration" class="clearfix">
                    <div class="form-group clearfix">
                        <label class="col-lg-4">{l s='Please select the categories you want to export:' mod='sellermania'}</label>
                        <div class="col-lg-8">{$category_tree}</div>
                    </div>
                </div>
            </div>

            <p><b>{l s='Send these links to Sellermania' mod='sellermania'}</b></p>
            <p>
                {foreach from=$languages_list item=language}
                    <strong>{$language.iso_code|strtoupper} :</strong> {$module_web_path}export.php?l={$language.iso_code|strtolower}&k={$sellermania_key} <br>
                {/foreach}
            </p>

            <br>
            <p><a href="#" id="see-advanced-export" class="btn btn-default">{l s='Advanced configuration' mod='sellermania'}</a></p>

            <div id="advanced-export">
                <p><b>{l s='Set a cron task' mod='sellermania'}</b></p>
                <p>{l s='Script path:' mod='sellermania'} {$script_path}/export.php {$sellermania_key}</p>
                <p>{l s='Generated files will be available at these urls:' mod='sellermania'}</p>
                <p>
                    {foreach from=$files_list item=file key=iso_code}
                        <strong>{$iso_code|strtoupper} :</strong> {if isset($file.generated)}{$file.file} ({l s='Generated on' mod='sellermania'} {$file.generated}){else}{l s='Not generated yet' mod='sellermania'}{/if}  <br>
                    {/foreach}
                </p>
                {if $export_directory_writable ne 1}
                    <div class="alert alert-danger"><p class="error"><strong>{l s='Beware, the following directory is not writable:' mod='sellermania'} {$script_path}/export/</strong></p></div>
                {/if}

                <div class="form-group clearfix">
                    <label class="col-lg-4">{l s='Enable export combination name' mod='sellermania'}</label>
                    <div class="col-lg-4">
                        <input type="radio" name="sm_enable_export_comb_name" id="sm_enable_export_comb_name_yes" value="yes" {if $sm_enable_export_comb_name eq 'yes'}checked="checked"{/if} />
                        <label for="sm_enable_export_comb_name_yes">{l s='Yes' mod='sellermania'}</label>&nbsp;&nbsp;
                        <input type="radio" name="sm_enable_export_comb_name" id="sm_enable_export_comb_name_no" value="no" {if $sm_enable_export_comb_name eq 'no' || $sm_enable_export_comb_name eq ''}checked="checked"{/if} />
                        <label for="sm_enable_export_comb_name_no">{l s='No' mod='sellermania'}</label>
                    </div><br clear="left" />
                    <p>
                        {l s='Natively this module will concatenate the attribute names with the product name in case of combination.' mod='sellermania'}<br>
                        {l s='You can disable this feature for merchants who create only one combination per product.' mod='sellermania'}
                    </p>
                </div>

            </div>



            <div class="panel-footer">
                <input type="submit" name="export_configuration" value="{l s='Validate' mod='sellermania'}" class="btn btn-default pull-right" />
            </div>
            {if isset($sm_confirm_export_options)}<div class="alert alert-success"><p class="conf"><strong>{l s='Configuration has been saved' mod='sellermania'}</strong></p></div>{/if}
        </fieldset>
    </form>
</div>