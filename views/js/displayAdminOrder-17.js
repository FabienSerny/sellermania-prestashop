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

var sellermania_status_update_result = '';
var sellermania_error_result = '';
var sellermania_block_product_general_legend = $('#orderProductsPanel .discount-action');
var sellermania_block_products_list = new Array();

$(document).ready(function() {

    // Hide template

    // Retrieve data
    var sellermania_title = $('#sellermania-template-title').html();
    var sellermania_customer = $('#sellermania-template-customer').html();
    var sellermania_order_summary = $('#sellermania-template-order-summary').html();
    sellermania_status_update_result = $('#sellermania-template-status-update').html();
    sellermania_error_result = $('#sellermania-template-error').html();

    // Get block identifiers
    var sellermania_block_order_state = $('#id_order_state');
    var sellermania_block_order_state_button = $('#status button[name="submitState"]');
    var sellermania_block_button_prev_next = $('#content .row .col-lg-6 .panel h3');
    var sellermania_block_order_actions = sellermania_block_button_prev_next.next();
    var sellermania_block_shipping_title = $('#myTab');
    var sellermania_block_shipping = $('#myTab').next();
    var sellermania_block_payment = $('#view_order_payments_block');
    var sellermania_block_documents = $('#tabOrder li').next();
    var sellermania_add_voucher = $('#add_voucher');
    var sellermania_add_product = $('#add_product');
    var sellermania_panel_voucher = $('.panel-vouchers');
    var sellermania_panel_total = $('.panel-vouchers').next();
    var sellermania_toolbar = $('.icon-calendar-empty').parent().parent().parent();

    // Get products list block identifier
    var sellermania_order_line = 1;
    sellermania_block_products_list = new Array();
    $('#orderProductsTable .productReference').each(function() {
        if ($(this).is(':visible')) {
            sellermania_block_products_list[sellermania_order_line] = $(this);
        }
        sellermania_order_line++;
    });


    // Replace status order selection
    sellermania_block_order_state.after(sellermania_title);
    if (!sellermania_order_edit_status && !sellermania_enable_native_order_interface) {
        sellermania_block_order_state.hide();
        sellermania_block_order_state_button.hide();
    }


    // Hide order actions
    if (!sellermania_enable_native_order_interface) {
        sellermania_block_order_actions.hide();
        sellermania_block_shipping_title.hide();
        sellermania_block_payment.hide();
        sellermania_block_documents.hide();
        sellermania_add_voucher.hide();
        sellermania_add_product.hide();
        sellermania_panel_voucher.hide();
        sellermania_panel_total.hide();
        sellermania_toolbar.hide();
        $('form[name="order_message"]').hide();
        $('.product_action').hide();
    }

    // Reenable native refund system
    if (sellermania_enable_native_refund_system == true || sellermania_enable_native_order_interface == true)
    {
        $('.icon-print').parent().parent().show();
        sellermania_block_order_actions.show();
    }
});
