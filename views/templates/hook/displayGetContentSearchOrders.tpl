{*
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
*}


<form action="" method="post">
    <fieldset>
        <legend><img src="{$sellermania_module_path}logo.gif" alt="" title="" />{l s='Export catalog' mod='sellermania'}</legend>
        <div class="margin-form" style="padding-left:15px">

            <p>
                <label>{l s='Search marketplace orders by reference' mod='sellermania'}</label>
                <input type="text" name="marketplace_order_reference" />
            </p>

            <br><p><input type="submit" name="export_configuration" value="{l s='Validate' mod='sellermania'}" class="button" /></p>

            {if isset($sm_confirm_export_options)}<br><p class="conf"><strong>{l s='Configuration has been saved' mod='sellermania'}</strong></p>{/if}

        </div>
    </fieldset>
</form>