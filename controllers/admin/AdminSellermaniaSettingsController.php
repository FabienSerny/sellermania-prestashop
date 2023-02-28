<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminSellermaniaSettingsController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminModules').
            '&module_name=sellermania&configure=sellermania'
        );
    }
}