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
var sellermania_shipping_status_update_result = '';
var sellermania_block_product_general_legend = '';
var sellermania_block_products_list = new Array();
var sellermania_right_column = '';

$(document).ready(function() {

    // Hide template
    $('#sellermania-template').hide();

    // Retrieve data
    var sellermania_title = $('#sellermania-template-title').html();
    var sellermania_customer = $('#sellermania-template-customer').html();
    var sellermania_order_summary = $('#sellermania-template-order-summary').html();    
    sellermania_status_update_result = $('#sellermania-template-status-update').html();
    sellermania_error_result = $('#sellermania-template-error').html();
    sellermania_shipping_status_update_result = $('#sellermania_template_shipping_status_update').html();

    // If status has changed
    if (sellermania_status_update_result !== 'undefined'){       
        $("#ajax_confirmation").after(sellermania_status_update_result);}
    if (sellermania_error_result !== 'undefined'){        
        $("#ajax_confirmation").after(sellermania_error_result);}
    if(sellermania_shipping_status_update_result!== 'undefined'){
        $("#ajax_confirmation").after(sellermania_shipping_status_update_result);
    }

    // Get block identifiers
    var sellermania_block_order_state = $('#id_order_state');
    var sellermania_block_order_state_button = $('#status button[name="submitState"]');
    $('.icon-user').each(function() {
        if ($(this).parent().is('h3'))
            sellermania_right_column = $(this).parent().parent();
    });
    var sellermania_block_button_prev_next = $('#content .row .col-lg-6 .panel h3');
    var sellermania_block_order_actions = sellermania_block_button_prev_next.next();
    var sellermania_block_shipping_title = $('#myTab');
    var sellermania_block_shipping = $('#myTab').next();
    var sellermania_block_payment = $('#formAddPayment').parent().parent().parent();
    var sellermania_block_documents = $('#tabOrder li').next();
    var sellermania_add_voucher = $('#add_voucher');
    var sellermania_add_product = $('#add_product');
    var sellermania_panel_voucher = $('.panel-vouchers');
    var sellermania_panel_total = $('.panel-vouchers').next();
    var sellermania_toolbar = $('.icon-calendar-empty').parent().parent().parent();

    // Get products list block identifier
    var sellermania_order_line = 0;
    sellermania_block_products_list = new Array();
    sellermania_block_product_general_legend = $('#orderProducts').next().next().find('div:first');
    $('#orderProducts tr').each(function() {
        if (sellermania_order_line > 0)
        {
            var sellermania_order_row = 0;
            $(this).find('td').each(function() {
                if (sellermania_order_row == 1 && $(this).is(':visible'))
                    sellermania_block_products_list[sellermania_order_line] = $(this);
                sellermania_order_row++;
            });
        }
        sellermania_order_line++;
    });

    // Fix for 1.6.0.6
    if ($('#addressShipping .thumbnail').length < 1)
    {
        sellermania_block_order_state = $('#id_order_state_chosen');
        sellermania_block_button_prev_next = $('#content .row .col-lg-7 .panel .panel-heading');
        sellermania_block_order_actions = sellermania_block_button_prev_next.next();
        sellermania_block_payment = $('#formAddPayment').parent();
        sellermania_toolbar = $('.icon-calendar-empty').parent().parent().parent().parent();
        sellermania_block_product_general_legend = $('#orderProducts').parent().next().next().next().find('div:first');

        // Fix in case of missing div (it happens sometimes)
        if (sellermania_block_product_general_legend.parent().hasClass('standard_refund_fields'))
            sellermania_block_product_general_legend = $('#orderProducts').parent().next().next().find('div:first');

        if (!sellermania_enable_native_order_interface) {
            $('#messages').parent().hide();
        }
    }

	// Fix for 1.6.0.11
	if (sellermania_right_column == '')
	{
		$('#onboarding-starter').parent().hide();
		$('.kpi-container').hide();
		$('.icon-print').parent().parent().hide();
		sellermania_right_column = $('.col-lg-5').find('.panel');
	}


    // Replace status order selection
    sellermania_block_order_state.after(sellermania_title);
    if (!sellermania_order_edit_status && !sellermania_enable_native_order_interface) {
        sellermania_block_order_state.hide();
        sellermania_block_order_state_button.hide();
    }

    // Replace right column
    if (!sellermania_enable_native_order_interface) {
        sellermania_right_column.first().html(sellermania_order_summary);
        sellermania_block_shipping.html(sellermania_customer);
    } else {
        sellermania_right_column.first().after(sellermania_order_summary);
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
        $('.product_action').hide();
    }

    // Legend
    if (!sellermania_enable_native_order_interface) {
        sellermania_block_product_general_legend.hide();
    }

    // Reenable native refund system
    if (sellermania_enable_native_refund_system == true || sellermania_enable_native_order_interface == true)
    {
        $('.icon-print').parent().parent().show();
        sellermania_block_order_actions.show();
    }
});
