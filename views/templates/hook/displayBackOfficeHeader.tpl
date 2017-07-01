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

<script>
    var nb_sellermania_orders_in_error = {$nb_orders_in_error|intval};
    var txt_sellermania_orders_in_error = "{l s='Sellermania orders could not be imported. Look at the module configuration for more details.' mod='sellermania'}";
    var sellermania_invoice_url = '{$sellermania_invoice_url}';

    var sellermania_admin_orders_url = '{$sellermania_admin_orders_url}';
    var sellermania_default_carrier = '{$sellermania_default_carrier}';
    var txt_sellermania_confirm_orders = "{l s='Confirm selected Sellermania orders' mod='sellermania'}";
    var txt_sellermania_send_orders = "{l s='Set selected Sellermania orders as sent' mod='sellermania'}";
    var txt_sellermania_select_at_least_one_order = "{l s='You have to select at least one order' mod='sellermania'}";
    var txt_sellermania_error_occured = "{l s='An error occured' mod='sellermania'}";
    var txt_sellermania_carrier_selection = "{l s='Which carrier do you want to use?' mod='sellermania'}";
    var txt_sellermania_orders_updated = "{l s='Orders were successfully updated' mod='sellermania'}";
    var txt_sellermania_select_all = "{l s='Select all orders' mod='sellermania'}";
    var txt_sellermania_unselect_all = "{l s='Unselect all orders' mod='sellermania'}";
    var txt_sellermania_timeout_exception = "{l s='Sellermania rejected the request (too many requests has been made), please wait a few seconds and try again.' mod='sellermania'}";
</script>
<script type="text/javascript" src="{$sellermania_module_path}views/js/displayBackOfficeHeader.js"></script>
<script type="text/javascript" src="{$sellermania_module_path}views/js/displayBackOfficeHeader-{$ps_version}.js"></script>
