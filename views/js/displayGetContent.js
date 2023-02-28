/*
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
 */

$(document).ready(function() {

    function displaySellermaniaCredentials()
    {
        let val = $('input[name=sm_import_orders]:checked').val();
        if ("yes" === val) {
            $('#sm_import_orders_credentials').show();
        } else {
            $('#sm_import_orders_credentials').hide();
        }
        return true;
    }

    //displaySellermaniaCredentials();

    $('input[name=sm_import_orders]').change(function () {
        displaySellermaniaCredentials();
    });


    function displaySellermaniaSyncOption()
    {
        if ($('#sm_stock_sync_option_yes').is(':checked')) {
            $('#sm_stock_sync_option_configuration').show();
        } else if ($('#sm_stock_sync_option_no').is(':checked')) {
            $('#sm_stock_sync_option_configuration').hide();
        }
        return true;
    }

    $('#sm_stock_sync_option_yes').click(function() { return displaySellermaniaSyncOption(); });
    $('#sm_stock_sync_option_no').click(function() { return displaySellermaniaSyncOption(); });
    displaySellermaniaSyncOption();


    function displaySellermaniaImportConfiguration()
    {
        if (smCheckIfOptionIsSelected('#sm_import_method_cron'))
            $('#sm_import_method_cron_configuration').show();
        else
            $('#sm_import_method_cron_configuration').hide();
        return true;
    }
    displaySellermaniaImportConfiguration();

    $('input[name=sm_import_method]').change(function () {
        let value = this.value;
        if ("cron" === value) {
            $('#sm_import_method_cron_configuration').show();
        } else {
            $('#sm_import_method_cron_configuration').hide();
        }
    })


    function displaySellermaniaAlertOption()
    {
        if (smCheckIfOptionIsSelected('#sm_alert_missing_ref_option_yes'))
            $('#sm_alert_missing_ref_option_configuration').show();
        else
            $('#sm_alert_missing_ref_option_configuration').hide();
        return true;
    }

    $('#sm_alert_missing_ref_option_yes').click(function() { return displaySellermaniaAlertOption(); });
    $('#sm_alert_missing_ref_option_no').click(function() { return displaySellermaniaAlertOption(); });
    displaySellermaniaAlertOption();

    $('input[type=radio][name=sm_product_to_include_in_feed]').change(function () {
        if ("all" === this.value) {
            $('#sm_last_days_to_include_in_feed').val("")
        } else if ("without_oos" === this.value) {
            $('#sm_last_days_to_include_in_feed').val("7")
        }
    })

    function displaySellermaniaExportOptions()
    {
        if ($('#sm_export_all_yes').is(":checked")) {
            $('#sm_export_all_configuration').hide();
        } else {
            $('#sm_export_all_configuration').show();
        }
        return true;
    }

    $('#sm_export_all_yes').click(function() {
        return displaySellermaniaExportOptions();
    });
    $('#sm_export_all_no').click(function() {
        return displaySellermaniaExportOptions();
    });
    displaySellermaniaExportOptions();


    $('#see-advanced-export').click(function() {
        if ($('#advanced-export').is(':visible'))
            $('#advanced-export').hide();
        else
            $('#advanced-export').show();
        return false;
    });


    function switchSellermaniaTab(id, scrolltop = 0) {
        $('.panel', '#sellermania-admin-tab').addClass('hidden');
        $(id).removeClass('hidden');
        if (scrolltop) {
            scrolltop -= 126;
        }
        $(window).scrollTop(scrolltop);
    }

    $('.nav-link', '#sellermania-admin-tab').click(function(e) {
        e.preventDefault();
        let id = $(this).attr('href');
        window.location.hash = id;
        switchSellermaniaTab(id, $(window).scrollTop());
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
    $('#syncTrackingno').bind('click', false);
    if ($('#sm_import_ac_orders_after_adding_tracking_number').is(':checked')) {
        $('#syncTrackingno').unbind('click');
    }
    function smCheckIfOptionIsSelected(elem_id) {
        if ($(elem_id).attr('checked') == 'checked' || $(elem_id).attr('checked') == true) {
            return true;
        }
        return false;
    }

    $('.config-section-body-trigger').click(function (e) {
        e.preventDefault();
        let $parent = $(this).parent().parent();
        $('.config-section-body', $parent).toggle();
        $(this).toggleClass('activated');
    });
});
