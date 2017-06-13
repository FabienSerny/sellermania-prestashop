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

function handleOrdersBulkAction(selected_orders, sellermania_action)
{
    // Retrieve selected orders
    if (selected_orders.length < 1) {
        alert(txt_sellermania_select_at_least_one_order);
        return false;
    }

    // Retrieve carrier
    var carrier = '';
    if (sellermania_action == 'bulk-send-orders') {
        var carrier = window.prompt(txt_sellermania_carrier_selection, sellermania_default_carrier);
    }

    // Post values
    var post_values = { sellermania_bulk_action: sellermania_action, sellermania_selected_orders: JSON.stringify(selected_orders), sellermania_carrier: carrier};
    $.post(sellermania_admin_orders_url, post_values).done(function(data) {
        var result = JSON.parse(data);
        if (result.result == 'OK') {

            var confirmation_message = txt_sellermania_orders_updated;

            if (sellermania_action == 'bulk-confirm-orders') {
                for (var i in result.result_details.OrderItemConfirmationStatus) {
                    var conf_status = result.result_details.OrderItemConfirmationStatus[i];
                    confirmation_message += "\n\n#" + conf_status.id_order_prestashop + ' (sku: ' + conf_status.sku + ') : ';
                    if (conf_status.Status == 'ERROR') {
                        confirmation_message += conf_status.Message;
                    } else {
                        confirmation_message += conf_status.Status;
                    }
                }
            }

            alert(confirmation_message);
        } else {
            alert(txt_sellermania_error_occured);
        }
    });
}

function getSelectedOrders16(pForm, boxName, parent)
{
    var boxes = [];
    for (i = 0; i < pForm.elements.length; i++) {
        if (pForm.elements[i].name == boxName && pForm.elements[i].checked == true) {
            boxes.push(pForm.elements[i].value);
        }
    }
    return boxes;
}

function getSelectedOrders()
{
    var boxes = [];
    $('.order-selector').each(function() {
        if ($(this).prop('checked')) {
            boxes.push($(this).val());
        }
    });
    return boxes;
}