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

require_once(dirname(__FILE__).'/../../classes/SellermaniaHelper.php');
require_once(dirname(__FILE__).'/../../classes/SellermaniaTranslator.php');

class SellermaniaGetShippingCarriersForMarketplacesController
{

    const NEEDLE_TO_INCLUDE_SHIPPING_SERVICE = "AMAZON_";

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
        $this->translator = new SellermaniaTranslator();
    }

    /**
     * Run method
     * @return string $html
     */
    public function run()
    {
        $marketplaces = SellermaniaMarketplace::getAvailableSellermaniaMarketplaces();

        $ps_carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, 5);

        $html = '<table class="table shipping-carriers-table"><tr><th scope="col"></th><th scope="col"></th><th scope="col"></th></tr>';

        foreach ($marketplaces as $marketplace) {
            $marketplace_code = str_replace('.','_', $marketplace['code']);
            $icon_name = strtolower(explode('.', $marketplace['code'])[0]);

            $this->module->loader->loadMarketplaces();
            if (isset($this->module->sellermania_marketplaces_delivery[$marketplace['code']])) {
                $sm_carriers = $this->module->sellermania_marketplaces_delivery[$marketplace['code']];
            } else {
                $sm_carriers = [];
            }

            $html .= '<tr>';
            $html .= '<td><div class="marketplace-name-wrapper"><img src="'.$this->module->sm_mp_icon_link.$icon_name.'.png" alt=""><label>' . $marketplace['code'] . '</label></div></td>';
            if ($marketplace['enabled'] ==  0) {
                $html .= '<td><small class="badge badge-danger" title="'.$this->translator->l('Marketplace included in your subscription but not connected to your Sellermania account').'">'.$this->translator->l('Not connected').'</small></td>';
            } else {
                $html .= '<td><small class="badge badge-success" title="'.$this->translator->l('Marketplace included in your subscription and connected to your Sellermania account').'">'.$this->translator->l('Connected').'</small></td>';
            }
            $html .= '<td><table class="table"><tr><th width="27%" scope="col"><label>'.$this->translator->l('Prestashop carriers').'</label></th><th width="45%" scope="col"><label>'.$this->translator->l('Marketplace carriers').'</label></th>';
            if (strpos($marketplace_code, "AMAZON_") !== false) {
                $html .= '<th scope="col"><label>'.$this->translator->l('Shipping service').'</label></th>';
            } else {
                $html .= '<th scope="col"></th>';
            }
            $html .= '</tr>';

            $sm_mkp_delivery = json_decode(Configuration::get("SM_MKP_DELIVERY_".$marketplace_code), true);
            foreach ($ps_carriers as $ps_carrier) {
                $html .= '<tr><td>'.$ps_carrier['name'].'</td><td>';
                if (!empty($sm_carriers)) {
                    $html .= '<select class="select2" name="SM_MKP_DELIVERY_'.$marketplace_code.'_'.$ps_carrier['id_carrier'].'" id="SM_MKP_DELIVERY_'.$marketplace_code.'_'.$ps_carrier['id_carrier'].'" style="width: 100%"><option value=""></option>';
                    foreach ($sm_carriers as $carrier) {
                        $selected = "";
                        if(!empty($sm_mkp_delivery)) {
                            foreach ($sm_mkp_delivery as $k => $v) {
                                $v = (array) $v;
                                if (isset($v[$ps_carrier['id_carrier']]) && $v[$ps_carrier['id_carrier']] == $carrier) {
                                    $selected = " selected";
                                }
                            }
                        }
                        $html .= '<option'.$selected.' value="'.$carrier.'">'.$carrier.'</option>';
                    }
                    $html .= '</select>';
                } else {
                    $carrier_v = "";
                    if(!empty($sm_mkp_delivery)) {
                        foreach ($sm_mkp_delivery as $k => $v) {
                            $v = (array) $v;
                            if (isset($v[$ps_carrier['id_carrier']])) {
                                $carrier_v = $v[$ps_carrier['id_carrier']];
                            }
                        }
                    }
                    $html .= '<input type="text" name="SM_MKP_DELIVERY_'.$marketplace_code.'_'.$ps_carrier['id_carrier'].'" id="SM_MKP_DELIVERY_'.$marketplace_code.'_'.$ps_carrier['id_carrier'].'" value="'.$carrier_v.'" style="width:100%;margin-top:3px">';
                }
                $html .= '</td>';
                if (strpos($marketplace_code, self::NEEDLE_TO_INCLUDE_SHIPPING_SERVICE) !== false) {
                    $service = SellermaniaHelper::getShippingServiceForMarketplace($marketplace_code, $ps_carrier['id_carrier']);
                    $html .= '<td><input type="text" name="SM_MKP_SHIPPING_SERVICE_'.$marketplace_code.'_'.$ps_carrier['id_carrier'].'" id="SM_MKP_SHIPPING_SERVICE_'.$marketplace_code.'_'.$ps_carrier['id_carrier'].'" value="'.$service.'" style="width:100%;margin-top:3px"></td>';
                } else {
                    $html .= '<td></td>';
                }
                $html .= '</tr>';
            }
            $html .= '</table></td></tr>';
        }

        $html .= '</table>';

        return $html;
    }
}

