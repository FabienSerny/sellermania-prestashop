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

    // Hide PDF invoice link on orders list when the order status contains "Sellermania"
    $('#order div table tbody tr').each(function() {
        hidePDFButtons($(this));
    });

    $('#form-order div div table tbody tr').each(function() {
        hidePDFButtons($(this));
    });

    function hidePDFButtons(line)
    {
        var column = 0;
        var id_order = 0;
        var flag_sellermania = 0;

        line.find('td').each(function() {
            if (column == 1) {
                id_order = $(this).text();
            }
            else if (column == 8 && line.text().trim().toLowerCase().indexOf("marketplace") >= 0) {
                flag_sellermania = 1;
            }
            else if (column == 10 && flag_sellermania == 1) {
                $(this).find('a').each(function() {
                    if ($(this).attr('href').indexOf("generateInvoicePDF") >= 0) {
                        $(this).attr('href', sellermania_invoice_url + '&id_order=' + id_order);
                    } else {
                        $(this).remove();
                    }
                });
            }
            column++;
        });
    }


	if (nb_sellermania_orders_in_error > 0) {
        $('#order').before('<p align="center" style="border: 1px solid #cc0000;color: #d8000c;background-color:#ffbaba;padding:5px"><b>' + nb_sellermania_orders_in_error + '</b> ' + txt_sellermania_orders_in_error + '</p>');
    }


    /**
     * Handle Orders Bulk Action
     */

    html_bulk_actions = '<li class="divider"></li>';

    html_bulk_actions += '<li><a href="#" id="sellermania-bulk-confirm-orders">';
    html_bulk_actions += '<i class="icon-ok-circle"></i>&nbsp;' + txt_sellermania_confirm_orders;
    html_bulk_actions += '</a></li>';

    html_bulk_actions += '<li><a href="#" id="sellermania-bulk-send-orders">';
    html_bulk_actions += '<i class="icon-paper-plane"></i>&nbsp;' + txt_sellermania_send_orders;
    html_bulk_actions += '</a></li>';

    $('.bulk-actions .dropdown-menu').append(html_bulk_actions);

    $('#sellermania-bulk-confirm-orders').click(function() {
        var selected_orders = getSelectedOrders16($(this).closest('form').get(0), 'orderBox[]', true);
        handleOrdersBulkAction(selected_orders, 'bulk-confirm-orders');
        return false;
    });

    $('#sellermania-bulk-send-orders').click(function() {
        var selected_orders = getSelectedOrders16($(this).closest('form').get(0), 'orderBox[]', true);
        handleOrdersBulkAction(selected_orders, 'bulk-send-orders');
        return false;
    });

});
