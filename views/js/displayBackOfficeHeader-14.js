$(document).ready(function() {

    // Hide PDF invoice link on orders list when the order status contains "Sellermania"
    var row = 0;
    $('.table tr').each(function() {

        var column = 0;
        var flag_sellermania = 0;

        $(this).find('td').each(function() {
            if (column == 6 && $(this).text().trim().toLowerCase().indexOf("marketplace") >= 0)
                flag_sellermania = 1;
            else if (column == 8 && flag_sellermania == 1)
                $(this).html('&nbsp;');
            column++;
        });

        row++;
    });
});
