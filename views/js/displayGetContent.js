$(document).ready(function() {

    function displaySellermaniaCredentials()
    {
        if ($('#import_orders_yes').attr('checked') == 'checked')
            $('#import_orders_credentials').fadeIn();
        else
            $('#import_orders_credentials').fadeOut();
        return true;
    }

    $('#import_orders_yes').click(function() { return displaySellermaniaCredentials(); });
    $('#import_orders_no').click(function() { return displaySellermaniaCredentials(); });
    displaySellermaniaCredentials();
});