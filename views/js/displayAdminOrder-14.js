var sellermania_status_update_result = '';
var sellermania_error_result = '';
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

    // Get block identifier
    var sellermania_block_order_state = $('#content div form select[name="id_order_state"]');
    var sellermania_block_order_state_button = $('#content div form input[name="submitState"]');
    var sellermania_block_customer = $('#content div h2').next().next().next().next().next().next().next();
    var sellermania_block_button_prev_next = $('#content div h2');
    var sellermania_block_next_button_prev_next = $('#content div h2').next();
    var sellemernia_right_column = $('.path_bar').next().next().next().next();
    var sellermania_block_address1 = sellermania_block_button_prev_next.parent().next().next().next().next();
    var sellermania_block_address2 = sellermania_block_address1.next();
    var sellermania_block_customer_thread = sellermania_block_address2.next().next().next().next().next();
    var sellermania_block_discount = sellermania_block_customer_thread.next();
    var sellermania_block_cancel_button = $('#content form fieldset div div input');

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
    sellermania_block_next_button_prev_next.html(sellermania_title);
    sellermania_block_order_state.hide();
    sellermania_block_order_state_button.hide();

    // Replace customer block
    sellermania_block_customer.html(sellermania_customer);

    // Replace right column
    sellemernia_right_column.html(sellermania_order_summary);
    sellemernia_right_column.css('width', '45%');

    // Hide address block, "Add product" button, discount block, message thread customer block and general legend
    sellermania_block_address1.hide();
    sellermania_block_address2.hide();
    sellermania_block_discount.hide();
    sellermania_block_customer_thread.hide();
    sellermania_block_product_general_legend.hide();
    sellermania_block_cancel_button.hide();

    // Hide column products
    $('.cancelCheck').hide();
    $('.cancelQuantity').hide();
    $('#orderProducts tbody tr th').next().next().next().next().next().next().hide();

});
