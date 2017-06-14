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

    // Hide PDF invoice link on orders list when the order status contains "Sellermania"
    var row = 0;
    $('.order tr').each(function() {

        var column = 0;
        var id_order = 0;
        var flag_sellermania = 0;

        $(this).find('td').each(function() {
            if (row != 0 && row != 1) {
                if (column == 1) {
                    id_order = $(this).text().trim();
                    $(this).html('<input type="checkbox" name="orderBox[]" class="order-selector" value="' + id_order + '"><br>' + id_order);
                    $(this).attr('onclick', '').unbind('click');
                }
                if (column == 7 && $(this).text().trim().toLowerCase().indexOf("marketplace") >= 0) {
                    flag_sellermania = 1;
                }
                else if (column == 9 && flag_sellermania == 1) {
                    $(this).find('span a').each(function () {
                        if ($(this).attr('href').indexOf("generateInvoicePDF") >= 0) {
                            $(this).attr('href', sellermania_invoice_url + '&id_order=' + id_order);
                        } else {
                            $(this).remove();
                        }
                    });
                }
                column++;
            }
        });

        row++;
    });

	if (nb_sellermania_orders_in_error > 0) {
        $('.toolbar-placeholder').after('<p align="center" style="border: 1px solid #cc0000;color: #d8000c;background-color:#ffbaba;padding:5px"><b>' + nb_sellermania_orders_in_error + '</b> ' + txt_sellermania_orders_in_error + '</p>');
    }

    /**
     * Handle Orders Bulk Action
     */

    html_bulk_actions = '<input type="button" class="button" value="' + txt_sellermania_select_all + '" id="select-all-orders">';
    html_bulk_actions += '<input type="button" class="button" value="' + txt_sellermania_unselect_all + '" id="unselect-all-orders">';
    html_bulk_actions += '<input type="button" class="button" value="' + txt_sellermania_confirm_orders + '" id="sellermania-bulk-confirm-orders">';
    html_bulk_actions += '<input type="button" class="button" value="' + txt_sellermania_send_orders + '" id="sellermania-bulk-send-orders">';
    $('.order').after(html_bulk_actions);

    $('#select-all-orders').click(function() {
        $('.order-selector').prop('checked', 'checked');
    });
    $('#unselect-all-orders').click(function() {
        $('.order-selector').prop('checked', '');
    });

    $('#sellermania-bulk-confirm-orders').click(function() {
        var selected_orders = getSelectedOrders();
        handleOrdersBulkAction(selected_orders, 'bulk-confirm-orders');
        return false;
    });

    $('#sellermania-bulk-send-orders').click(function() {
        var selected_orders = getSelectedOrders();
        handleOrdersBulkAction(selected_orders, 'bulk-send-orders');
        return false;
    });
});
