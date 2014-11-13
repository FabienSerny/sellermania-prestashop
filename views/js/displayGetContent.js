/*
 * 2010 - 2014 Sellermania / Froggy Commerce / 23Prod SARL
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
 *  @author Fabien Serny - Froggy Commerce <team@froggy-commerce.com>
 *  @copyright	2010-2014 Sellermania / Froggy Commerce / 23Prod SARL
 *  @version		1.0
 *  @license		http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

$(document).ready(function() {


    function displaySellermaniaCredentials()
    {
        if ($('#sm_import_orders_yes').attr('checked') == 'checked' || $('#sm_import_orders_yes').attr('checked') == true)
            $('#sm_import_orders_credentials').fadeIn();
        else
            $('#sm_import_orders_credentials').fadeOut();
        return true;
    }

    $('#sm_export_all_yes').click(function() { return displaySellermaniaCredentials(); });
    $('#sm_export_all_no').click(function() { return displaySellermaniaCredentials(); });
    displaySellermaniaCredentials();


	function displaySellermaniaExportOptions()
	{
		if ($('#sm_export_all_yes').attr('checked') == 'checked' || $('#sm_export_all_yes').attr('checked') == true)
			$('#sm_export_all_configuration').fadeOut();
		else
			$('#sm_export_all_configuration').fadeIn();
		return true;
	}

	$('#sm_export_all_yes').click(function() { return displaySellermaniaExportOptions(); });
	$('#sm_export_all_no').click(function() { return displaySellermaniaExportOptions(); });
	displaySellermaniaExportOptions();


	$('#see-advanced-export').click(function() {
		if ($('#advanced-export').is(':visible'))
			$('#advanced-export').fadeOut();
		else
			$('#advanced-export').fadeIn();
		return false;
	});

});