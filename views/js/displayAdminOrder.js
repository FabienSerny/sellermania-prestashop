$(document).ready(function() {

    // Hide template
    $('#sellermania-template').hide();

    // Retrieve data
    var sellermania_title = $('#sellermania-template-title').html();
    var sellermania_customer = $('#sellermania-template-customer').html();
    var sellermania_order_summary = $('#sellermania-template-order-summary').html();
    var sellermania_status_update_result = $('#sellermania-template-status-update').html();

    // Get block identifier
    var sellermania_block_order_state = $('#content div div form #id_order_state');
    var sellermania_block_order_state_button = $('#content div div form input[name="submitState"]');
    var sellermania_block_customer = $('#content div div fieldset:first');
    var sellermania_block_button_prev_next = $('.button-command-prev-next');
    var sellermania_block_parent_button_prev_next = $('.button-command-prev-next').parent();
    var sellermania_block_address = $('.container-command:first').next();
    var sellermania_block_add_product = $('.add_product').parent();
    var sellermania_block_discount = $('#total_products').parent().parent().parent().next().next();
    var sellermania_block_customer_thread = $('.add_product').parent().parent().parent().parent().next().next();
    var sellermania_block_product_general_legend = $('.add_product').parent().next().next().next().next().next();
    var sellermania_block_products_list = new Array();

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
    sellermania_block_order_state.after(sellermania_title);
    sellermania_block_order_state.hide();
    sellermania_block_order_state_button.hide();

    // Replace customer block
    sellermania_block_customer.html(sellermania_customer);

    // Replace right column
    var order_buttons = '<div class="button-command-prev-next">' + sellermania_block_button_prev_next.html() + '</div><div class="clear"></div>';
    sellermania_block_parent_button_prev_next.html(order_buttons + sellermania_order_summary);

    // Hide address block, "Add product" button, discount block and message thread customer block
    sellermania_block_address.hide();
    sellermania_block_add_product.hide();
    sellermania_block_discount.hide();
    sellermania_block_customer_thread.hide();


    // Fill product details
    var nb_buttons = 0;
    for (i = 1; sellermania_block_products_list[i]; i++)
    {
        var sm_block_product = sellermania_block_products_list[i];

        var sku = sm_block_product.html().split('<br>');
        var remove = sku[1].split(' ');
        sku = sku[1].replace(remove[0], '').trim();

        var html_order_line = '';
        if (sellermania_products[sku].insurance_price > 0)
            html_order_line += '<br><b>Insurance price:</b> ' + sellermania_products[sku].insurance_price + ' ' + sellermania_products[sku].currency + '<br>';
        html_order_line += '<br><u>Order item ID:</u> <b>' + sellermania_products[sku].order_item_id + '</b><br>';
        html_order_line += '<br><u>External order ID:</u> <b>' + sellermania_products[sku].external_order_id + '</b><br>';
        html_order_line += '<u>Sku:</u> <b>' + sku + '</b><br>';
        html_order_line += '<u>Ean:</u> <b>' + sellermania_products[sku].ean + '</b><br>';
        html_order_line += '<u>ASIN:</u> <b>' + sellermania_products[sku].product_id + '</b><br>';
        html_order_line += '<u>Condition:</u> <b>' + sellermania_products[sku].item_condition + '</b><br>';
        html_order_line += '<u>Status:</u> <b>' + sellermania_products[sku].status + '</b><br>';
        if (sellermania_products[sku].status_id == 6)
        {
            html_order_line += '<input type="radio" id="status_confirm_' + i + '" name="status_' + i + '" value="9" class="status_order_line" data-toggle="' + sku + '" /> Confirm ';
            html_order_line += '<input type="radio" id="status_cancel_' + i + '" name="status_' + i + '" value="4" class="status_order_line" data-toggle="' + sku + '" /> Cancel ';
            nb_buttons++;
        }
        sm_block_product.append(html_order_line);
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
                $('#status_confirm_' + i).prop('checked', 'checked');
            sellermania_update_line_status();
        });
        $('#sellermania_cancel_all_products').click(function() {
            for (i = 1; sellermania_block_products_list[i]; i++)
                $('#status_cancel_' + i).prop('checked', 'checked');
            sellermania_update_line_status();
        });
    }

    // If status has changed
    if (sellermania_status_update_result !== 'undefined')
        sellermania_block_product_general_legend.after(sellermania_status_update_result);


    // Check status
    $('.status_order_line').click(function() {
        sellermania_update_line_status();
    });


    function sellermania_update_line_status()
    {
        // Fill product details
        var sellermania_status_not_defined = 0;
        for (i = 1; sellermania_block_products_list[i]; i++)
        {
            // Retrieve sku
            var sku = sellermania_block_products_list[i].html().split('<br>');
            var remove = sku[1].split(' ');
            sku = sku[1].replace(remove[0], '').trim();

            // Status
            var order_line_status = 'Not defined';
            if ($('#status_confirm_' + i).prop('checked'))
                order_line_status = 'Confirmed';
            if ($('#status_cancel_' + i).prop('checked'))
                order_line_status = 'Cancelled';

            // Count not defined Status
            if (order_line_status == 'Not defined')
                sellermania_status_not_defined++;
        }

        // Check how many not defined status there is
        if (sellermania_status_not_defined == 0)
        {
            // Display submit
            sellermania_block_product_general_legend.html('<input type="button" id="sellermania_register_status" value="Register status" class="button" />');

            // Generate form and submit it
            $('#sellermania_register_status').click(function() {

                // Generate form
                var html_form = '<form id="sellermania_status_form" action="" method="POST">';
                html_form += '<input type="hidden" name="sellermania_status_form_registration" value="1" />';
                $('.status_order_line').each(function() {
                    if ($(this).prop('checked'))
                    {
                        html_form += '<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).attr('value') + '" />';
                        html_form += '<input type="hidden" name="sku_' + $(this).attr('name') + '" value="' + $(this).attr('data-toggle') + '" />';
                    }
                });
                html_form += '</form>';

                // Display form and submit
                sellermania_block_product_general_legend.html(html_form);
                $('#sellermania_status_form').submit();

                return false;
            });
        }
    }
});
