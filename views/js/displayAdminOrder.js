$(document).ready(function() {

    // Hide template
    $('#sellermania-template').hide();

    // Retrieve data
    var sellermania_title = $('#sellermania-template-title').html();
    var sellermania_customer = $('#sellermania-template-customer').html();
    var sellermania_order_summary = $('#sellermania-template-order-summary').html();
    var sellermania_status_update_result = $('#sellermania-template-status-update').html();

    // Replace status order selection
    $('#content div div form #id_order_state').after(sellermania_title);
    $('#content div div form #id_order_state').hide();
    $('#content div div form input[name="submitState"]').hide();

    // Replace status order selection
    $('#content div div fieldset:first').html(sellermania_customer);

    // Replace right column
    var order_buttons = '<div class="button-command-prev-next">' + $('.button-command-prev-next').html() + '</div><div class="clear"></div>';
    $('.button-command-prev-next').parent().html(order_buttons + sellermania_order_summary);

    // Hide address block
    $('.container-command:first').next().hide();

    // Hide "Add product" button
    $('.add_product').parent().hide();

    // Hide discount block
    $('#total_products').parent().parent().parent().next().next().hide();

    // Hide message thread customer block
    $('.add_product').parent().parent().parent().parent().next().next().hide();


    // Fill product details
    var sellermania_order_line = 0;
    $('#orderProducts tr').each(function() {
        if (sellermania_order_line > 0)
        {
            var sellermania_order_row = 0;
            $(this).find('td').each(function() {
                if (sellermania_order_row == 1 && $(this).is(':visible'))
                {
                    var sku = $(this).html().split('<br>');
                    var remove = sku[1].split(' ');
                    sku = sku[1].replace(remove[0], '').trim();

                    var html_order_line = $(this).html();
                    if (sellermania_products[sku].insurance_price > 0)
                        html_order_line += '<br><b>Insurance price:</b> ' + sellermania_products[sku].insurance_price + ' ' + sellermania_products[sku].currency + '<br>';
                    html_order_line += '<br><u>Order item ID:</u> <b>' + sellermania_products[sku].order_item_id + '</b><br>';
                    html_order_line += '<u>Sku:</u> <b>' + sku + '</b><br>';
                    html_order_line += '<u>Ean:</u> <b>' + sellermania_products[sku].ean + '</b><br>';
                    html_order_line += '<u>Condition:</u> <b>' + sellermania_products[sku].item_condition + '</b><br>';
                    html_order_line += '<u>Status:</u> <b>' + sellermania_products[sku].status + '</b><br>';
                    if (sellermania_products[sku].status_id == 6)
                    {
                        html_order_line += '<input type="radio" id="status_confirm_' + sellermania_order_line + '" name="status_' + sellermania_order_line + '" value="9" class="status_order_line" data-toggle="' + sku + '" /> Confirm ';
                        html_order_line += '<input type="radio" id="status_cancel_' + sellermania_order_line + '" name="status_' + sellermania_order_line + '" value="4" class="status_order_line" data-toggle="' + sku + '" /> Cancel ';
                    }
                    $(this).html(html_order_line);
                }
                sellermania_order_row++;
            });
        }
        sellermania_order_line++;
    });


    // If status has changed
    if (sellermania_status_update_result !== 'undefined')
        $('.add_product').parent().next().next().next().next().next().after(sellermania_status_update_result);


    // Check status
    $('.status_order_line').click(function() {

        // Fill product details
        var sellermania_status_not_defined = 0;
        var sellermania_order_line = 0;

        $('#orderProducts tr').each(function() {
            if (sellermania_order_line > 0)
            {
                var sellermania_order_row = 0;
                $(this).find('td').each(function() {
                    if (sellermania_order_row == 1 && $(this).is(':visible'))
                    {
                        // Retrieve sku
                        var sku = $(this).html().split('<br>');
                        var remove = sku[1].split(' ');
                        sku = sku[1].replace(remove[0], '').trim();

                        // Status
                        var order_line_status = 'Not defined';
                        if ($('#status_confirm_' + sellermania_order_line).prop('checked'))
                            order_line_status = 'Confirmed';
                        if ($('#status_cancel_' + sellermania_order_line).prop('checked'))
                            order_line_status = 'Cancelled';

                        // Count not defined Status
                        if (order_line_status == 'Not defined')
                            sellermania_status_not_defined++;
                    }
                    sellermania_order_row++;
                });
            }
            sellermania_order_line++;
        });

        // Check how many not defined status there is
        if (sellermania_status_not_defined == 0)
        {
            // Display submit
            $('.add_product').parent().next().next().next().next().next().html('<input type="button" id="sellermania_register_status" value="Register status" class="button" />');

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
                $('.add_product').parent().next().next().next().next().next().html(html_form);
                $('#sellermania_status_form').submit();

                return false;
            });
        }
    });
});
