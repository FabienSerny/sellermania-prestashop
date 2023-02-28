<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/SellermaniaFieldError.php');
require_once(dirname(__FILE__).'/SellermaniaTranslator.php');
require_once(dirname(__FILE__).'/SellermaniaMarketplace.php');

class SellermaniaValidator
{

    private static $mandatoryOrderStatesToMap = [
        ["sm_status" => 6, "en_label" => "To be confirmed", "fr_label" => "A confirmer"],
        ["sm_status" => 1, "en_label" => "To dispatch", "fr_label" => "A expédier"],
        ["sm_status" => 2, "en_label" => "Dispatched", "fr_label" => "Expédiée"],
        ["sm_status" => 4, "en_label" => "Canceled", "fr_label" => "Annulée"],
    ];


    public static function validateConfiguration ($data)
    {
        $isGood = true;

        $lang = Context::getContext()->language->iso_code;
        $translator = new SellermaniaTranslator();

        SellermaniaFieldError::resetValidations();

        if (isset($data["import_orders"]) || isset($data["wizard_button"])) {
            //$is_import_ok = false;
            $marketplaces_to_import_from = [];
            $marketplaces = SellermaniaMarketplace::getAvailableSellermaniaMarketplaces();
            foreach ($marketplaces as $marketplace) {
                $marketplace_code = str_replace('.', '_', $marketplace['code']);
                if (isset($data['SM_MKP_'.$marketplace_code])) {
                    $import_mode = $data['SM_MKP_'.$marketplace_code];
                } else {
                    $import_mode = "NO";
                }
                if ($import_mode != "NO" && $marketplace['enabled'] == 1 && $marketplace['available'] == 1) {
                    //$is_import_ok = true;
                    $marketplaces_to_import_from[] = $marketplace_code;
                }
            }
            $count_marketplaces_to_import_from = count($marketplaces_to_import_from);
            /*if (!$is_import_ok) {
                SellermaniaFieldError::createFieldError("", "One marketplace should at least have an active import mode", "import-orders");
            }*/

            if (!isset($data['sm_order_import_past_days']) || !is_numeric($data['sm_order_import_past_days']) || $data['sm_order_import_past_days'] < 1) {
                SellermaniaFieldError::createFieldError("sm_order_import_past_days", $translator->l("You need to provide a positive number for last X days"), "import-orders");
                $isGood = false;
            }
            if (!isset($data['sm_order_import_limit']) || !is_numeric($data['sm_order_import_limit']) || $data['sm_order_import_limit'] < 1) {
                SellermaniaFieldError::createFieldError("sm_order_import_past_days", $translator->l("You need to provide a positive number for last X days"), "import-orders");
                $isGood = false;
            }

            $order_states_count = count(self::$mandatoryOrderStatesToMap);

            foreach ($marketplaces_to_import_from as $mkp) {
                $marketplace_configured_is_good = false;
                foreach ($data as $k => $v) {
                    if (false !== strpos($k, "MKP_DELIVERY_".$mkp."_")) {
                        if ($v != "") {
                            $marketplace_configured_is_good = true;
                            if (false !== strpos($mkp, "AMAZON_")) {
                                $ps_carrier_id = str_replace("SM_MKP_DELIVERY_".$mkp."_", "", $k);
                                if (isset($data["SM_MKP_SHIPPING_SERVICE_".$mkp."_".$ps_carrier_id]) && "" == $data["SM_MKP_SHIPPING_SERVICE_".$mkp."_".$ps_carrier_id]) {
                                    SellermaniaFieldError::createFieldError("SM_MKP_SHIPPING_SERVICE_".$mkp."_".$ps_carrier_id, $translator->l("A configured shipping carrier for")." ".str_replace('_', '.', $mkp). " ".$translator->l("should have a shipping service"), "import-orders");
                                    $isGood = false;
                                }
                            }
                        }
                    } elseif (false !== strpos($k, "PS_ORDER_MAP")) {
                        $order_status_index = -1;
                        for ($i = 0; $i < $order_states_count; $i++) {
                            if (isset(self::$mandatoryOrderStatesToMap[$i]["sm_status"]) && self::$mandatoryOrderStatesToMap[$i]["sm_status"] == $v) {
                                $order_status_index = $i;
                            }
                        }
                        if ($order_status_index !== -1) {
                            array_splice(self::$mandatoryOrderStatesToMap, $order_status_index, 1);
                        }
                    }
                }
                if (!$marketplace_configured_is_good) {
                    SellermaniaFieldError::createFieldError("", $translator->l("You are importing orders from")." ".str_replace('_', '.', $mkp)." ".$translator->l("but you haven't configured one shipping carrier at least for it"), "import-orders");
                    $isGood = false;
                }
            }

            $order_states_string = "";

            if ($count_marketplaces_to_import_from && count(self::$mandatoryOrderStatesToMap)) {
                $j = 0;
                foreach (self::$mandatoryOrderStatesToMap as $order_state) {
                    if (isset($order_state[$lang."_label"])) {
                        $label = $order_state[$lang."_label"];
                    } else {
                        $label = $order_state["en_label"];
                    }

                    if ($j !== count(self::$mandatoryOrderStatesToMap) - 1) {
                        $order_states_string .= $label.", ";
                    } else {
                        $order_states_string .= $label;
                    }
                    $j++;
                }
                SellermaniaFieldError::createFieldError("", $translator->l("The following order states need to be matched:")." ".$order_states_string."<br>".$translator->l("A default matching was suggested to fix the error, you can keep it by re-saving your configuration."), "import-orders");
                $isGood = false;
            }
        }

        if (isset($data["export_configuration"]) || isset($data["wizard_button"])) {
            if (isset($data['sm_product_to_include_in_feed']) && "" === $data['sm_product_to_include_in_feed']) {
                SellermaniaFieldError::createFieldError("sm_product_to_include", $translator->l("You need to choose the way you want to export your catalog"), "export-catalog");
                $isGood = false;
            } else {
                if (isset($data['sm_product_to_include_in_feed']) && "without_oos" === $data['sm_product_to_include_in_feed']) {
                    if ($data['sm_last_days_to_include_in_feed'] == "" || !is_numeric($data['sm_last_days_to_include_in_feed']) || $data['sm_last_days_to_include_in_feed'] < 1) {
                        SellermaniaFieldError::createFieldError("sm_last_days_to_include_in_feed", $translator->l("You need to define a positive number of days"), "export-catalog");
                        $isGood = false;
                    }
                }
            }
        }

        return $isGood;
    }
}