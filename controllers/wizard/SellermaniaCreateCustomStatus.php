<?php
/*
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
*/

/*
 * Security
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'sellermania'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SellermaniaHelper.php');

class SellermaniaCreateCustomStatusController
{
    const SUPPORTED_LANGUAGES = ['en', 'fr'];
    /**
     * Controller constructor
     */
    public function __construct($module, $dir_path, $web_path)
    {
        $this->module = $module;
        $this->web_path = $web_path;
        $this->dir_path = $dir_path;
        $this->context = Context::getContext();
        $this->ps_version = str_replace('.', '', substr(_PS_VERSION_, 0, 3));
    }

    /**
     * Run method
     * @return string $html
     */
    public function run()
    {
        $order_status_to_create = $this->module->sellermania_order_states;

        $custom_added_states = [];

        $html = '<tr><th><u>'.$this->module->l('PrestaShop order state').'</u></th><th></th><th><u>'.$this->module->l('Sellermania order state').'</u></th></tr>';

        if (!in_array($this->context->language->iso_code, self::SUPPORTED_LANGUAGES)) {
            $lang = 'en';
        } else {
            $lang = $this->context->language->iso_code;
        }

        foreach ($order_status_to_create as $sm_os) {
            $sm_os_id = $sm_os['sm_status'];
            $name = "Marketplace - ".$sm_os['label'][$lang];

            $id_order_state = SellermaniaHelper::createOrderStatus($name, '#50b4e2', $this->module->name, (int)$this->context->language->id, $sm_os);

            $html .= '
            <tr class="specific-sellermania-os">
                <td class="animate-highlight-bg"><label>'.$name.'</label></td>
                <td class="animate-highlight-bg">=</td>
                <td class="animate-highlight-bg"><select name="SM_PS_ORDER_MAP_'.$id_order_state.'" id="SM_PS_ORDER_MAP_'.$id_order_state.'"><option value=""></option>';
            foreach ($order_status_to_create as $smo) {
                $selected = '';
                if ($sm_os_id == $smo['sm_status']) {
                    $selected = ' selected';
                }
                $html .= '<option value="'.$smo['sm_status'].'"'.$selected.'>'.str_replace("Marketplace - ", "", $smo['label'][$lang]).'</option>';
            }
            $html .= '</select></td></tr>';

            $custom_added_states[] = $id_order_state;
        }

        $ps_order_states = OrderState::getOrderStates($this->context->language->id);
        foreach ($ps_order_states as $ps_order_state) {
            if (!in_array($ps_order_state["id_order_state"], $custom_added_states)) {
                $html .= '
                <tr>
                    <td><label>'.$ps_order_state["name"].'</label></td>
                    <td>=</td>
                    <td><select name="SM_PS_ORDER_MAP_'.$ps_order_state["id_order_state"].'" id="SM_PS_ORDER_MAP_'.$ps_order_state["id_order_state"].'"><option value=""></option>
                ';
                foreach ($order_status_to_create as $smo) {
                    $html .= '<option value="'.$smo["sm_status"].'">'.str_replace("Marketplace - ", "", $smo['label'][$lang]).'</option>';
                }
                $html .= '</select></td></tr>';
            }
        }

        return $html;
    }
}

