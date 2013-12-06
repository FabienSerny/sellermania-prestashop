<h2>{l s='SellerMania' mod='sellermania'}</h2>

<form action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" method="post">
    <fieldset>
        <legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />{l s='SellerMania configuration' mod='sellermania'}</legend>
        <div class="margin-form" style="padding-left:15px">
            <p><b>{l s='Do you want to import Sellermania orders in PrestaShop?' mod='sellermania'}</b></p><br>
            <input type="radio" name="import_orders" id="import_orders_yes" value="yes" {if $sm_import_orders eq 'yes'}checked="checked"{/if} /> {l s='Yes' mod='sellermania'}
            <input type="radio" name="import_orders" id="import_orders_no" value="no" {if $sm_import_orders eq 'no' || $sm_import_orders eq ''}checked="checked"{/if} /> {l s='No' mod='sellermania'}
        </div>
        <div class="margin-form" style="padding-left:15px" id="import_orders_credentials">
            <p><b>{l s='Please fill up with the informations Sellermania provide you:' mod='sellermania'}</b></p>
            <p><label>{l s='Order e-mail' mod='sellermania'}</label> <input type="text" name="order_email" value="{$sm_order_email}" /></p>
            <p><label>{l s='Order token' mod='sellermania'}</label> <input type="text" name="order_token" value="{$sm_order_token}" /></p>
            <p><label>{l s='Order endpoint' mod='sellermania'}</label> <input type="text" name="order_endpoint" value="{$sm_order_endpoint}" /></p>
            <p><label><input type="submit" name="import_orders" value="{l s='Validate' mod='sellermania'}" /></label></p>
        </div>
    </fieldset>
</form>

<br />

<fieldset>
    <legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />{l s='SellerMania export' mod='sellermania'}</legend>
    <div class="margin-form" style="padding-left:15px">
        <p>{l s='You have two solutions to send your catalog to SellerMania' mod='sellermania'}</p><br>
        <p><b>1) {l s='Set a cron task' mod='sellermania'}</b></p>
        <p>{l s='Script path:' mod='sellermania'} {$script_path}/export.php {$sellermania_key}</p>
        <p>{l s='Generated files will be available at these urls:' mod='sellermania'}</p>
        <p>
        {foreach from=$files_list item=file key=iso_code}
            <strong>{$iso_code|strtoupper} :</strong> {$file.file} ({if isset($file.generated)}{l s='Generated on' mod='sellermania'} {$file.generated}{else}{l s='Not generated yet' mod='sellermania'}{/if})  <br>
        {/foreach}
        </p>
        {if $export_directory_writable ne 1}<p class="error"><strong>{l s='Beware, the following directory is not writable:' mod='sellermania'} {$script_path}/export/</strong></p>{/if}

        <br><p><b><u>{l s='OR' mod='sellermania'}</u></b></p><br>

        <p><b>2) {l s='Send these links to SellerMania' mod='sellermania'}</b></p>
        <p>
        {foreach from=$languages_list item=language}
            <strong>{$language.iso_code|strtoupper} :</strong> {$module_web_path}export.php?l={$language.iso_code|strtolower}&k={$sellermania_key} <br>
        {/foreach}
        </p>

    </div>
</fieldset>

<script type="text/javascript" src="{$sellermania_module_path}views/js/displayGetContent.js"></script>
