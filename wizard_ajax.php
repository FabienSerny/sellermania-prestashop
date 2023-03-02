<?php

$config_path = dirname(__FILE__).'/../../config/config.inc.php';
$module_path = dirname(__FILE__).'/sellermania.php';

// Set _PS_ADMIN_DIR_ define
define('_PS_ADMIN_DIR_', getcwd());

// Keep going if config script is found
if (file_exists($config_path))
{
    include($config_path);
    include($module_path);

    $sm_key = Configuration::get("SM_SECRET_KEY");

    $method = Tools::getValue('method');
    $key = Tools::getValue('key');

    if ($key !== $sm_key) {
        die("Authentication Error");
    }

    $sellermania = new Sellermania();

    switch ($method) {
        case "test_api":
            $testApiResult = $sellermania->wz_testConnectionApi(Tools::getValue('email'), Tools::getValue('token'), Tools::getValue('endpoint'));
            echo $testApiResult;
            break;
        case "get_available_marketplaces":
            $availableMarketplacesResult = $sellermania->wz_getAvailableMarketplaces(Tools::getValue('email'), Tools::getValue('token'), Tools::getValue('endpoint'));
            echo $availableMarketplacesResult;
            break;
        case "get_carriers_for_marketplaces":
            $availableMarketplacesResult = $sellermania->wz_getShippingCarriersForMarketplaces();
            echo $availableMarketplacesResult;
            break;
        case "order_status":
            $id_order_status = $sellermania->wz_createCustomStatus();
            echo $id_order_status;
            break;
        default:
            exit;
    }
}
else
    die('ERROR');

