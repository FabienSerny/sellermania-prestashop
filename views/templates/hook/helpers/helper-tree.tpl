{*
* 2010-2020 Sellermania / Froggy Commerce / 23Prod SARL
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
*  @copyright      2010-2020 Sellermania / Froggy Commerce / 23Prod SARL
*  @version        1.0
*  @license        http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<input type="button" id="category-tree-select-all-{$categories_tree_id}" value="{l s='Select all' mod='sellermania'}" class="button" />
<input type="button" id="category-tree-unselect-all-{$categories_tree_id}" value="{l s='Unselect all' mod='sellermania'}" class="button" />
<br><br>
{$categories_tree}

{literal}
<script>
        $('#category-tree-select-all-{/literal}{$categories_tree_id}{literal}').click(function() {
    $('.categories-tree-checkbox-{$categories_tree_id}').attr('checked', 'checked');
});
        $('#category-tree-unselect-all-{/literal}{$categories_tree_id}{literal}').click(function() {
    $('.categories-tree-checkbox-{$categories_tree_id}').removeAttr('checked');
});
</script>

<style>
    .froggy-categories-tree { padding-left:15px }
    .froggy-categories-tree li { list-style-type: none }
</style>
{/literal}
