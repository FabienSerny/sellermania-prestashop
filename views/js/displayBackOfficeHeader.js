$(document).ready(function() {

    // Hide PDF invoice link on orders list when the order status contains "Sellermania"
    var row = 0;
    $('.order tr').each(function() {

        var column = 0;
        var flag_sellermania = 0;

        $(this).find('td').each(function() {
            if (column == 7 && $(this).text().trim().toLowerCase().indexOf("sellermania") >= 0)
                flag_sellermania = 1;
            else if (column == 9 && flag_sellermania == 1)
                $(this).html('&nbsp;');
            column++;
        });

        row++;
    });
});
