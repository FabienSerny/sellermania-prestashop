<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class SellermaniaMarketplacesSynchronizer
{
    public static function sync ($return_marketplaces_list = false, $email = null, $token = null, $endpoint = null)
    {
        if (!$email) {
            $email = Configuration::get("SM_ORDER_EMAIL");
        }
        if (!$token) {
            $token = Configuration::get("SM_ORDER_TOKEN");
        }
        if (!$endpoint) {
            $endpoint = Configuration::get("SM_ORDER_ENDPOINT");
        }

        $is_success = true;

        try {
            $client = new Sellermania\OrderClient();
            $client->setEmail($email);
            $client->setToken($token);
            $client->setEndpoint($endpoint);

            $marketplacesList = $client->getActiveMarketplacesList();
            if ($marketplacesList["SellermaniaWs"]["Header"]["Numbers"] > 1) {
                $marketplaces = $marketplacesList["SellermaniaWs"]['GetMarketplacesList']['Marketplace'];
            } else {
                $marketplaces = [];
                $marketplaces[] = $marketplacesList["SellermaniaWs"]['GetMarketplacesList']['Marketplace'];
            }
        } catch (Exception $e) {
            $marketplaces = [];
            $is_success = false;
        }


        SellermaniaMarketplace::resetMarketplaceAvailability();
        $allMarketplaces = SellermaniaMarketplace::getAllSellermaniaMarketplaces();

        foreach ($marketplaces as $marketplace) {
            if ("true" == $marketplace["IsConnected"]) {
                $isConnected = 1;
            } else {
                $isConnected = 0;
            }
            // add new marketplaces if detected from API
            $mpExists = false;
            foreach ($allMarketplaces as $tmp_mp) {
                if ($tmp_mp['code'] === $marketplace['Code']) {
                    $mpExists = true;
                    SellermaniaMarketplace::setMarketplaceAvailabilityByCode($marketplace['Code'], 1, $isConnected);
                }
            }
            if (!$mpExists) {
                SellermaniaMarketplace::createMarketplace($marketplace['Code'], 1, $isConnected);
            }
        }

        if (!$return_marketplaces_list) {
            return $is_success;
        } else {
            return ["is_success" => $is_success, "marketplaces" => SellermaniaMarketplace::getAvailableSellermaniaMarketplaces()];
        }
    }
}