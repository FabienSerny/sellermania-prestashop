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
            'AMAZON.FR',
            'AMAZON.DE',
            'AMAZON.GB',
            'AMAZON.IT',
            'AMAZON.ES',
            'AMAZON.NL',
            'ATLAS4MEN.FR',
            'AUCHAN.FR',
            'BACKMARKET.FR',
            'BOULANGER.FR',
            'CDISCOUNT.FR',
            'COMPTOIRSANTE.FR',
            'CONFORAMA.FR',
            'DARTY.FR',
            'DELAMAISON.FR',
            'DOCTIPHARMA.FR',
            'EBAY.FR',
            'ELCORTEINGLES.ES',
            'EPRICE.IT',
            'FNAC.FR',
            'GALERIESLAFAYETTE.FR',
            'GAME.GB',
            'GOSPORT.FR',
            'INTERMARCHE.FR',
            'LAREDOUTE.FR',
            'LEQUIPE.FR',
            'MACWAY.FR',
            'MANOMANO.FR',
            'MATY.FR',
            'MENLOOK.FR',
            'METRO.FR',
            'NATUREETDECOUVERTE.FR',
            'OUTIZ.FR',
            'PRIVALIA.FR',
            'RAKUTEN.FR',
            'RETIF.FR',
            'RUEDUCOMMERCE.FR',
            'SHOPPINGACTIONS.FR',
            'THEBEAUTIST.FR',
            'TRUFFAUT.FR',
            'UBALDI.FR',
        );

        $this->module->sellermania_marketplaces_migration = array(
            'AMAZON.UK' => 'AMAZON.GB',
            'CDISCOUNT.COM' => 'CDISCOUNT.FR',
            'ELCORTEINGLES.FR' => 'ELCORTEINGLES.ES',
            'FNAC.COM' => 'FNAC.FR',
            'GALLERIESLAFAYETTE.FR' => 'GALERIESLAFAYETTE.FR',
            'GAME.FR' => 'GAME.GB',
            'INTERMARCHE.COM' => 'INTERMARCHE.FR',
            'OUTIZ.COM' => 'OUTIZ.FR',
            'PRICEMINISTER.FR' => 'RAKUTEN.FR',
            'THEBEAUTISTE.FR' => 'THEBEAUTIST.FR',
            'EBAY.UK' => 'EBAY.GB',
            'MACWAY.COM' => 'MACWAY.FR',
            'GOOGLE.FR' => 'SHOPPINGACTIONS.FR',
        );

        if (Configuration::get('SM_API_VERSION') == 'v3') {
            return true;
        }

        $this->module->sellermania_marketplaces = array(
            'AMAZON.CA',
            'AMAZON.COM',
            'AMAZON.DE',
            'AMAZON.ES',
            'AMAZON.FR',
            'AMAZON.IT',
            'AMAZON.UK',
            'AMAZON.NL',
            'ATLAS4MEN.FR',
            'AUCHAN.FR',
            'BOULANGER.FR',
            'CDISCOUNT.COM',
            'COMPTOIRSANTE.FR',
            'DARTY.FR',
            'DELAMAISON.FR',
            'DOCTIPHARMA.FR',
            'EBAY.DE',
            'EBAY.FR',
            'EBAY.UK',
            'ELCORTEINGLES.FR',
            'EPRICE.IT',
            'FNAC.COM',
            'GALLERIESLAFAYETTE.FR',
            'GAME.FR',
            'GOSPORT.FR',
            'LEQUIPE.FR',
            'MACWAY.COM',
            'MENLOOK.FR',
            'NATUREETDECOUVERTE.FR',
            'PRICEMINISTER.FR',
            'PRIVALIA.FR',
            'RETIF.FR',
            'RUEDUCOMMERCE.FR',
            'THEBEAUTISTE.FR',
            'OUTIZ.COM',
            'TRUFFAUT.FR',
            'MANOMANO.FR',
            'BACKMARKET.FR',
            'INTERMARCHE.COM',
            'CONFORAMA.FR',
            'LAREDOUTE.FR',
            'SHOPPINGACTIONS.FR',
            'UBALDI.FR',
        );

        $this->module->sellermania_marketplaces_delivery = array(
            'AMAZON.CA' => array('4PX', 'AAA Cooper', 'ABG', 'AFL/Fedex', 'AO', 'AT Post', 'AUSSIE_POST', 'Adrexo', 'Alljoy', 'Amazon Shipping', 'Aolde', 'Aoluk', 'Aramex', 'Arrow', 'Arrow UK', 'Australia Post', 'BJS', 'BRT', 'Belgian Post', 'Bestbuy', 'Blue Package', 'BlueDart', 'CNE Express', 'CTT Express', 'Canada Post', 'Celeritas', 'Centex', 'China Post', 'Chrono Express', 'Chronopost', 'Cititran', 'City Link', 'City Link Coliposte', 'Colissimo', 'Correios', 'Correos', 'Correos Express', 'Customer custom shipping carrier', 'DBSA', 'DHL', 'DHL DE', 'DHL Ecommerce', 'DHL Global Mail', 'DHL HB', 'DHL Home Delivery', 'DHL NL', 'DHL Parcel', 'DHL US', 'DPD', 'DPD DE', 'DPD FR', 'DPD UK', 'DPGM', 'DSV', 'DTDC', 'DX Freight', 'Db-Schenker', 'Delhivery', 'Deutsche Post', 'EUB', 'Emirates Post', 'Endopack', 'Energo', 'Energos', 'Envialia', 'Estafeta', 'Estes Express', 'FDXG', 'FEDEX_JP', 'Fastway', 'FedEx', 'FedEx SmartPost', 'Fedex Freight', 'First Flight', 'Firstmile', 'Furdeco', 'GEL Express', 'GLS', 'GO!', 'Gati', 'Gel', 'Geodis', 'GlS ES', 'GlS IT', 'Hermes', 'Hermes Einrichtungs Service', 'Hermes Logistik Gruppe', 'Hermes UK', 'Hmshb', 'Home Logistics', 'ICC', 'IDS', 'India Post', 'JCEX', 'JP Express', 'JP_EXPRESS', 'Janio', 'Japan Post', 'KNA', 'Katolec', 'Kuehne Nagel', 'La Poste', 'Lasership', 'MRW', 'NITTSU', 'NL Post', 'Nacex', 'Newgistics', 'Nexive', 'Ninjavan', 'NipponExpress', 'OSM', 'OnTrac', 'Otro', 'Overnite Express', 'PLTA', 'Panther', 'Parcel Force', 'Parcelforce', 'Parcelnet', 'Pilot Freight', 'Poste Italiane', 'Professional', 'Q Express', 'Q Xpress', 'R+L Carriers', 'RBNA', 'RPDA', 'Raben', 'Rhenus', 'Rieck', 'Roadrunner', 'Royal Mail', 'SAGAWA', 'SAIA', 'SDA', 'SEFL', 'SF Express', 'SFC', 'SagawaExpress', 'Seino', 'Seur', 'Singapore Post', 'Smartmail', 'Speedex', 'Startrack', 'Streamlite', 'Swa', 'TNT', 'TNT IT', 'TNT NL', 'TNT UK', 'TNTA', 'Target', 'Teleflora', 'Tipsa', 'Toll Global Express', 'Tourline Express', 'Tuffnells', 'UPS', 'UPS Freight', 'UPS Mail Innovations', 'UPSMI', 'USPS', 'VIR', 'Vnlin', 'WINIT', 'Wanbang', 'Watkins', 'XDP', 'XPO', 'YAMATO', 'YANWEN', 'YDH', 'YamatoTransport', 'Yellow Freight', 'Yodel', 'Yun Express', 'Yurtici'),
            'AMAZON.COM' => array('4PX', 'AAA Cooper', 'ABG', 'AFL/Fedex', 'AO', 'AT Post', 'AUSSIE_POST', 'Adrexo', 'Alljoy', 'Amazon Shipping', 'Aolde', 'Aoluk', 'Aramex', 'Arrow', 'Arrow UK', 'Australia Post', 'BJS', 'BRT', 'Belgian Post', 'Bestbuy', 'Blue Package', 'BlueDart', 'CNE Express', 'CTT Express', 'Canada Post', 'Celeritas', 'Centex', 'China Post', 'Chrono Express', 'Chronopost', 'Cititran', 'City Link', 'City Link Coliposte', 'Colissimo', 'Correios', 'Correos', 'Correos Express', 'Customer custom shipping carrier', 'DBSA', 'DHL', 'DHL DE', 'DHL Ecommerce', 'DHL Global Mail', 'DHL HB', 'DHL Home Delivery', 'DHL NL', 'DHL Parcel', 'DHL US', 'DPD', 'DPD DE', 'DPD FR', 'DPD UK', 'DPGM', 'DSV', 'DTDC', 'DX Freight', 'Db-Schenker', 'Delhivery', 'Deutsche Post', 'EUB', 'Emirates Post', 'Endopack', 'Energo', 'Energos', 'Envialia', 'Estafeta', 'Estes Express', 'FDXG', 'FEDEX_JP', 'Fastway', 'FedEx', 'FedEx SmartPost', 'Fedex Freight', 'First Flight', 'Firstmile', 'Furdeco', 'GEL Express', 'GLS', 'GO!', 'Gati', 'Gel', 'Geodis', 'GlS ES', 'GlS IT', 'Hermes', 'Hermes Einrichtungs Service', 'Hermes Logistik Gruppe', 'Hermes UK', 'Hmshb', 'Home Logistics', 'ICC', 'IDS', 'India Post', 'JCEX', 'JP Express', 'JP_EXPRESS', 'Janio', 'Japan Post', 'KNA', 'Katolec', 'Kuehne Nagel', 'La Poste', 'Lasership', 'MRW', 'NITTSU', 'NL Post', 'Nacex', 'Newgistics', 'Nexive', 'Ninjavan', 'NipponExpress', 'OSM', 'OnTrac', 'Otro', 'Overnite Express', 'PLTA', 'Panther', 'Parcel Force', 'Parcelforce', 'Parcelnet', 'Pilot Freight', 'Poste Italiane', 'Professional', 'Q Express', 'Q Xpress', 'R+L Carriers', 'RBNA', 'RPDA', 'Raben', 'Rhenus', 'Rieck', 'Roadrunner', 'Royal Mail', 'SAGAWA', 'SAIA', 'SDA', 'SEFL', 'SF Express', 'SFC', 'SagawaExpress', 'Seino', 'Seur', 'Singapore Post', 'Smartmail', 'Speedex', 'Startrack', 'Streamlite', 'Swa', 'TNT', 'TNT IT', 'TNT NL', 'TNT UK', 'TNTA', 'Target', 'Teleflora', 'Tipsa', 'Toll Global Express', 'Tourline Express', 'Tuffnells', 'UPS', 'UPS Freight', 'UPS Mail Innovations', 'UPSMI', 'USPS', 'VIR', 'Vnlin', 'WINIT', 'Wanbang', 'Watkins', 'XDP', 'XPO', 'YAMATO', 'YANWEN', 'YDH', 'YamatoTransport', 'Yellow Freight', 'Yodel', 'Yun Express', 'Yurtici'),
            'AMAZON.DE' => array('4PX', 'AAA Cooper', 'ABG', 'AFL/Fedex', 'AO', 'AT Post', 'AUSSIE_POST', 'Adrexo', 'Alljoy', 'Amazon Shipping', 'Aolde', 'Aoluk', 'Aramex', 'Arrow', 'Arrow UK', 'Australia Post', 'BJS', 'BRT', 'Belgian Post', 'Bestbuy', 'Blue Package', 'BlueDart', 'CNE Express', 'CTT Express', 'Canada Post', 'Celeritas', 'Centex', 'China Post', 'Chrono Express', 'Chronopost', 'Cititran', 'City Link', 'City Link Coliposte', 'Colissimo', 'Correios', 'Correos', 'Correos Express', 'Customer custom shipping carrier', 'DBSA', 'DHL', 'DHL DE', 'DHL Ecommerce', 'DHL Global Mail', 'DHL HB', 'DHL Home Delivery', 'DHL NL', 'DHL Parcel', 'DHL US', 'DPD', 'DPD DE', 'DPD FR', 'DPD UK', 'DPGM', 'DSV', 'DTDC', 'DX Freight', 'Db-Schenker', 'Delhivery', 'Deutsche Post', 'EUB', 'Emirates Post', 'Endopack', 'Energo', 'Energos', 'Envialia', 'Estafeta', 'Estes Express', 'FDXG', 'FEDEX_JP', 'Fastway', 'FedEx', 'FedEx SmartPost', 'Fedex Freight', 'First Flight', 'Firstmile', 'Furdeco', 'GEL Express', 'GLS', 'GO!', 'Gati', 'Gel', 'Geodis', 'GlS ES', 'GlS IT', 'Hermes', 'Hermes Einrichtungs Service', 'Hermes Logistik Gruppe', 'Hermes UK', 'Hmshb', 'Home Logistics', 'ICC', 'IDS', 'India Post', 'JCEX', 'JP Express', 'JP_EXPRESS', 'Janio', 'Japan Post', 'KNA', 'Katolec', 'Kuehne Nagel', 'La Poste', 'Lasership', 'MRW', 'NITTSU', 'NL Post', 'Nacex', 'Newgistics', 'Nexive', 'Ninjavan', 'NipponExpress', 'OSM', 'OnTrac', 'Otro', 'Overnite Express', 'PLTA', 'Panther', 'Parcel Force', 'Parcelforce', 'Parcelnet', 'Pilot Freight', 'Poste Italiane', 'Professional', 'Q Express', 'Q Xpress', 'R+L Carriers', 'RBNA', 'RPDA', 'Raben', 'Rhenus', 'Rieck', 'Roadrunner', 'Royal Mail', 'SAGAWA', 'SAIA', 'SDA', 'SEFL', 'SF Express', 'SFC', 'SagawaExpress', 'Seino', 'Seur', 'Singapore Post', 'Smartmail', 'Speedex', 'Startrack', 'Streamlite', 'Swa', 'TNT', 'TNT IT', 'TNT NL', 'TNT UK', 'TNTA', 'Target', 'Teleflora', 'Tipsa', 'Toll Global Express', 'Tourline Express', 'Tuffnells', 'UPS', 'UPS Freight', 'UPS Mail Innovations', 'UPSMI', 'USPS', 'VIR', 'Vnlin', 'WINIT', 'Wanbang', 'Watkins', 'XDP', 'XPO', 'YAMATO', 'YANWEN', 'YDH', 'YamatoTransport', 'Yellow Freight', 'Yodel', 'Yun Express', 'Yurtici'),
            'AMAZON.ES' => array('4PX', 'AAA Cooper', 'ABG', 'AFL/Fedex', 'AO', 'AT Post', 'AUSSIE_POST', 'Adrexo', 'Alljoy', 'Amazon Shipping', 'Aolde', 'Aoluk', 'Aramex', 'Arrow', 'Arrow UK', 'Australia Post', 'BJS', 'BRT', 'Belgian Post', 'Bestbuy', 'Blue Package', 'BlueDart', 'CNE Express', 'CTT Express', 'Canada Post', 'Celeritas', 'Centex', 'China Post', 'Chrono Express', 'Chronopost', 'Cititran', 'City Link', 'City Link Coliposte', 'Colissimo', 'Correios', 'Correos', 'Correos Express', 'Customer custom shipping carrier', 'DBSA', 'DHL', 'DHL DE', 'DHL Ecommerce', 'DHL Global Mail', 'DHL HB', 'DHL Home Delivery', 'DHL NL', 'DHL Parcel', 'DHL US', 'DPD', 'DPD DE', 'DPD FR', 'DPD UK', 'DPGM', 'DSV', 'DTDC', 'DX Freight', 'Db-Schenker', 'Delhivery', 'Deutsche Post', 'EUB', 'Emirates Post', 'Endopack', 'Energo', 'Energos', 'Envialia', 'Estafeta', 'Estes Express', 'FDXG', 'FEDEX_JP', 'Fastway', 'FedEx', 'FedEx SmartPost', 'Fedex Freight', 'First Flight', 'Firstmile', 'Furdeco', 'GEL Express', 'GLS', 'GO!', 'Gati', 'Gel', 'Geodis', 'GlS ES', 'GlS IT', 'Hermes', 'Hermes Einrichtungs Service', 'Hermes Logistik Gruppe', 'Hermes UK', 'Hmshb', 'Home Logistics', 'ICC', 'IDS', 'India Post', 'JCEX', 'JP Express', 'JP_EXPRESS', 'Janio', 'Japan Post', 'KNA', 'Katolec', 'Kuehne Nagel', 'La Poste', 'Lasership', 'MRW', 'NITTSU', 'NL Post', 'Nacex', 'Newgistics', 'Nexive', 'Ninjavan', 'NipponExpress', 'OSM', 'OnTrac', 'Otro', 'Overnite Express', 'PLTA', 'Panther', 'Parcel Force', 'Parcelforce', 'Parcelnet', 'Pilot Freight', 'Poste Italiane', 'Professional', 'Q Express', 'Q Xpress', 'R+L Carriers', 'RBNA', 'RPDA', 'Raben', 'Rhenus', 'Rieck', 'Roadrunner', 'Royal Mail', 'SAGAWA', 'SAIA', 'SDA', 'SEFL', 'SF Express', 'SFC', 'SagawaExpress', 'Seino', 'Seur', 'Singapore Post', 'Smartmail', 'Speedex', 'Startrack', 'Streamlite', 'Swa', 'TNT', 'TNT IT', 'TNT NL', 'TNT UK', 'TNTA', 'Target', 'Teleflora', 'Tipsa', 'Toll Global Express', 'Tourline Express', 'Tuffnells', 'UPS', 'UPS Freight', 'UPS Mail Innovations', 'UPSMI', 'USPS', 'VIR', 'Vnlin', 'WINIT', 'Wanbang', 'Watkins', 'XDP', 'XPO', 'YAMATO', 'YANWEN', 'YDH', 'YamatoTransport', 'Yellow Freight', 'Yodel', 'Yun Express', 'Yurtici'),
            'AMAZON.FR' => array('4PX', 'AAA Cooper', 'ABG', 'AFL/Fedex', 'AO', 'AT Post', 'AUSSIE_POST', 'Adrexo', 'Alljoy', 'Amazon Shipping', 'Aolde', 'Aoluk', 'Aramex', 'Arrow', 'Arrow UK', 'Australia Post', 'BJS', 'BRT', 'Belgian Post', 'Bestbuy', 'Blue Package', 'BlueDart', 'CNE Express', 'CTT Express', 'Canada Post', 'Celeritas', 'Centex', 'China Post', 'Chrono Express', 'Chronopost', 'Cititran', 'City Link', 'City Link Coliposte', 'Colissimo', 'Correios', 'Correos', 'Correos Express', 'Customer custom shipping carrier', 'DBSA', 'DHL', 'DHL DE', 'DHL Ecommerce', 'DHL Global Mail', 'DHL HB', 'DHL Home Delivery', 'DHL NL', 'DHL Parcel', 'DHL US', 'DPD', 'DPD DE', 'DPD FR', 'DPD UK', 'DPGM', 'DSV', 'DTDC', 'DX Freight', 'Db-Schenker', 'Delhivery', 'Deutsche Post', 'EUB', 'Emirates Post', 'Endopack', 'Energo', 'Energos', 'Envialia', 'Estafeta', 'Estes Express', 'FDXG', 'FEDEX_JP', 'Fastway', 'FedEx', 'FedEx SmartPost', 'Fedex Freight', 'First Flight', 'Firstmile', 'Furdeco', 'GEL Express', 'GLS', 'GO!', 'Gati', 'Gel', 'Geodis', 'GlS ES', 'GlS IT', 'Hermes', 'Hermes Einrichtungs Service', 'Hermes Logistik Gruppe', 'Hermes UK', 'Hmshb', 'Home Logistics', 'ICC', 'IDS', 'India Post', 'JCEX', 'JP Express', 'JP_EXPRESS', 'Janio', 'Japan Post', 'KNA', 'Katolec', 'Kuehne Nagel', 'La Poste', 'Lasership', 'MRW', 'NITTSU', 'NL Post', 'Nacex', 'Newgistics', 'Nexive', 'Ninjavan', 'NipponExpress', 'OSM', 'OnTrac', 'Otro', 'Overnite Express', 'PLTA', 'Panther', 'Parcel Force', 'Parcelforce', 'Parcelnet', 'Pilot Freight', 'Poste Italiane', 'Professional', 'Q Express', 'Q Xpress', 'R+L Carriers', 'RBNA', 'RPDA', 'Raben', 'Rhenus', 'Rieck', 'Roadrunner', 'Royal Mail', 'SAGAWA', 'SAIA', 'SDA', 'SEFL', 'SF Express', 'SFC', 'SagawaExpress', 'Seino', 'Seur', 'Singapore Post', 'Smartmail', 'Speedex', 'Startrack', 'Streamlite', 'Swa', 'TNT', 'TNT IT', 'TNT NL', 'TNT UK', 'TNTA', 'Target', 'Teleflora', 'Tipsa', 'Toll Global Express', 'Tourline Express', 'Tuffnells', 'UPS', 'UPS Freight', 'UPS Mail Innovations', 'UPSMI', 'USPS', 'VIR', 'Vnlin', 'WINIT', 'Wanbang', 'Watkins', 'XDP', 'XPO', 'YAMATO', 'YANWEN', 'YDH', 'YamatoTransport', 'Yellow Freight', 'Yodel', 'Yun Express', 'Yurtici'),
            'AMAZON.IT' => array('4PX', 'AAA Cooper', 'ABG', 'AFL/Fedex', 'AO', 'AT Post', 'AUSSIE_POST', 'Adrexo', 'Alljoy', 'Amazon Shipping', 'Aolde', 'Aoluk', 'Aramex', 'Arrow', 'Arrow UK', 'Australia Post', 'BJS', 'BRT', 'Belgian Post', 'Bestbuy', 'Blue Package', 'BlueDart', 'CNE Express', 'CTT Express', 'Canada Post', 'Celeritas', 'Centex', 'China Post', 'Chrono Express', 'Chronopost', 'Cititran', 'City Link', 'City Link Coliposte', 'Colissimo', 'Correios', 'Correos', 'Correos Express', 'Customer custom shipping carrier', 'DBSA', 'DHL', 'DHL DE', 'DHL Ecommerce', 'DHL Global Mail', 'DHL HB', 'DHL Home Delivery', 'DHL NL', 'DHL Parcel', 'DHL US', 'DPD', 'DPD DE', 'DPD FR', 'DPD UK', 'DPGM', 'DSV', 'DTDC', 'DX Freight', 'Db-Schenker', 'Delhivery', 'Deutsche Post', 'EUB', 'Emirates Post', 'Endopack', 'Energo', 'Energos', 'Envialia', 'Estafeta', 'Estes Express', 'FDXG', 'FEDEX_JP', 'Fastway', 'FedEx', 'FedEx SmartPost', 'Fedex Freight', 'First Flight', 'Firstmile', 'Furdeco', 'GEL Express', 'GLS', 'GO!', 'Gati', 'Gel', 'Geodis', 'GlS ES', 'GlS IT', 'Hermes', 'Hermes Einrichtungs Service', 'Hermes Logistik Gruppe', 'Hermes UK', 'Hmshb', 'Home Logistics', 'ICC', 'IDS', 'India Post', 'JCEX', 'JP Express', 'JP_EXPRESS', 'Janio', 'Japan Post', 'KNA', 'Katolec', 'Kuehne Nagel', 'La Poste', 'Lasership', 'MRW', 'NITTSU', 'NL Post', 'Nacex', 'Newgistics', 'Nexive', 'Ninjavan', 'NipponExpress', 'OSM', 'OnTrac', 'Otro', 'Overnite Express', 'PLTA', 'Panther', 'Parcel Force', 'Parcelforce', 'Parcelnet', 'Pilot Freight', 'Poste Italiane', 'Professional', 'Q Express', 'Q Xpress', 'R+L Carriers', 'RBNA', 'RPDA', 'Raben', 'Rhenus', 'Rieck', 'Roadrunner', 'Royal Mail', 'SAGAWA', 'SAIA', 'SDA', 'SEFL', 'SF Express', 'SFC', 'SagawaExpress', 'Seino', 'Seur', 'Singapore Post', 'Smartmail', 'Speedex', 'Startrack', 'Streamlite', 'Swa', 'TNT', 'TNT IT', 'TNT NL', 'TNT UK', 'TNTA', 'Target', 'Teleflora', 'Tipsa', 'Toll Global Express', 'Tourline Express', 'Tuffnells', 'UPS', 'UPS Freight', 'UPS Mail Innovations', 'UPSMI', 'USPS', 'VIR', 'Vnlin', 'WINIT', 'Wanbang', 'Watkins', 'XDP', 'XPO', 'YAMATO', 'YANWEN', 'YDH', 'YamatoTransport', 'Yellow Freight', 'Yodel', 'Yun Express', 'Yurtici'),
            'AMAZON.UK' => array('4PX', 'AAA Cooper', 'ABG', 'AFL/Fedex', 'AO', 'AT Post', 'AUSSIE_POST', 'Adrexo', 'Alljoy', 'Amazon Shipping', 'Aolde', 'Aoluk', 'Aramex', 'Arrow', 'Arrow UK', 'Australia Post', 'BJS', 'BRT', 'Belgian Post', 'Bestbuy', 'Blue Package', 'BlueDart', 'CNE Express', 'CTT Express', 'Canada Post', 'Celeritas', 'Centex', 'China Post', 'Chrono Express', 'Chronopost', 'Cititran', 'City Link', 'City Link Coliposte', 'Colissimo', 'Correios', 'Correos', 'Correos Express', 'Customer custom shipping carrier', 'DBSA', 'DHL', 'DHL DE', 'DHL Ecommerce', 'DHL Global Mail', 'DHL HB', 'DHL Home Delivery', 'DHL NL', 'DHL Parcel', 'DHL US', 'DPD', 'DPD DE', 'DPD FR', 'DPD UK', 'DPGM', 'DSV', 'DTDC', 'DX Freight', 'Db-Schenker', 'Delhivery', 'Deutsche Post', 'EUB', 'Emirates Post', 'Endopack', 'Energo', 'Energos', 'Envialia', 'Estafeta', 'Estes Express', 'FDXG', 'FEDEX_JP', 'Fastway', 'FedEx', 'FedEx SmartPost', 'Fedex Freight', 'First Flight', 'Firstmile', 'Furdeco', 'GEL Express', 'GLS', 'GO!', 'Gati', 'Gel', 'Geodis', 'GlS ES', 'GlS IT', 'Hermes', 'Hermes Einrichtungs Service', 'Hermes Logistik Gruppe', 'Hermes UK', 'Hmshb', 'Home Logistics', 'ICC', 'IDS', 'India Post', 'JCEX', 'JP Express', 'JP_EXPRESS', 'Janio', 'Japan Post', 'KNA', 'Katolec', 'Kuehne Nagel', 'La Poste', 'Lasership', 'MRW', 'NITTSU', 'NL Post', 'Nacex', 'Newgistics', 'Nexive', 'Ninjavan', 'NipponExpress', 'OSM', 'OnTrac', 'Otro', 'Overnite Express', 'PLTA', 'Panther', 'Parcel Force', 'Parcelforce', 'Parcelnet', 'Pilot Freight', 'Poste Italiane', 'Professional', 'Q Express', 'Q Xpress', 'R+L Carriers', 'RBNA', 'RPDA', 'Raben', 'Rhenus', 'Rieck', 'Roadrunner', 'Royal Mail', 'SAGAWA', 'SAIA', 'SDA', 'SEFL', 'SF Express', 'SFC', 'SagawaExpress', 'Seino', 'Seur', 'Singapore Post', 'Smartmail', 'Speedex', 'Startrack', 'Streamlite', 'Swa', 'TNT', 'TNT IT', 'TNT NL', 'TNT UK', 'TNTA', 'Target', 'Teleflora', 'Tipsa', 'Toll Global Express', 'Tourline Express', 'Tuffnells', 'UPS', 'UPS Freight', 'UPS Mail Innovations', 'UPSMI', 'USPS', 'VIR', 'Vnlin', 'WINIT', 'Wanbang', 'Watkins', 'XDP', 'XPO', 'YAMATO', 'YANWEN', 'YDH', 'YamatoTransport', 'Yellow Freight', 'Yodel', 'Yun Express', 'Yurtici'),
            'AMAZON.NL' => array('4PX', 'AAA Cooper', 'ABG', 'AFL/Fedex', 'AO', 'AT Post', 'AUSSIE_POST', 'Adrexo', 'Alljoy', 'Amazon Shipping', 'Aolde', 'Aoluk', 'Aramex', 'Arrow', 'Arrow UK', 'Australia Post', 'BJS', 'BRT', 'Belgian Post', 'Bestbuy', 'Blue Package', 'BlueDart', 'CNE Express', 'CTT Express', 'Canada Post', 'Celeritas', 'Centex', 'China Post', 'Chrono Express', 'Chronopost', 'Cititran', 'City Link', 'City Link Coliposte', 'Colissimo', 'Correios', 'Correos', 'Correos Express', 'Customer custom shipping carrier', 'DBSA', 'DHL', 'DHL DE', 'DHL Ecommerce', 'DHL Global Mail', 'DHL HB', 'DHL Home Delivery', 'DHL NL', 'DHL Parcel', 'DHL US', 'DPD', 'DPD DE', 'DPD FR', 'DPD UK', 'DPGM', 'DSV', 'DTDC', 'DX Freight', 'Db-Schenker', 'Delhivery', 'Deutsche Post', 'EUB', 'Emirates Post', 'Endopack', 'Energo', 'Energos', 'Envialia', 'Estafeta', 'Estes Express', 'FDXG', 'FEDEX_JP', 'Fastway', 'FedEx', 'FedEx SmartPost', 'Fedex Freight', 'First Flight', 'Firstmile', 'Furdeco', 'GEL Express', 'GLS', 'GO!', 'Gati', 'Gel', 'Geodis', 'GlS ES', 'GlS IT', 'Hermes', 'Hermes Einrichtungs Service', 'Hermes Logistik Gruppe', 'Hermes UK', 'Hmshb', 'Home Logistics', 'ICC', 'IDS', 'India Post', 'JCEX', 'JP Express', 'JP_EXPRESS', 'Janio', 'Japan Post', 'KNA', 'Katolec', 'Kuehne Nagel', 'La Poste', 'Lasership', 'MRW', 'NITTSU', 'NL Post', 'Nacex', 'Newgistics', 'Nexive', 'Ninjavan', 'NipponExpress', 'OSM', 'OnTrac', 'Otro', 'Overnite Express', 'PLTA', 'Panther', 'Parcel Force', 'Parcelforce', 'Parcelnet', 'Pilot Freight', 'Poste Italiane', 'Professional', 'Q Express', 'Q Xpress', 'R+L Carriers', 'RBNA', 'RPDA', 'Raben', 'Rhenus', 'Rieck', 'Roadrunner', 'Royal Mail', 'SAGAWA', 'SAIA', 'SDA', 'SEFL', 'SF Express', 'SFC', 'SagawaExpress', 'Seino', 'Seur', 'Singapore Post', 'Smartmail', 'Speedex', 'Startrack', 'Streamlite', 'Swa', 'TNT', 'TNT IT', 'TNT NL', 'TNT UK', 'TNTA', 'Target', 'Teleflora', 'Tipsa', 'Toll Global Express', 'Tourline Express', 'Tuffnells', 'UPS', 'UPS Freight', 'UPS Mail Innovations', 'UPSMI', 'USPS', 'VIR', 'Vnlin', 'WINIT', 'Wanbang', 'Watkins', 'XDP', 'XPO', 'YAMATO', 'YANWEN', 'YDH', 'YamatoTransport', 'Yellow Freight', 'Yodel', 'Yun Express', 'Yurtici'),
            'AUCHAN.FR' => array('Chronopost', 'Colis privé', 'Customer custom shipping carrier', 'DBSchenker', 'DPD', 'Fedex', 'France-Express', 'GLS', 'GLS bis - New website', 'Gefco', 'Géodis-Calberson', 'Kuehne + Nagel', 'Laposte (Colissimo,Lettre suivi)', 'Mondial Relay', 'Relais Colis', 'TNT', 'UPS'),
            'BACKMARKET.FR' => array('Amazon', 'Chronopost', 'Colissimo', 'Correos Express', 'Customer custom shipping carrier', 'DB Schenker', 'DHL', 'DPD', 'Exapaq', 'GLS - DE', 'GLS - FR', 'GLS - IT', 'GLS International', 'GLS-SPAIN', 'Geodis', 'Mondial Relay LD1', 'Mondial Relay LDS', 'NACEX', 'Socolissimo', 'TNT', 'Transporteur privé', 'UPS'),
            'BOULANGER.FR' => array('Agediss', 'Calberson', 'Chronopost', 'Colissimo', 'Customer custom shipping carrier', 'DHL Express', 'DPD', 'GLS', 'Schenker', 'TNT', 'UPS'),
            'CDISCOUNT.COM' => array('4PX', 'Asendia HK ', 'Bpost', 'CNE Express', 'China EMS (ePacket)', 'China Post', 'Chronofresh', 'Chronopost', 'Colis Privé', 'Colissimo', 'Customer custom shipping carrier', 'DB Schenker', 'DHL Deutsche Post', 'DHL Express', 'DHL ecommerce asia', 'DPD', 'FedEx', 'GEODIS', 'GLS', 'Hermes', 'La Poste - Courrier', 'Malaysia Post', 'Mondial Relay', 'PostNL', 'Relais Colis', 'SF Express', 'SFC Service', 'Singapore Post', 'TNT', 'UPS', 'WishPost', 'Yanwen', 'Yun express'),
            'CONFORAMA.FR' => array('Chronopost', 'Colis privé', 'Colissimo', 'Customer custom shipping carrier', 'DHL', 'DHL DE', 'DPD DE', 'DPD France', 'DPD Parcel', 'DPD UK', 'Dascher', 'FEDEX', 'GEFCO', 'GEODIS', 'GLS', 'HEPPNER', 'Kuehne+Nagel', 'La Poste', 'Mondial Relay (Livraison Domicile)', 'Raben', 'SCHENKER', 'SEUR', 'TDL', 'TNT', 'UPS', 'XPO Logistics'),
            'DARTY.FR' => array('4px', 'Aftership', 'B2C Europe', 'Bpost', 'Calberson Super10count', 'Cchezvous', 'Chronopost', 'Coliposte', 'Colis privé', 'Colissimo', 'Cubyn', 'Customer custom shipping carrier', 'DB Schenker', 'DHL France', 'DHL Germany', 'DNJ express', 'DPD Allemagne', 'DPD FR référence_numéro de compte', 'DPD France', 'DPD Pologne', 'DPD UK', 'Dachser', 'FEDEX', 'France Express', 'GLS', 'Gefco', 'Geodis', 'Heppner', 'Kuehne Nagel', 'La Poste', 'MRCI', 'TG Express', 'TNT', 'UPS', 'WanB Express', 'Yun Post', 'wnDirect'),
            'EBAY.DE' => array('Chronopost', 'Coliposte Domestic', 'Coliposte International', 'Customer custom shipping carrier', 'DHL', 'UPS'),
            'EBAY.FR' => array('Chronopost', 'Coliposte Domestic', 'Coliposte International', 'Customer custom shipping carrier', 'DHL', 'UPS'),
            'EBAY.UK' => array('Chronopost', 'Coliposte Domestic', 'Coliposte International', 'Customer custom shipping carrier', 'DHL', 'UPS'),
            'ELCORTEINGLES.FR' => array('ASM', 'AZKAR', 'CHRONO EXPRESS', 'CHRONOPOST', 'COLISSIMO', 'CORREOS', 'CORREOS EXPRESS', 'Customer custom shipping carrier', 'DACHSER', 'DECOEXSA', 'DHL ESPAÑA', 'DHL Expres ES', 'DHL Expres PT', 'DHL PT', 'DPD ESP', 'DPD PT', 'ENVIALIA', 'GLS', 'MAIL BOXES ETC', 'MRW', 'NACEX', 'SENDING', 'SEUR', 'TIPSA', 'TNT', 'TOURLINE', 'TXT', 'UPS', 'ZELERIS'),
            'EPRICE.IT' => array('4PX', 'AMATI JR', 'Arco Spedizioni', 'BRT ID collo cliente', 'BRT numero di spedizione', 'BRT riferimento mittente', 'CNE Express', 'CORREOS', 'Chronopost International (FR)', 'Citypost HRP', 'Click&Quick', 'Customer custom shipping carrier', 'DHL', 'DHL.DE', 'DPD', 'Delivery Agency Chain (DAC)', 'ENERGO Logistics', 'FAST WORLD LOGISTIC', 'FEDEX', 'FERCAM', 'GLS ID Collo', 'GLS numero di spedizione', 'GLS spedizione internazionale', 'Installo', 'La Poste (FR)', 'NEXIVE', 'Poste italiane', 'Royal Mail', 'SDA', 'SEUR', 'SGT Flyer', 'TECNOTRANS', 'TNT', 'UPS', 'Ydh', 'Yun Express', 'Züst Ambrosetti'),
            'FNAC.COM' => array('007EX', '17 Post Service', '17TRACK', '2GO', '2ebox', '360 Lion Express', '4-72 Entregando', '4PX', '99minutos', 'A Duie Pyle', 'AAA Cooper', 'AB Custom Group', 'ABF Freight', 'ABX Express', 'ACS Courier', 'ACS Worldwide Express', 'ADRIATIC GROUP SRL', 'ADSOne', 'AIR21', 'ALLJOY SUPPLY CHAIN CO., LTD', 'ANSERX', 'ANSERX Logistics', 'AO Logistics', 'APC Overnight', 'APC Overnight Consignment Number', 'APC Postal Logistics', 'APG eCommerce Solutions Ltd.', 'ARK Logistics', 'ASIGNA', 'ASM', 'ATS', 'AUTORADIO', 'Ader', 'Adicional Logistics', 'Adrexo', 'Aeronet', 'Aersure', 'AfterShip', 'Aftership Mainway', 'Agility', 'Air Canada', 'Airmee', 'Airpak Express', 'Airspeed International Corporati', 'AlfaTrex', 'AliExpress Standard Shipping', 'Alianca', 'Allied Express', 'Allied Express (FTP)', 'Always Express', 'Amazon', 'Amazon Logistics', 'Amazon Shipping', 'Amazon Shipping + Amazon MCF', 'An Post', 'Anjun', 'Antron Express', 'Aprisa Express', 'Aquiline', 'Aramex', 'Aramex Australia (formerly Fastw', 'Arco Spedizioni SP', 'Arrow XL', 'Asendia', 'Asendia Germany', 'Asendia HK', 'Asendia HK – Premium Service (LA', 'Asendia UK', 'Asendia USA', 'AuPost China', 'Australia Post', 'Australia Post Sftp', 'Austrian Post (Express)', 'Austrian Post (Registered)', 'Autre', 'Averitt Express', 'B&H Worldwide', 'B2C', 'B2C Europe', 'BARTOLINI', 'BJS Distribution, Storage & Cour', 'BRT Bartolini', 'BRT Bartolini(Parcel ID)', 'BRT Bartolini(Sender Reference)', 'Balaji Shipping', 'Barq', 'Belpost', 'Bert Transport', 'Best Express', 'Best Way Parcel', 'BirdSystem', 'Blue Sky', 'Blue Star', 'Bluecare Express Ltd', 'Bluedart', 'Bneed', 'Bond', 'Bonds Couriers', 'Bonnard', 'Border Express', 'BoxC', 'Boxberry', 'Bpost API', 'Bpost international', 'Brazil Correios', 'Bring', 'Brouwer Transport en Logistiek B', 'Budbee', 'Bulgarian Posts', 'Buylogic', 'C Chez Vous', 'C.H. Robinson Worldwide, Inc.', 'CAE Delivers', 'CBL Logistica', 'CBL Logistica (API)', 'CDEK', 'CDEK TR', 'CDL Last Mile', 'CEVA LOGISTICS', 'CEVA Package', 'CFL Logistics', 'CGS Express', 'CJ Century', 'CJ Century (International)', 'CJ GLS', 'CJ Korea Express', 'CJ Logistics International', 'CJ Logistics International(Hong ', 'CJ Packet', 'CJ Transnational Philippines', 'CL E-Logistics Solutions Limited', 'CMA CGM', 'CND Express', 'CNE (Diyi express)', 'CNE Express', 'CTC Express', 'Cambodia Post', 'Canada Post', 'Canpar Courier', 'Capital Transport', 'Caribou', 'Carriers', 'Carry-Flap Co.,Ltd.', 'Celeritas Transporte, S.L', 'Cello Square', 'Champion Logistics', 'China EMS (ePacket)', 'China Post', 'China Shipping', 'Chit Chats', 'Chronopost', 'Chronopost France', 'Chronopost Portugal(DPD)', 'City-Link Express', 'Clevy Links', 'Cloudwish Asia', 'Coliposte', 'Colis Privé', 'Colissimo', 'Colissimo Access', 'Colissimo Expert', 'Collect+', 'CollectCo', 'CometTech', 'Con-way Freight', 'Continental', 'Copa Airlines Courier', 'Cope Sensitive Freight', 'Corporate Couriers', 'Correos Express', 'Correos Express (API)', 'Correos de España', 'Correos de Mexico', 'Cosmetics Now', 'Courant Plus', 'Courier IT', 'Courier Plus', 'CourierPost', 'Couriers Please', 'Courrier Suivi', 'Croshot', 'Cubyn', 'Cuckoo Express', 'Cyprus Post', 'DAO365', 'DB Schenker', 'DB Schenker Sweden', 'DD Express Courier', 'DELIVERYONTIME LOGISTICS PVT LTD', 'DELNEXT', 'DEX-I', 'DHL', 'DHL 2-Mann-Handling', 'DHL Active Tracing', 'DHL Benelux', 'DHL Ecommerce', 'DHL Express (Piece ID)', 'DHL Global Forwarding', 'DHL Hong Kong', 'DHL Netherlands', 'DHL Parcel NL', 'DHL Parcel Spain', 'DHL Parcel UK', 'DHL Poland Domestic', 'DHL Spain Domestic', 'DHL Supply Chain Australia', 'DHL Supply Chain Indonesia', 'DHL eCommerce Asia', 'DHL eCommerce Asia (API)', 'DHL eCommerce US', 'DHL supply chain India', 'DHL.DE', 'DHLint', 'DHl (Reference number)', 'DMM Network', 'DMSMatrix', 'DNJ Express', 'DPD', 'DPD France', 'DPD Germany', 'DPD HK', 'DPD Ireland', 'DPD Local (Interlink Express)', 'DPD Local reference', 'DPD Poland', 'DPD Romania', 'DPD Russia', 'DPD UK', 'DPE Express', 'DPE South Africa', 'DPEX', 'DPEX China', 'DSV', 'DTDC Australia', 'DTDC Express Global PTE LTD', 'DTDC India', 'DX', 'DX (B2B)', 'DX (SFTP)', 'DX Freight', 'Dachser', 'Danske Fragtmænd', 'Dawn Wing', 'Daylight Transport, LLC', 'Dayton Freight', 'Delcart', 'Delhivery', 'Deltec Courier', 'DemandShip', 'Designer Transport', 'Destiny Transportation', 'Detrack', 'Deutsche Post', 'Deutsche Post DHL', 'Deutsche Post DHL (FTP)', 'Deutsche Post Mail', 'Dimerco Express Group', 'Direct Freight Express', 'Directlog', 'DoorDash', 'Doora Logistics', 'Dotzot', 'Ducros', 'Dynamic Logistics', 'ECMS International Logistics Co.', 'EFS (E-commerce Fulfillment Serv', 'ELTA Hellenic Post', 'ELogistica', 'EMPS Express', 'EP-Box', 'EU Fleet Solutions', 'EZship', 'Easy Mail', 'Ecargo', 'Echo', 'Ecom Express', 'Ekart', 'Emirates Post', 'Endeavour Delivery', 'Ensenda', 'Envialia', 'Equick China', 'Eshipping', 'Estafeta', 'Estes', 'Etomars', 'Eurodis', 'Euromatic', 'Exapaq', 'Expeditors', 'Expeditors API', 'Expeditors API Reference', 'Expresssale', 'FAN COURIER EXPRESS', 'FAR international', 'FERCAM Logistics & Transport', 'FMX', 'Fast World Logistic', 'Fastrak Services', 'Fasttrack', 'Fastway Ireland', 'Fastway New Zealand', 'Fastway South Africa', 'FedEx Freight', 'FedEx International MailService', 'FedEx Poland Domestic', 'FedEx UK', 'Fedex', 'Fedex Cross Border', 'Fetchr', 'First Flight Couriers', 'First Logistics', 'FirstMile', 'FitzMark', 'Flyt Express', 'Fonsen Logistics', 'France Express', 'Frete Rápido', 'GBA Services Ltd', 'GBS-Broker', 'GDEX', 'GEL Express', 'GEM Worldwide', 'GEODIS - Distribution & Express', 'GLS', 'GLS Croatia', 'GLS Czech Republic', 'GLS Denmark', 'GLS General Logistics Systems Sl', 'GLS Italy', 'GLS Logistic Systems Canada Ltd.', 'GLS Netherlands', 'GLS Slovenia', 'GLS Spain', 'GSI EXPRESS', 'GSO(GLS-USA)', 'Gati-KWE', 'Gel Express Logistik', 'Geniki Taxydromiki', 'Geodis Calberson', 'Geodis E-space', 'Giao hàng nhanh', 'Gio Express', 'GlobalTranz', 'Globegistics Inc.', 'Go Express', 'Go!Express and logistics', 'GoFly', 'GoJavas', 'Grab', 'Greyhound', 'Groupe Mazet', 'Grupo logistico Andreani', 'HCT LOGISTICS CO.LTD.', 'HEPPNER', 'HK Post', 'HSM Global', 'HUAHANG EXPRESS', 'HX Express', 'Haidaibao', 'Haidaibao (BOX)', 'Helthjem', 'Henri Ducros', 'Hermes', 'Hermes Germany', 'Hermes Italy', 'Hipshipper', 'Holisol', 'Home Delivery Solutions Ltd', 'Home Logistics', 'Hong Kong Post', 'Hrvatska Pošta', 'Hua Han Logistics', 'Hunter Express', 'Huodull', 'IDEX', 'IDS Logistics', 'IMEX Global Solutions', 'IML', 'IMX', 'IMX Mail', 'Iceland Post', 'InPost Paczkomaty', 'India Post Domestic', 'India Post International', 'Inexpost', 'Innight Express Germany GmbH (no', 'Innovel', 'Intel-Valley Supply chain (ShenZ', 'Intelipost (TMS for LATAM)', 'International Seur', 'International Seur API', 'Internet Express', 'Interparcel Australia', 'Interparcel New Zealand', 'Interparcel UK', 'Israel Post', 'Israel Post Domestic', 'Italy SDA', 'Ivoy', 'J & T Express Singapore', 'J&T EXPRESS MALAYSIA', 'J-Net', 'JCEX', 'JCO', 'JINSUNG TRADING', 'JNE', 'JNE (API)', 'JP BH Pošta', 'JS EXPRESS', 'JX', 'Jam Express', 'Janco Ecommerce', 'Janio Asia', 'Japan Post', 'Jayon Express (JEX)', 'Jersey Post', 'Jet-Ship Worldwide', 'Jocom', 'K1 Express', 'KEC', 'KGM Hub', 'KNAirlink Aerospace Domestic Net', 'KURASI', 'KWE Global', 'Kangaroo Worldwide Express', 'Kerry Express (Vietnam) Co Ltd', 'Kerry Express Hong Kong', 'Kerry Express Thailand', 'Kerry TJ Logistics', 'Kerry eCommerce', 'Kiala', 'Korea Post', 'Korea Post EMS', 'Kronos Express', 'Kua Yue Express', 'Kuehne + Nagel', 'Kuehne et Nagel', 'LHT Express', 'LPS', 'LTIAN EXP', 'LTL', 'La Poste', 'Lalamove', 'Landmark Global', 'Landmark Global Reference', 'Lao Post', 'LaserShip', 'Latvijas Pasts', 'Legion Express', 'Lettre Suivie', 'LexShip', 'Lietuvos Paštas', 'Line Clear Express & Logistics S', 'Link Bridge(BeiJing)internationa', 'Lion Parcel', 'Livrapide', 'Locus', 'Logistic Worldwide Express', 'Logwin Logistics', 'Lone Star Overnight', 'Loomis Express', 'Lotte Global Logistics', 'M Xpress Sdn Bhd', 'M&X cargo', 'MDS Collivery Pty (Ltd)', 'MISUMI Group Inc.', 'MRCI', 'MRW', 'MUDITA', 'MXE Express', 'Magyar Posta', 'Mail Boxes Etc.', 'MailAmericas', 'MailPlus', 'MailPlus (Japan)', 'Mainfreight', 'Mainway', 'Malaysia Post - Registered', 'Malaysia Post EMS / Pos Laju', 'Mara Xpress', 'Matdespatch', 'Matkahuolto', 'Megasave', 'Mena 360 (Fetchr)', 'Mexico AeroFlash', 'Mexico Redpack', 'Mexico Senda Express', 'Midland', 'Mikropakket', 'Mikropakket Belgium', 'Milkman', 'Mondial Relay', 'Mondial Relay France', 'Mondial Relay Spain(Punto Pack)', 'Mory Ducros', 'MyHermes UK', 'Mypostonline', 'NACEX', 'NACEX Spain', 'NEW ZEALAND COURIERS', 'NOX NightTimeExpress', 'NTL logistics', 'Nanjing Woyuan', 'Naqel Express', 'National Sameday', 'Nationwide Express', 'New Zealand Post', 'Neway Transport', 'Newgistics', 'Newgistics API', 'Nexive (TNT Post Italy)', 'Nhans Solutions', 'NiPost', 'Nightline', 'Nim Express', 'Ninja Van', 'Ninja Van Indonesia', 'Ninja Van Malaysia', 'Ninja Van Philippines', 'Ninja Van Thailand', 'Ninjavan Webhook', 'Norsk Global', 'Nova Poshta', 'Nova Poshta (International)', 'OCA Argentina', 'OCS ANA Group', 'OCS WORLDWIDE', 'OSM Worldwide', 'OkayParcel', 'Old Dominion Freight Line', 'Omni Parcel', 'Omniva', 'OnTrac', 'One Saar for IT', 'One World Express', 'One click delivery services', 'Other', 'PAL Express Limited', 'PARCEL ONE', 'PFC Express', 'PFL', 'PICK UPP', 'PICK UPP (Singapore)', 'PIL Logistics (China) Co., Ltd', 'PITT OHIO', 'PIXSELL LOGISTICS', 'PRESIDENT TRANSNET CORP', 'PT MGLOBAL LOGISTICS INDONESIA', 'PT Prima Multi Cipta', 'PTT Posta', 'Paack', 'Packlink', 'Packs', 'Palletways', 'Pan-Asia International', 'Pandu Logistics', 'Panther', 'Panther Order Number', 'Panther Reference', 'Paper Express', 'Paperfly Private Limited', 'Paquetexpress', 'Parcel Force', 'Parcel Post Singapore', 'Parcel2Go', 'ParcelPal', 'ParcelPoint Pty Ltd', 'Parcelled.in', 'Park N Parcel', 'PayPal Package', 'Pickupp', 'Pickupp Vietnam', 'Pilot Freight Services', 'Pitney Bowes', 'Planzer Group', 'Poczta Polska', 'Pony express', 'Portugal CTT', 'Portugal Seur', 'Post Luxembourg', 'Post Serbia', 'Post of Slovenia', 'Post56', 'PostNL', 'PostNL International', 'PostNL International 3S', 'PostNord Denmark', 'PostNord Logistics', 'PostNord Sweden', 'Poste Italiane', 'Poste Italiane Paccocelere', 'Posten Norge / Bring', 'Posten Âland', 'Posti', 'ProMed Delivery, Inc.', 'Professional Couriers', 'Prévoté', 'Purolator', 'Purolator International', 'Qingdao HKD International Logist', 'QualityPost', 'Quantium', 'Qxpress', 'RABEN', 'RAF Philippines', 'RAM', 'RETS express', 'RL Carriers', 'RPD2man Deliveries', 'RPX Indonesia', 'RPX Online', 'RRD International Logistics U.S.', 'RZY Express', 'Raben Group', 'RaidereX', 'Red Carpet Logistics', 'Red je Pakketje', 'Redur Spain', 'Relais Colis Transporteur', 'Rincos', 'Roadbull Logistics', 'Roadrunner Transport Service', 'Rocket Parcel International', 'Royal Mail', 'Russian Post', 'Ruston', 'SAILPOST', 'SAP EXPRESS', 'SDV', 'SEKO Logistics', 'SEKO Worldwide, LLC', 'SERNAM', 'SEUR', 'SF Express', 'SF Express (Webhook)', 'SF International', 'SFC', 'SFC Service', 'SHIPA', 'SHREE TIRUPATI COURIER SERVICES ', 'SKYBOX', 'SMG Direct', 'SMSA Express', 'SPOTON Logistics Pvt Ltd', 'SPRINT PACK', 'SRE Korea', 'STARKEN', 'STO Express', 'STONE3PL', 'SZENDEX', 'Safexpress', 'Sagawa', 'Saia LTL Freight', 'Saudi Post', 'Scudex Express', 'Seino', 'Sellier', 'Sending Transporte Urgente y Com', 'Sendit', 'Sendle', 'Sequoialog', 'Shanghai Aqrum Chemical Logistic', 'Shenzhen Jinghuada Logistics Co.', 'Ship It Asia', 'Shippify, Inc', 'Shippit', 'Shiptor', 'ShopfansRU LLC', 'Shree Maruti Courier Services Pv', 'Singapore Post', 'Singapore Speedpost', 'Siodemka', 'SkyNet Malaysia', 'SkyNet Worldwide Express', 'SkyNet Worldwide Express UAE', 'SkyPostal', 'Skynet World Wide Express South ', 'Skynet Worldwide Express UK', 'Slovenská pošta, a.s.', 'Smooth Couriers', 'SoColissimo', 'Sonic Transportation & Logistics', 'SortHub', 'South African Post Office', 'Southeastern Freight Lines', 'Spanish Seur', 'Spanish Seur API', 'Specialised Freight', 'Spectran', 'Spee-Dee Delivery', 'Speed Couriers', 'Speedex Courier', 'Speedy', 'Spring GDS', 'Spring Global Mail', 'Stallion Express', 'Star Track Courier', 'Star Track Express', 'StarTrack', 'Sunyou Post', 'Sutton Transport', 'Sweden Post', 'Swiship UK', 'Swiss Post', 'TAQBIN Hong Kong', 'TAQBIN Malaysia', 'TAQBIN Singapore', 'TAT Express', 'TCK Express', 'TCS', 'TDG – The Delivery Group', 'TFM Xpress', 'TForce Final Mile', 'TG Express', 'TGR', 'TIPSA', 'TNT', 'TNT Australia', 'TNT France', 'TNT Italy', 'TNT Reference', 'TNT UK', 'TNT UK Reference', 'TNT-Click Italy', 'TONDA GLOBAL', 'Tai Wan Global Business', 'Taiwan Post', 'Tazmanian Freight Systems', 'Teliway SIC Express', 'Thailand Thai Post', 'The Courier Guy', 'Tiki', 'Toll IPEC', 'Toll New Zealand', 'Toll Priority', 'Tolos', 'TopYou', 'Tophatter Express', 'Total Express', 'Tourline Express', 'TrakPak', 'Trans Kargo Internasional', 'TransMission', 'Transgroup', 'Transports Groupages Réunis', 'Trunkrs', 'Tuffnells Parcels Express', 'Tuffnells Parcels Express- Refer', 'UBI', 'UBI Smart Parcel', 'UK Mail', 'UP-express', 'UPS', 'UPS Freight', 'UPS Mail Innovations', 'UPS Pologne', 'USF Reddaway', 'USPS', 'USPS Informed Visibility - Webho', 'UkrPoshta', 'United Delivery Service, Ltd', 'Urbanfox', 'Urgent Cargus', 'VIR FOSSE', 'VIR Transport', 'VIWO IoT', 'Viaxpress', 'Vietnam Post', 'Vietnam Post EMS', 'ViettelPost', 'Vir', 'WMG Delivery', 'Wahana', 'WanbExpress', 'Watkins Shepard', 'WeDo Logistics', 'WePost Logistics', 'Weaship', 'West Bank Courier', 'Whistl', 'Wise Express', 'Wiseloads', 'WishPost', 'Wizmo', 'Wndirect', 'XDP Express', 'XDP Express Reference', 'XL Express', 'XPO logistics', 'XQ Express', 'Xend Express', 'Xpedigo', 'Xpert Delivery', 'Xpost.ph', 'XpressBees', 'YDH express', 'YRC', 'YTO Express', 'Yakit', 'Yamato Hong Kong Shipments', 'Yamato Japan', 'Yanwen', 'Yilian (Elian) Supply Chain', 'Yodel Domestic', 'Yodel International', 'Yun Express', 'YunExpress', 'Yunda Express', 'Yurtici Kargo', 'ZIM', 'ZJS International', 'ZTO Express', 'Zajil Express Company', 'Zeek', 'Zeleris', 'ZeptoExpress', 'Ziegler', 'Ziing Final Mile Inc', 'Zinc', 'Zyllem', 'aCommerce', 'alphaFAST', 'bpost', 'bpost__do_not_use', 'cPacket', 'chukou1', 'cnwangtong', 'eCourier', 'eCoutier', 'eFEx (E-Commerce Fulfillment & E', 'eParcel Korea', 'eTotal Solution Limited', 'forrun Pvt Ltd (Arpatech Venture', 'i-dika', 'i-parcel', 'leader', 'liefery', 'schenker-joyau', 'sendcloud', 'tourline', 'uShip'),
            'GOSPORT.FR' => array('Chronopost France', 'Colissimo', 'Customer custom shipping carrier', 'DHL France', 'DHL Germany', 'DPD France', 'DPD Germany', 'DPD UK', 'Dachser', 'Deutsche Post', 'GEFCO', 'GLS', 'Geodis Calberson', 'La Poste suivie', 'LandMark Global', 'Mondial Relay', 'Norsk Global Shippment', 'Premier Air Courier', 'Royal Mail', 'SCHENKER', 'TNT', 'UPS', 'WN Direct'),
            'INTERMARCHE.COM' => array('Chronopost France', 'Colissimo', 'Customer custom shipping carrier', 'DHL', 'DPD', 'Fedex', 'GLS', 'Kuehne + Nagel', 'Poste - Lettre MAX', 'TNT', 'UPS', 'Vir'),
            'MACWAY.COM' => array('Chronopost', 'Colissimo', 'Customer custom shipping carrier', 'DHL', 'DPD', 'GLS', 'La Poste', 'Mondial Relay', 'TNT', 'UPS'),
            'PRICEMINISTER.FR' => array('4PX', 'Autre', 'B2C Europe', 'Bluecare', 'Bpost', 'CNE Express', 'China EMS', 'China Post', 'Chronopost', 'Colis Prive', 'Colissimo', 'Continental', 'Courrier Suivi', 'Cubyn', 'DHL', 'DPD', 'DPD Germany', 'DPD UK', 'Exapaq', 'Fedex', 'France Express', 'GLS', 'GLS-ITALY', 'Geodis', 'Hong Kong Post', 'Kiala', 'Mondial Relay', 'Mondial Relay prépayé', 'PostNL International', 'Royal Mail', 'S.F. Express', 'SFC Service', 'Singapore Post', 'So Colissimo', 'Swiss Post', 'TNT', 'Tatex', 'TrackYourParcel', 'UPS', 'WeDo Logistics', 'Yun Express'),
            'RUEDUCOMMERCE.FR' => array('Cchezvous', 'Chronopost', 'Colis Privé', 'Colissimo', 'Customer custom shipping carrier', 'DHL', 'DPD', 'Fedex', 'GLS', 'Mondial Relay', 'TNT', 'UPS'),
            'SHOPPINGACTIONS.FR' => array('boxtal', 'bpost', 'chronopost', 'colis prive', 'colissimo', 'dpd', 'geodis', 'gls', 'la poste', 'tnt', 'ups'),
            'TRUFFAUT.FR' => array('Ascendia', 'BPOST', 'Chronopost', 'Colis Privé', 'Colissimo', 'Customer custom shipping carrier', 'DB SCHENKER', 'DHL France', 'DPD', 'DPD Predict', 'Exapaq', 'Fedex', 'France Express', 'GEODIS - Calberson', 'GLS FRANCE', 'GLS ITALY', 'Heppner', 'Kuehne + Nagel', 'La Poste (colis Pro)', 'Landmark', 'Lettre Suivie', 'Mazet', 'TNT', 'UPS'),
            'UBALDI.FR' => array('CCHEZVOUS', 'CHRONOPOST', 'COLIS PRIVE', 'COLISSIMO', 'Customer custom shipping carrier', 'DHL FRANCE', 'DPD', 'FEDEX', 'FRANCE EXPRESS', 'GLS', 'HEPPNER', 'LETTREMAX', 'MONDIAL RELAY', 'ROYAL MAIL', 'TNT', 'UPS'),
        );
    }

    public function initContext()
    {
        $this->context = Context::getContext();
        if (!Validate::isLoadedObject($this->context->currency)) {
            $this->context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        }

        if (version_compare(_PS_VERSION_, '1.7.1') >= 0) {
            $this->context = Context::getContext();
            $shop = $this->context->shop;
            if (!Validate::isLoadedObject($shop)) {
                $shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
            }
            Shop::setContext($shop::CONTEXT_SHOP, $shop->id);
            $this->context->shop = $shop;
            $this->context->cookie->id_shop = $shop->id;
        }
    }
}
