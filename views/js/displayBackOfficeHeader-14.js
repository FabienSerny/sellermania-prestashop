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

	if (nb_sellermania_orders_in_error > 0) {
		$('.path_bar').after('<p align="center" style="border: 1px solid #cc0000;color: #d8000c;background-color:#ffbaba;padding:5px"><b>' + nb_sellermania_orders_in_error + '</b> ' + txt_sellermania_orders_in_error + '</p>');
    }


});
