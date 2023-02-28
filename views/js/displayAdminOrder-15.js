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
        $(".toolbar-placeholder").before(sellermania_status_update_result);}
    if (sellermania_error_result !== 'undefined'){        
        $(".toolbar-placeholder").before(sellermania_error_result);}
    if(sellermania_shipping_status_update_result !== 'undefined'){
        $(".toolbar-placeholder").before(sellermania_shipping_status_update_result);
    }
    
    // Get block identifier
    var sellermania_block_order_state = $('#content div div form #id_order_state');
    var sellermania_block_order_state_button = $('#content div div form input[name="submitState"]');
    var sellermania_block_customer = $('#content div div fieldset:first');
    var sellermania_block_button_prev_next = $('.button-command-prev-next');
    var sellermania_block_parent_button_prev_next = $('.button-command-prev-next').parent();
    var sellermania_block_address = $('.container-command:first').next();
    var sellermania_block_add_product = $('.add_product').parent();
    var sellermania_block_discount = $('#total_products').parent().parent().parent().next().next();
    var sellermania_block_customer_thread = $('form.container-command-top-spacing').next().next();
    var sellermania_block_partial_refund = $('#desc-order-partial_refund');
    var sellermania_block_order_toolbar = $('.bloc-command');
    var sellermania_block_warn = $('.warn');

    // Get products list block identifier
    var sellermania_order_line = 0;
    sellermania_block_products_list = new Array();
    sellermania_block_product_general_legend = $('#orderProducts').next();
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



    // Replace status order selection
    sellermania_block_order_state.after(sellermania_title);
    if (!sellermania_order_edit_status && !sellermania_enable_native_order_interface) {
        sellermania_block_order_state.hide();
        sellermania_block_order_state_button.hide();
    }

    // Replace customer block
    if (!sellermania_enable_native_order_interface) {
        sellermania_block_customer.html(sellermania_customer);
    } else {
        sellermania_block_customer.after(sellermania_customer);
    }

    // Replace right column
    var order_buttons = '<div class="button-command-prev-next">' + sellermania_block_button_prev_next.html() + '</div><div class="clear"></div>';
    sellermania_block_parent_button_prev_next.html(order_buttons + sellermania_order_summary);

    // Hide address block, "Add product" button, discount block, message thread customer block and general legend
    if (!sellermania_enable_native_order_interface) {
        sellermania_block_address.hide();
        sellermania_block_add_product.hide();
        sellermania_block_discount.hide();
        sellermania_block_customer_thread.hide();
        sellermania_block_product_general_legend.hide();
        sellermania_block_partial_refund.hide();
        sellermania_block_order_toolbar.hide();
        sellermania_block_warn.hide();

        // Hide product action
        $('.product_action').hide();
        $('.partial_refund_fields').next().hide();

        // Hide total at the bottom of the screen
        $('#total_products').parent().parent().hide();
    }
});
