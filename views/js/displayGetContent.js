/*
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
 */

$(document).ready(function() {


    function displaySellermaniaCredentials()
    {
        if ($('#sm_import_orders_yes').attr('checked') == 'checked' || $('#sm_import_orders_yes').attr('checked') == true)
            $('#sm_import_orders_credentials').show();
        else
            $('#sm_import_orders_credentials').hide();
        return true;
    }

    $('#sm_import_orders_yes').click(function() { return displaySellermaniaCredentials(); });
    $('#sm_import_orders_no').click(function() { return displaySellermaniaCredentials(); });
    displaySellermaniaCredentials();

    function displaySellermaniaSyncOption()
    {
        if ($('#sm_stock_sync_option_yes').attr('checked') == 'checked' || $('#sm_stock_sync_option_yes').attr('checked') == true)
            $('#sm_stock_sync_option_configuration').show();
        else
            $('#sm_stock_sync_option_configuration').hide();
        return true;
    }

    $('#sm_stock_sync_option_yes').click(function() { return displaySellermaniaSyncOption(); });
    $('#sm_stock_sync_option_no').click(function() { return displaySellermaniaSyncOption(); });
    displaySellermaniaSyncOption();


    function displaySellermaniaImportConfiguration()
    {
        if ($('#sm_import_method_cron').attr('checked') == 'checked' || $('#sm_import_method_cron').attr('checked') == true)
            $('#sm_import_method_cron_configuration').show();
        else
            $('#sm_import_method_cron_configuration').hide();
        return true;
    }

    $('#sm_import_method_cron').click(function() { return displaySellermaniaImportConfiguration(); });
    $('#sm_import_method_automatic').click(function() { return displaySellermaniaImportConfiguration(); });
    displaySellermaniaImportConfiguration();




    function displaySellermaniaAlertOption()
    {
        if ($('#sm_alert_missing_ref_option_yes').attr('checked') == 'checked' || $('#sm_alert_missing_ref_option_yes').attr('checked') == true)
            $('#sm_alert_missing_ref_option_configuration').show();
        else
            $('#sm_alert_missing_ref_option_configuration').hide();
        return true;
    }

    $('#sm_alert_missing_ref_option_yes').click(function() { return displaySellermaniaAlertOption(); });
    $('#sm_alert_missing_ref_option_no').click(function() { return displaySellermaniaAlertOption(); });
    displaySellermaniaAlertOption();


    function displaySellermaniaExportOptions()
    {
        if ($('#sm_export_all_yes').attr('checked') == 'checked' || $('#sm_export_all_yes').attr('checked') == true) {
            $('#sm_export_all_configuration').hide();
        } else {
            $('#sm_export_all_configuration').show();
        }
        return true;
    }

    $('#sm_export_all_yes').click(function() { return displaySellermaniaExportOptions(); });
    $('#sm_export_all_no').click(function() { return displaySellermaniaExportOptions(); });
    displaySellermaniaExportOptions();


    $('#see-advanced-export').click(function() {
        if ($('#advanced-export').is(':visible'))
            $('#advanced-export').hide();
        else
            $('#advanced-export').show();
        return false;
    });


    function switchSellermaniaTab(id) {
        $('#sellermania-module-help').addClass('hidden');
        $('#sellermania-module-export').addClass('hidden');
        $('#sellermania-module-import').addClass('hidden');
        $('#sellermania-module-search').addClass('hidden');
        $(id).removeClass('hidden');
    }

    $('#sellermania-admin-tab ul li a').click(function() {
        switchSellermaniaTab($(this).attr('href'));
    });
    if (window.location.href.indexOf('#sellermania-module-export') >= 0) {
        switchSellermaniaTab('#sellermania-module-export');
    }
    if (window.location.href.indexOf('#sellermania-module-import') >= 0) {
        switchSellermaniaTab('#sellermania-module-import');
    }
    if (window.location.href.indexOf('#sellermania-module-search') >= 0) {
        switchSellermaniaTab('#sellermania-module-search');
    }

    $('#sm_import_orders_with_client_email').change(function() {
        if ($(this).is(':checked')) {
            if (!confirm(sm_import_orders_with_client_email_label)) {
                $(this).prop("checked", false);
            }
        }
    });
});
