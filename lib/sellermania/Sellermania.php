<?php

/**
 * This file is the Sellermania loader.
 *
 * You should include this file to use the Sellermania Client.
 */

namespace Sellermania;

// Pear Mail_Mime dependancy
require_once(__DIR__ . '/Vendors/Mail/mimeDecode.php');
require_once(__DIR__ . '/Vendors/Mail/mimePart.php');

// Sellermania models
require_once(__DIR__ . '/Model/Marketplace.php');

// Sellermania MTOM and WS-Security customized SOAP client
require_once(__DIR__ . '/Generic/Exception.class.php');
require_once(__DIR__ . '/Generic/MTOMSoapClient.class.php');
require_once(__DIR__ . '/Generic/GenericClient.class.php');

// Sellermania specific clients
require_once(__DIR__ . '/Specific/InventoryClient.class.php');
require_once(__DIR__ . '/Specific/OrderClient.class.php');
require_once(__DIR__ . '/Specific/OrderConfirmClient.class.php');

