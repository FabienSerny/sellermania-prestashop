$(document).ready(function() {

    // Hide template
    $('#sellermania-template').hide();

    // Retrieve data
    var sellermania_title = $('#sellermania-template-title').html();
    var sellermania_customer = $('#sellermania-template-customer').html();
    var sellermania_order_summary = $('#sellermania-template-order-summary').html();
    var sellermania_status_update_result = $('#sellermania-template-status-update').html();
    var sellermania_error_result = $('#sellermania-template-error').html();

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
    var sellermania_block_product_general_legend = $('#orderProducts').next();
    var sellermania_block_cancel_button = $('#content form fieldset div div input');

    // Get products list block identifier
    var sellermania_order_line = 0;
    var sellermania_block_products_list = new Array();
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


    // Fill product details
    var nb_buttons = 0;
    for (i = 1; sellermania_block_products_list[i]; i++)
    {
        var sm_block_product = sellermania_block_products_list[i];

        var sku_short = sm_block_product.html().split('<br>');
        var remove = sku_short[1].split(' ');
        if (remove[1] == ':')
            sku_short = sku_short[1].replace(remove[0] + ' ' + remove[1], '').trim();
        else
            sku_short = sku_short[1].replace(remove[0], '').trim();

        if (sku_short != '')
        {
            var html_order_line = '';
            if (sellermania_products[sku_short].insurance_price > 0)
                html_order_line += '<br><u>' + insurance_price_label + '</u> <b>' + sellermania_products[sku_short].insurance_price + ' ' + sellermania_products[sku_short].currency + '</b><br>';
            if (sellermania_products[sku_short].order_item_id != '') html_order_line += '<br><u>' + order_item_id_label + '</u> <b>' + sellermania_products[sku_short].order_item_id + '</b><br>';
            if (sellermania_products[sku_short].external_order_id != '') html_order_line += '<br><u>' + external_order_id_label + '</u> <b>' + sellermania_products[sku_short].external_order_id + '</b><br>';
            html_order_line += '<u>' + sku_label + '</u> <b>' + sellermania_products[sku_short].sku + '</b><br>';
            if (sellermania_products[sku_short].ean != '') html_order_line += '<u>' + ean_label + '</u> <b>' + sellermania_products[sku_short].ean + '</b><br>';
            if (sellermania_products[sku_short].product_id != '') html_order_line += '<u>' + asin_label + '</u> <b>' + sellermania_products[sku_short].product_id + '</b><br>';
            if (sellermania_products[sku_short].item_condition != '')
                html_order_line += '<u>' + condition_label + '</u> <b>' + sellermania_products[sku_short].item_condition + '</b><br>';
            else
                html_order_line += '<u>' + condition_label + '</u> <b>' + unknown_label + '</b><br>';
            html_order_line += '<u>' + status_label + '</u> <b>' + sellermania_products[sku_short].status + '</b><br>';
            if (sellermania_products[sku_short].status_id == 6)
            {
                html_order_line += '<input type="radio" id="status_confirm_' + i + '" name="status_' + i + '" value="9" class="status_order_line" data-toggle="' + sellermania_products[sku_short].sku + '" /> ' + confirm_label + ' ';
                html_order_line += '<input type="radio" id="status_cancel_' + i + '" name="status_' + i + '" value="4" class="status_order_line" data-toggle="' + sellermania_products[sku_short].sku + '" /> ' + cancel_label + ' ';
                nb_buttons++;
            }
            sm_block_product.append(html_order_line);
        }
    }

    // Add button check all
    if (nb_buttons > 0)
    {
        var sellermania_html_buttons_all = '<input type="button" value="Confirm all products" id="sellermania_confirm_all_products" class="button" />';
        sellermania_html_buttons_all += '<input type="button" value="Cancel all products" id="sellermania_cancel_all_products" class="button" />';
        sellermania_block_product_general_legend.html(sellermania_html_buttons_all);
        sellermania_block_product_general_legend.show();

        $('#sellermania_confirm_all_products').click(function() {
            for (i = 1; sellermania_block_products_list[i]; i++)
                $('#status_confirm_' + i).attr('checked', 'checked');
            sellermania_update_line_status();
        });
        $('#sellermania_cancel_all_products').click(function() {
            for (i = 1; sellermania_block_products_list[i]; i++)
                $('#status_cancel_' + i).attr('checked', 'checked');
            sellermania_update_line_status();
        });
    }

    // If status has changed
    if (sellermania_status_update_result !== 'undefined')
        sellermania_block_product_general_legend.after(sellermania_status_update_result);
    if (sellermania_error_result !== 'undefined')
        sellermania_block_product_general_legend.after(sellermania_error_result);


    // Check status
    $('.status_order_line').click(function() {
        sellermania_update_line_status();
    });


    function sellermania_update_line_status()
    {
        // Fill product details
        var line_max = 0;
        var sellermania_status_defined = 0;
        for (i = 1; sellermania_block_products_list[i]; i++)
        {
            // Status
            var order_line_status = 'Not defined';
            if ($('#status_confirm_' + i).attr('checked'))
                order_line_status = 'Confirmed';
            if ($('#status_cancel_' + i).attr('checked'))
                order_line_status = 'Cancelled';

            // Count not defined Status
            if (order_line_status != 'Not defined')
                sellermania_status_defined++;

            // Save the line max
            line_max = i;
        }

        // Check how many not defined status there is
        if (sellermania_status_defined > 0)
        {
            // Display submit
            sellermania_block_product_general_legend.html('<input type="button" id="sellermania_register_status" value="Register status" class="button" />');

            // Generate form and submit it
            $('#sellermania_register_status').click(function() {

                // Generate form
                var html_form = '<input type="hidden" name="sellermania_line_max" value="' + line_max + '" />';
                $('.status_order_line').each(function() {
                    if ($(this).attr('checked'))
                    {
                        html_form += '<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).attr('value') + '" />';
                        html_form += '<input type="hidden" name="sku_' + $(this).attr('name') + '" value="' + $(this).attr('data-toggle') + '" />';
                    }
                });

                // Display form and submit
                $('#sellermania_status_form').html(html_form);
                document.forms["sellermania_status_form"].submit();

                return false;
            });
        }
    }
});
