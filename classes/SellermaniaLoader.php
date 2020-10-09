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

class SellermaniaLoader
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * Load Sellermania order states
     */
    public function loadOrderStates()
    {
        $this->module->sellermania_order_states = array(
            'PS_OS_SM_ERR_CONF' => array('sm_status' => 11, 'sm_prior' => 1, 'label' => array('en' => 'Error confirmation', 'fr' => 'En erreur de confirmation'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#fa4b4c'),
            'PS_OS_SM_ERR_CANCEL_CUS' => array('sm_status' => 12, 'sm_prior' => 1, 'label' => array('en' => 'Error cancel by customer', 'fr' => 'En erreur, annulée par client'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#fa4b4c'),
            'PS_OS_SM_ERR_CANCEL_SEL' => array('sm_status' => 13, 'sm_prior' => 1, 'label' => array('en' => 'Error cancel by seller', 'fr' => 'En erreur, annulée par vendeur'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#fa4b4c'),

            'PS_OS_SM_AWAITING' => array('sm_status' => 6, 'sm_prior' => 1, 'label' => array('en' => 'To be confirmed', 'fr' => 'A confirmer'), 'logable' => false, 'invoice' => false, 'shipped' => false, 'paid' => false, 'color' => '#98c3ff'),
            'PS_OS_SM_CONFIRMED' => array('sm_status' => 9, 'sm_prior' => 0, 'label' => array('en' => 'Waiting for payment', 'fr' => 'En attente de paiement'), 'logable' => true, 'invoice' => false, 'shipped' => false, 'paid' => false, 'color' => '#98c3ff'),
            'PS_OS_SM_TO_DISPATCH' => array('sm_status' => 1, 'sm_prior' => 1, 'label' => array('en' => 'To dispatch', 'fr' => 'A expédier'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => true, 'color' => '#98c3ff'),
            'PS_OS_SM_DISPATCHED' => array('sm_status' => 2, 'sm_prior' => 0, 'label' => array('en' => 'Dispatched', 'fr' => 'Expédiée'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => true, 'color' => '#98c3ff'),

            'PS_OS_SM_CANCEL_CUS' => array('sm_status' => 3, 'sm_prior' => 0, 'label' => array('en' => 'Cancel by customer', 'fr' => 'Annulée par client'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#98c3ff'),
            'PS_OS_SM_CANCEL_SEL' => array('sm_status' => 4, 'sm_prior' => 0, 'label' => array('en' => 'Cancel by seller', 'fr' => 'Annulée par vendeur'), 'logable' => true, 'invoice' => false, 'shipped' => true, 'paid' => false, 'color' => '#98c3ff'),
        );
    }

    /**
     * Load Sellermania product conditions
     */
    public function loadConditionsList()
    {
        $this->module->sellermania_conditions_list = array(
            0 => $this->module->l('Unknown', 'sellermaniadisplayadminorder'),
            1 => $this->module->l('Used - Mint', 'sellermaniadisplayadminorder'),
            2 => $this->module->l('Used - Very good', 'sellermaniadisplayadminorder'),
            3 => $this->module->l('Used - Good', 'sellermaniadisplayadminorder'),
            4 => $this->module->l('Used - Acceptable', 'sellermaniadisplayadminorder'),
            5 => $this->module->l('Collectible - Mint', 'sellermaniadisplayadminorder'),
            6 => $this->module->l('Collectible - Very good', 'sellermaniadisplayadminorder'),
            7 => $this->module->l('Collectible - Good', 'sellermaniadisplayadminorder'),
            8 => $this->module->l('Collectible - Acceptable', 'sellermaniadisplayadminorder'),
            9 => $this->module->l('Collectible - New', 'sellermaniadisplayadminorder'),
            10 => $this->module->l('Refurbished - New', 'sellermaniadisplayadminorder'),
            11 => $this->module->l('New', 'sellermaniadisplayadminorder'),
            12 => $this->module->l('New OEM', 'sellermaniadisplayadminorder'),
            13 => $this->module->l('Used - Openbox', 'sellermaniadisplayadminorder'),
            14 => $this->module->l('Refurbished - Mint', 'sellermaniadisplayadminorder'),
            15 => $this->module->l('Refurbished - Very good', 'sellermaniadisplayadminorder'),
            16 => $this->module->l('Used - Poor', 'sellermaniadisplayadminorder'),
            17 => $this->module->l('Refurbished - Good', 'sellermaniadisplayadminorder'),
            18 => $this->module->l('Refurbished - Acceptable', 'sellermaniadisplayadminorder'),
        );
    }


    public function loadMarketplaces()
    {
        $this->module->sellermania_marketplaces = array(
            'AMAZON.CA',
            'AMAZON.US',
            'AMAZON.DE',
            'AMAZON.ES',
            'AMAZON.FR',
            'AMAZON.IT',
            'AMAZON.GB',
            'AMAZON.NL',
            'ATLAS4MEN.FR',
            'AUCHAN.FR',
            'BOULANGER.FR',
            'CDISCOUNT.FR',
            'COMPTOIRSANTE.FR',
            'DARTY.FR',
            'DELAMAISON.FR',
            'DOCTIPHARMA.FR',
            'EBAY.DE',
            'EBAY.FR',
            'EBAY.GB',
            'ELCORTEINGLES.ES',
            'EPRICE.IT',
            'FNAC.COM',
            'GALERIESLAFAYETTE.FR',
            'GAME.FR',
            'GOSPORT.FR',
            'LEQUIPE.FR',
            'MACWAY.FR',
            'MENLOOK.FR',
            'NATUREETDECOUVERTE.FR',
            'RAKUTEN.FR',
            'PRIVALIA.FR',
            'RETIF.FR',
            'RUEDUCOMMERCE.FR',
            'THEBEAUTIST.FR',
            'OUTIZ.COM',
            'TRUFFAUT.FR',
            'MANOMANO.FR',
            'BACKMARKET.FR',
            'INTERMARCHE.FR',
            'CONFORAMA.FR',
            'LAREDOUTE.FR',
            'SHOPPINGACTIONS.FR',
            'UBALDI.FR',
        );




    }
}
