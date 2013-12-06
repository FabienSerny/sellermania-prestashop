$(document).ready(function() {

    function displaySellermaniaCredentials()
    {
        if ($('#sm_import_orders_yes').attr('checked') == 'checked')
            $('#sm_import_orders_credentials').fadeIn();
        else
            $('#sm_import_orders_credentials').fadeOut();
        return true;
    }

    $('#sm_import_orders_yes').click(function() { return displaySellermaniaCredentials(); });
    $('#sm_import_orders_no').click(function() { return displaySellermaniaCredentials(); });
    displaySellermaniaCredentials();
});