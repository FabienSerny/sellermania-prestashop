<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/SellermaniaMarketplace.php');

class SellermaniaHelper
{
    const SHARED_ERROR_ORDER_STATES = ["4" => "3"];

    public static function createOrderStatus ($name, $color, $module_name, $language_id, $additional_information = null)
    {
        $id_order_state = null;
        $states = OrderState::getOrderStates($language_id);

        // check if order state exist
        $state_exist = false;
        foreach ($states as $state) {
            if ($name == $state['name']) {
                $state_exist = true;
                $id_order_state = $state['id_order_state'];
                break;
            }
        }

        // If the state does not exist, we create it.
        if (!$state_exist) {
            // create new order state
            $order_state = new OrderState();
            $order_state->color = $color;
            $order_state->send_email = true;
            $order_state->module_name = $module_name;
            $order_state->name = array();
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $order_state->name[$language['id_lang']] = $name;
            }

            if ($additional_information) {
                if (isset($additional_information['logable'])) {
                    $order_state->logable = $additional_information['logable'];
                }
                if (isset($additional_information['invoice'])) {
                    $order_state->invoice = $additional_information['invoice'];
                }
                if (isset($additional_information['shipped'])) {
                    $order_state->shipped = $additional_information['shipped'];
                }
                if (isset($additional_information['paid'])) {
                    $order_state->paid = $additional_information['paid'];
                }
            }

            // Update object
            $order_state->add();
            $id_order_state = $order_state->id;

            copy(_PS_MODULE_DIR_.'sellermania/logo.gif', _PS_MODULE_DIR_.'../img/os/'.$id_order_state.'.gif');
            copy(_PS_MODULE_DIR_.'sellermania/logo.gif', _PS_MODULE_DIR_.'../img/tmp/order_state_mini_'.$id_order_state.'.gif');
        }

        return $id_order_state;
    }

    public static function getSMOrderStateIdByPSOrderStateId($ps_os_id, $module, $isSending = false)
    {
        $sm_order_states = $module->sellermania_order_states;
        foreach ($sm_order_states as $sm_order_key => $sm_order_state) {
            $map_value = json_decode(Configuration::get($sm_order_key));
            if (!empty($map_value)) {
                foreach ($map_value as $os_id) {
                    if ($os_id == $ps_os_id) {
                        if ($isSending && $sm_order_state["sm_status"] == 1) {
                            return 9;
                        } else {
                            return $sm_order_state["sm_status"];
                        }
                    }
                }
            }
        }
        return false;
    }

    public static function getPSOrderStatesBySMOrderStateId($sm_os_id, $module)
    {
        // check if it's an error order state
        if (array_key_exists($sm_os_id, self::SHARED_ERROR_ORDER_STATES)) {
            $sm_os_id = self::SHARED_ERROR_ORDER_STATES[$sm_os_id];
        }
        $sm_order_states = $module->sellermania_order_states;
        foreach ($sm_order_states as $sm_order_key => $sm_order_state) {
            if ($sm_order_state["sm_status"] == $sm_os_id) {
                return json_decode(Configuration::get($sm_order_key));
            }
        }
        return false;
    }

    public static function getMPCarrierFromPSCarrierByMP($marketplace_code, $ps_carrier_id)
    {
        $marketplace_code = str_replace('.', '_', $marketplace_code);
        $map_value = json_decode(Configuration::get('SM_MKP_DELIVERY_'.$marketplace_code));
        if (is_array($map_value)) {
            foreach ($map_value as $v) {
                $v = (array) $v;
                if (isset($v[$ps_carrier_id])) {
                    return $v[$ps_carrier_id];
                }
            }
        }
        return '';
    }

    public static function getShippingServiceForMarketplace ($marketplace_code, $ps_carrier_id)
    {
        $marketplace_code = str_replace('.', '_', $marketplace_code);
        $map_value = json_decode(Configuration::get('SM_MKP_SHIPPING_SERVICE_'.$marketplace_code), true);
        if (is_array($map_value)) {
            foreach ($map_value as $v) {
                $v = (array) $v;
                if (isset($v[$ps_carrier_id])) {
                    return $v[$ps_carrier_id];
                }
            }
        }
        return '';
        }
    }
