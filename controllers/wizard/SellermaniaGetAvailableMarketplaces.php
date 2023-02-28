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

require_once(dirname(__FILE__).'/../../classes/SellermaniaTranslator.php');

class SellermaniaGetAvailableMarketplacesController
{
    public $translator;
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
        $client = new Sellermania\OrderClient();
        $client->setEmail($this->params['email']);
        $client->setToken($this->params['token']);
        $client->setEndpoint($this->params['endpoint']);

        $marketplacesList = $client->getActiveMarketplacesList();
        if ($marketplacesList["SellermaniaWs"]["Header"]["Numbers"] > 1) {
            $marketplaces = $marketplacesList["SellermaniaWs"]['GetMarketplacesList']['Marketplace'];
        } else {
            $marketplaces = [];
            $marketplaces[] = $marketplacesList["SellermaniaWs"]['GetMarketplacesList']['Marketplace'];
        }

        SellermaniaMarketplace::resetMarketplaceAvailability();
        $allMarketplaces = SellermaniaMarketplace::getAllSellermaniaMarketplaces();

        $html = '<table class="table"><tr><th scope="col"></th><th scope="col"></th><th scope="col"><label>Action</label></th></tr>';
        foreach ($marketplaces as $marketplace) {
            $marketplace_code = str_replace('.','_', $marketplace['Code']);
            $icon_name = strtolower(explode('.', $marketplace['Code'])[0]);

            if ("true" == $marketplace["IsConnected"]) {
                $isConnected = 1;
                $badge = '<small class="badge badge-success" title="'.$this->translator->l('Marketplace included in your subscription and connected to your Sellermania account').'">'.$this->translator->l('Connected', 'sellermania').'</small>';
                SellermaniaMarketplace::setMarketplaceAvailabilityByCode($marketplace['Code'], 1, 1);
            } else {
                $isConnected = 0;
                $badge = '<small class="badge badge-danger" title="'.$this->translator->l('Marketplace included in your subscription but not connected to your Sellermania account').'">'.$this->translator->l('Not connected').'</small>';
                SellermaniaMarketplace::setMarketplaceAvailabilityByCode($marketplace['Code'], 1, 0);
            }
            $html .= '<tr>';
            $html .= '<td><div class="marketplace-name-wrapper"><img src="'.$this->module->sm_mp_icon_link.$icon_name.'.png" alt=""><label>' . $marketplace['Code'] . '</label></div></td>';
            $html .= '<td>'.$badge.'</td>';
            $html .= '<td>';
            $html .= '<select data-connected="'.$isConnected.'" name="SM_MKP_' . $marketplace_code. '" id="SM_MKP_' . $marketplace_code. '" style="width:100%">';
            $import_mode = Configuration::get('SM_MKP_'.$marketplace_code);
            $html .= '<option value="NO"'.(($import_mode === 'NO') ? ' selected' : "").'>'.$this->translator->l('Do not import the orders', 'sellermania').'</option>';
            $html .= '<option value="AUTO"'.(($import_mode === 'AUTO') ? ' selected' : "").'>'.$this->translator->l('Import the orders with auto-confirmation (recommended)').'</option>';
            $html .= '<option value="MANUAL"'.(($import_mode === 'MANUAL') ? ' selected' : "").'>'.$this->translator->l('Import the orders without auto-confirmation').'</option>';
            $html .= '</select>';
            $html .= '</td>';
            $html .= '</tr>';

            // add new marketplaces if detected from API
            $mpExists = false;
            foreach ($allMarketplaces as $tmp_mp) {
                if ($tmp_mp['code'] === $marketplace['Code']) {
                    $mpExists = true;
                }
            }
            if (!$mpExists) {
                SellermaniaMarketplace::createMarketplace($marketplace['Code'], 1, $isConnected);
            }
        }
        $html .= '</table>';

        return $html;
    }
}

