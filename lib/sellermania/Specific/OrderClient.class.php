<?php

namespace Sellermania;

/**
 * This class wraps the Sellermania OrderAPIs calls.
 *
 * @author alain tiemblo
 *
 * @see User Manual API Client.doc
 * @see EN-OrderAPIS.pdf
 * @see EN-Order cycles.pdf
 */
class OrderClient extends GenericClient
{
    // ORDER STATUS
    const STATUS_TO_DISPATCH = 1;
    const STATUS_DISPATCHED = 2;
    const STATUS_CANCELLED_CUSTOMER = 3;
    const STATUS_CANCELLED_SELLER = 4;
    const STATUS_AWAITING_DISPATCH = 5;
    const STATUS_TO_BE_CONFIRMED = 6;
    const STATUS_CONFIRMED = 9;
    const STATUS_AWAITING_CONFIRM = 10;
    const STATUS_AVAILABLE_IN_STORE = 14;
    const STATUS_PICKED_UP = 15;
    const STATUS_NON_PICKED_UP = 16;
    const STATUS_TO_BE_COLLECT_BY_LAPOSTE = 17;
    const STATUS_CANCELLED_BY_FULFILLER = 18;
    const STATUS_AZ_PENDING_ORDER = 19;
    const STATUS_DELIVERED = 20;
    const STATUS_READY_TO_SHIP = 21;

    const MKP_ABEBOOKS = 'ABEBOOKS';
    const MKP_ALLTRICKS = 'ALLTRICKS';
    const MKP_AMAZON = 'AMAZON';
    const MKP_AMAZONVENDOR = 'AMAZONVENDOR';
    const MKP_ANIMALIS = 'ANIMALIS';
    const MKP_ARAMISAUTO = 'ARAMISAUTO';
    const MKP_ATLAS4MEN = 'ATLAS4MEN';
    const MKP_AUCHAN = 'AUCHAN';
    const MKP_BACKMARKET = 'BACKMARKET';
    const MKP_BHV = 'BHV';
    const MKP_BOULANGER = 'BOULANGER';
    const MKP_BRICOPRIVE = 'BRICOPRIVE';
    const MKP_BULEVIP = 'BULEVIP';
    const MKP_BUT = 'BUT';
    const MKP_CARREFOUR = 'CARREFOUR';
    const MKP_CDISCOUNT = 'CDISCOUNT';
    const MKP_COMPTOIRSANTE = 'COMPTOIRSANTE';
    const MKP_CONFORAMA = 'CONFORAMA';
    const MKP_CONRAD = 'CONRAD';
    const MKP_DARTY = 'DARTY';
    const MKP_DELAMAISON = 'DELAMAISON';
    const MKP_DOCTIPHARMA = 'DOCTIPHARMA';
    const MKP_EBAY = 'EBAY';
    const MKP_ELCORTEINGLES = 'ELCORTEINGLES';
    const MKP_EPRICE = 'EPRICE';
    const MKP_FNAC = 'FNAC';
    const MKP_GALERIESLAFAYETTE = 'GALERIESLAFAYETTE';
    const MKP_GAME = 'GAME';
    const MKP_GOSPORT = 'GOSPORT';
    const MKP_GREENWEEZ = 'GREENWEEZ';
    const MKP_INTERMARCHE = 'INTERMARCHE';
    const MKP_LAPOSTE = 'LAPOSTE';
    const MKP_LAREDOUTE = 'LAREDOUTE';
    const MKP_LDLC = 'LDLC';
    const MKP_LECLERC = 'LECLERC';
    const MKP_LEQUIPE = 'LEQUIPE';
    const MKP_LEROYMERLIN = 'LEROYMERLIN';
    const MKP_LYRECO = 'LYRECO';
    const MKP_MACWAY = 'MACWAY';
    const MKP_MAISONSDUMONDE = 'MAISONSDUMONDE';
    const MKP_MANOMANO = 'MANOMANO';
    const MKP_MATY = 'MATY';
    const MKP_MENLOOK = 'MENLOOK';
    const MKP_METRO = 'METRO';
    const MKP_NATUREETDECOUVERTE = 'NATUREETDECOUVERTE';
    const MKP_OUTIZ = 'OUTIZ';
    const MKP_PCCOMPONENTES = 'PCCOMPONENTES';
    const MKP_PIXMANIA = 'PIXMANIA';
    const MKP_PRIVALIA = 'PRIVALIA';
    const MKP_QUELBONPLAN = 'QUELBONPLAN';
    const MKP_RAKUTEN = 'RAKUTEN';
    const MKP_REFURBED = 'REFURBED';
    const MKP_RETIF = 'RETIF';
    const MKP_RUEDUCOMMERCE = 'RUEDUCOMMERCE';
    const MKP_SHOPPINGACTIONS = 'SHOPPINGACTIONS';
    const MKP_SPARTOO = 'SPARTOO';
    const MKP_THEBEAUTIST = 'THEBEAUTIST';
    const MKP_TRUFFAUT = 'TRUFFAUT';
    const MKP_UBALDI = 'UBALDI';
    const MKP_SHOWROOMPRIVE = 'SHOWROOMPRIVE';
    const MKP_VANDENBORRE = 'VANDENBORRE';
    const MKP_VIDAXL = 'VIDAXL';
    const MKP_WORTEN = 'WORTEN';
    const MKP_ZALANDO = 'ZALANDO';

    // MARKETPLACES ALLOWED
    const MKP_ABEBOOKS_FR = 'ABEBOOKS.FR';
    const MKP_ALLTRICKS_FR = 'ALLTRICKS.FR';
    const MKP_AMAZONVENDOR_FR = 'AMAZONVENDOR.FR';
    const MKP_AMAZON_AU = 'AMAZON.AU';
    const MKP_AMAZON_BE = 'AMAZON.BE';
    const MKP_AMAZON_CA = 'AMAZON.CA';
    const MKP_AMAZON_DE = 'AMAZON.DE';
    const MKP_AMAZON_ES = 'AMAZON.ES';
    const MKP_AMAZON_FR = 'AMAZON.FR';
    const MKP_AMAZON_GB = 'AMAZON.GB';
    const MKP_AMAZON_IN = 'AMAZON.IN';
    const MKP_AMAZON_IT = 'AMAZON.IT';
    const MKP_AMAZON_JP = 'AMAZON.JP';
    const MKP_AMAZON_MX = 'AMAZON.MX';
    const MKP_AMAZON_NL = 'AMAZON.NL';
    const MKP_AMAZON_PL = 'AMAZON.PL';
    const MKP_AMAZON_SA = 'AMAZON.SA';
    const MKP_AMAZON_SE = 'AMAZON.SE';
    const MKP_AMAZON_SG = 'AMAZON.SG';
    const MKP_AMAZON_TR = 'AMAZON.TR';
    const MKP_AMAZON_US = 'AMAZON.US';
    const MKP_ANIMALIS_FR = 'ANIMALIS.FR';
    const MKP_ARAMISAUTO_FR = 'ARAMISAUTO.FR';
    const MKP_ATLAS4MEN_FR = 'ATLAS4MEN.FR';
    const MKP_AUCHAN_FR = 'AUCHAN.FR';
    const MKP_BACKMARKET_AT = 'BACKMARKET.AT';
    const MKP_BACKMARKET_BE = 'BACKMARKET.BE';
    const MKP_BACKMARKET_DE = 'BACKMARKET.DE';
    const MKP_BACKMARKET_ES = 'BACKMARKET.ES';
    const MKP_BACKMARKET_FI = 'BACKMARKET.FI';
    const MKP_BACKMARKET_FR = 'BACKMARKET.FR';
    const MKP_BACKMARKET_GB = 'BACKMARKET.GB';
    const MKP_BACKMARKET_GR = 'BACKMARKET.GR';
    const MKP_BACKMARKET_IE = 'BACKMARKET.IE';
    const MKP_BACKMARKET_IT = 'BACKMARKET.IT';
    const MKP_BACKMARKET_NL = 'BACKMARKET.NL';
    const MKP_BACKMARKET_PT = 'BACKMARKET.PT';
    const MKP_BACKMARKET_SE = 'BACKMARKET.SE';
    const MKP_BACKMARKET_SK = 'BACKMARKET.SK';
    const MKP_BACKMARKET_US = 'BACKMARKET.US';
    const MKP_BHV_FR = 'BHV.FR';
    const MKP_BOULANGER_FR = 'BOULANGER.FR';
    const MKP_BRICOPRIVE_FR = 'BRICOPRIVE.FR';
    const MKP_BULEVIP_ES = 'BULEVIP.ES';
    const MKP_BUT_FR = 'BUT.FR';
    const MKP_CARREFOUR_FR = 'CARREFOUR.FR';
    const MKP_CDISCOUNT_FR = 'CDISCOUNT.FR';
    const MKP_COMPTOIRSANTE_FR = 'COMPTOIRSANTE.FR';
    const MKP_CONFORAMA_FR = 'CONFORAMA.FR';
    const MKP_CONRAD_FR = 'CONRAD.FR';
    const MKP_DARTY_FR = 'DARTY.FR';
    const MKP_DELAMAISON_FR = 'DELAMAISON.FR';
    const MKP_DOCTIPHARMA_FR = 'DOCTIPHARMA.FR';
    const MKP_EBAY_FR = 'EBAY.FR';
    const MKP_EBAY_AT = 'EBAY.AT';
    const MKP_EBAY_AU = 'EBAY.AU';
    const MKP_EBAY_BE = 'EBAY.BE';
    const MKP_EBAY_CA = 'EBAY.CA';
    const MKP_EBAY_CH = 'EBAY.CH';
    const MKP_EBAY_DE = 'EBAY.DE';
    const MKP_EBAY_ES = 'EBAY.ES';
    const MKP_EBAY_GB = 'EBAY.GB';
    const MKP_EBAY_HK = 'EBAY.HK';
    const MKP_EBAY_IE = 'EBAY.IE';
    const MKP_EBAY_IN = 'EBAY.IN';
    const MKP_EBAY_IT = 'EBAY.IT';
    const MKP_EBAY_MY = 'EBAY.MY';
    const MKP_EBAY_NL = 'EBAY.NL';
    const MKP_EBAY_PH = 'EBAY.PH';
    const MKP_EBAY_PL = 'EBAY.PL';
    const MKP_EBAY_RU = 'EBAY.RU';
    const MKP_EBAY_SG = 'EBAY.SG';
    const MKP_EBAY_US = 'EBAY.US';
    const MKP_ELCORTEINGLES_ES = 'ELCORTEINGLES.ES';
    const MKP_EPRICE_IT = 'EPRICE.IT';
    const MKP_FNAC_FR = 'FNAC.FR';
    const MKP_FNAC_ES = 'FNAC.ES';
    const MKP_FNAC_BE = 'FNAC.BE';
    const MKP_FNAC_PT = 'FNAC.PT';
    const MKP_GALERIESLAFAYETTE_FR = 'GALERIESLAFAYETTE.FR';
    const MKP_GAME_GB = 'GAME.GB';
    const MKP_GOSPORT_FR = 'GOSPORT.FR';
    const MKP_GREENWEEZ_FR = 'GREENWEEZ.FR';
    const MKP_INTERMARCHE_FR = 'INTERMARCHE.FR';
    const MKP_LAREDOUTE_FR = 'LAREDOUTE.FR';
    const MKP_LECLERC_FR = 'LECLERC.FR';
    const MKP_LEQUIPE_FR = 'LEQUIPE.FR';
    const MKP_LYRECO_FR = 'LYRECO.FR';
    const MKP_MACWAY_FR = 'MACWAY.FR';
    const MKP_MAISONSDUMONDE_FR = 'MAISONSDUMONDE.FR';
    const MKP_MANOMANO_FR = 'MANOMANO.FR';
    const MKP_MATY_FR = 'MATY.FR';
    const MKP_MENLOOK_FR = 'MENLOOK.FR';
    const MKP_METRO_FR = 'METRO.FR';
    const MKP_NATUREETDECOUVERTE_FR = 'NATUREETDECOUVERTE.FR';
    const MKP_OUTIZ_FR = 'OUTIZ.FR';
    const MKP_PCCOMPONENTES_ES = 'PCCOMPONENTES.ES';
    const MKP_PIXMANIA_FR = 'PIXMANIA.FR';
    const MKP_PRIVALIA_FR = 'PRIVALIA.FR';
    const MKP_QUELBONPLAN_FR = 'QUELBONPLAN.FR';
    const MKP_RAKUTEN_FR = 'RAKUTEN.FR';
    const MKP_REFURBED_AT = 'REFURBED.AT';
    const MKP_REFURBED_DE = 'REFURBED.DE';
    const MKP_REFURBED_DK = 'REFURBED.DK';
    const MKP_REFURBED_ES = 'REFURBED.ES';
    const MKP_REFURBED_FR = 'REFURBED.FR';
    const MKP_REFURBED_GB = 'REFURBED.GB';
    const MKP_REFURBED_IE = 'REFURBED.IE';
    const MKP_REFURBED_IT = 'REFURBED.IT';
    const MKP_REFURBED_NL = 'REFURBED.NL';
    const MKP_REFURBED_PL = 'REFURBED.PL';
    const MKP_REFURBED_SE = 'REFURBED.SE';
    const MKP_REFURBED_SI = 'REFURBED.SI';
    const MKP_REFURBED_SK = 'REFURBED.SK';
    const MKP_RETIF_FR = 'RETIF.FR';
    const MKP_RUEDUCOMMERCE_FR = 'RUEDUCOMMERCE.FR';
    const MKP_SHOPPINGACTIONS_FR = 'SHOPPINGACTIONS.FR';
    const MKP_SPARTOO_DE = 'SPARTOO.DE';
    const MKP_SPARTOO_ES = 'SPARTOO.ES';
    const MKP_SPARTOO_FR = 'SPARTOO.FR';
    const MKP_SPARTOO_IT = 'SPARTOO.IT';
    const MKP_SPARTOO_NL = 'SPARTOO.NL';
    const MKP_SPARTOO_PT = 'SPARTOO.PT';
    const MKP_THEBEAUTIST_FR = 'THEBEAUTIST.FR';
    const MKP_TRUFFAUT_FR = 'TRUFFAUT.FR';
    const MKP_UBALDI_FR = 'UBALDI.FR';
    const MKP_LEROYMERLIN_FR = 'LEROYMERLIN.FR';
    const MKP_LAPOSTE_FR = 'LAPOSTE.FR';
    const MKP_LDLC_FR = 'LDLC.FR';
    const MKP_SHOWROOMPRIVE_FR = 'SHOWROOMPRIVE.FR';
    const MKP_VANDENBORRE_BE = 'VANDENBORRE.BE';
    const MKP_VIDAXL_NL = 'VIDAXL.NL';
    const MKP_WORTEN_PT = 'WORTEN.PT';
    const MKP_ZALANDO_AT = "ZALANDO.AT";
    const MKP_ZALANDO_BE = "ZALANDO.BE";
    const MKP_ZALANDO_CH = "ZALANDO.CH";
    const MKP_ZALANDO_CZ = "ZALANDO.CZ";
    const MKP_ZALANDO_DE = "ZALANDO.DE";
    const MKP_ZALANDO_DK = "ZALANDO.DK";
    const MKP_ZALANDO_EE = "ZALANDO.EE";
    const MKP_ZALANDO_ES = "ZALANDO.ES";
    const MKP_ZALANDO_FI = "ZALANDO.FI";
    const MKP_ZALANDO_FR = "ZALANDO.FR";
    const MKP_ZALANDO_GB = "ZALANDO.GB";
    const MKP_ZALANDO_HR = "ZALANDO.HR";
    const MKP_ZALANDO_HU = "ZALANDO.HU";
    const MKP_ZALANDO_IE = "ZALANDO.IE";
    const MKP_ZALANDO_IT = "ZALANDO.IT";
    const MKP_ZALANDO_LT = "ZALANDO.LT";
    const MKP_ZALANDO_LV = "ZALANDO.LV";
    const MKP_ZALANDO_NL = "ZALANDO.NL";
    const MKP_ZALANDO_NO = "ZALANDO.NO";
    const MKP_ZALANDO_PL = "ZALANDO.PL";
    const MKP_ZALANDO_PT = "ZALANDO.PT";
    const MKP_ZALANDO_RO = "ZALANDO.RO";
    const MKP_ZALANDO_SE = "ZALANDO.SE";
    const MKP_ZALANDO_SI = "ZALANDO.SI";
    const MKP_ZALANDO_SK = "ZALANDO.SK";

    /**
     * This method let you get your orders between 2 dates. Dates are given as
     * \DateTime instances, and if no end date is given, the current date (NOW)
     * will be used.
     *
     * This service throws an exception when a SoapFault occurs or when the
     * server is not available. To know precisely how to traverse and use the
     * response, use a var_dump on the returned array.
     *
     * @param \DateTime      $startDate
     * @param \DateTime|null $endDate
     * @param int|null       $invoiceAvailable
     *
     * @return array
     *
     * @throws Exception
     */
    public function getOrderByDate(\DateTime $startDate, \DateTime $endDate = null, $invoiceAvailable = null)
    {
        if (is_null($endDate)) {
            $endDate = $startDate;
        }

        $params = [
            'date' => $startDate->format('Y-m-d'),
            'enddate' => $endDate->format('Y-m-d'),
            'invoiceavailable' => $invoiceAvailable,
        ];

        if ($this->isValidInvoiceavailableValue($invoiceAvailable)) {
            $params['invoiceavailable'] = $invoiceAvailable;
        }

        return $this->operation('GetOrderByDate', $params);
    }

    /**
     * On Sellermania, each of your orders has a unique ID. This method let
     * you recover one order by using this id.
     *
     * Take care about type casting / juggling : if your order id begins with 0,
     * it should appear, so your variable should keep its string type and must
     * not be converted to integer / float.
     *
     * This service throws an exception when a SoapFault occurs or when the
     * server is not available. To know precisely how to traverse and use the
     * response, use a var_dump on the returned array.
     *
     * @param string $orderId
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getOrderById($orderId)
    {
        $params = [
            'orderId' => $orderId,
        ];

        return $this->operation('GetOrderById', $params);
    }

    /**
     * This method let you search orders using a status name, an interval
     * and a marketplace.
     *
     * This class has all allowed order status and marketplaces defined
     * as constants, you'd better use them instead of typing numbers /
     * strings.
     *
     * If the end date is missing, the current date (NOW) will be used. All
     * dates should be instances of \DateTime class.
     *
     * If the marketplace is missing, no marketplace filter will be applied
     * so all orders will be returned.
     *
     * This service throws an exception when a SoapFault occurs or when the
     * server is not available. To know precisely how to traverse and use the
     * response, use a var_dump on the returned array.
     *
     * @param int            $orderStatus
     * @param string         $marketplace
     * @param \DateTime|null $endDate
     * @param int|null       $invoiceAvailable
     *
     * @return array
     *
     * @throws Exception
     */
    public function getOrderByStatus($orderStatus, $marketplace, \DateTime $startDate, \DateTime $endDate = null, $invoiceAvailable = null)
    {
        if (!$this->_isValidOrderStatus($orderStatus)) {
            throw new Exception(sprintf('Invalid order status given: %s. See available constants for details.', $orderStatus));
        }

        if ((!is_null($marketplace)) && (!Marketplace::isValid($marketplace))) {
            throw new Exception(sprintf('Invalid marketplace given: %s. See available constants for details.', $marketplace));
        }

        if (is_null($endDate)) {
            $endDate = $startDate;
        }

        $params = [
            'status' => $orderStatus,
            'marketplace' => $marketplace,
            'date' => $startDate->format('Y-m-d'),
            'enddate' => $endDate->format('Y-m-d'),
        ];

        if ($this->isValidInvoiceavailableValue($invoiceAvailable)) {
            $params['invoiceavailable'] = $invoiceAvailable;
        }

        return $this->operation('GetOrderByStatus', $params);
    }

    /**
     * This service return all the marketplaces available for your account,
     * and indicates for each of them whether you are connected or not
     *
     * @return array
     */
    public function getActiveMarketplacesList()
    {
        return $this->operation('GetActiveMarketplacesList');
    }

    /**
     * "invoiceavailable" is an optional parameter.
     * If set it should be restricted to [O,1] values only.
     *  - 1 matches orders that can be invoiced
     *  - 0 matches orders that cannot be invoiced
     *  - every other value (for instance "y" or "yes" or "no") will be cast as null and return orders without filter
     * If not set both cases will be fetched.
     *
     * @param $invoiceAvailable
     *
     * @return bool
     *
     * @throws Exception
     */
    private function isValidInvoiceavailableValue($invoiceAvailable)
    {
        if (!is_null($invoiceAvailable)) {
            if (!in_array($invoiceAvailable, [0, 1, '0', '1'], true)) {
                throw new Exception(sprintf('Invalid "invoiceavailable" status given: %s. 
                 Please use 1 to get orders that can be invoiced(<> pending with bill_address and price<>0) 
                 and 0 for orders that cannot be invoiced (= pending or with no bill_address or price=0).
                If not set then both cases will be fetched', $invoiceAvailable));
            }

            return true;
        }

        return false;
    }

    /**
     * This private method returns true if the given order status
     * is valid; false otherwise.
     *
     * @param string $orderStatus
     *
     * @return bool
     */
    private function _isValidOrderStatus($orderStatus)
    {
        return in_array($orderStatus,
            [
                self::STATUS_TO_DISPATCH,
                self::STATUS_DISPATCHED,
                self::STATUS_CANCELLED_CUSTOMER,
                self::STATUS_CANCELLED_SELLER,
                self::STATUS_AWAITING_DISPATCH,
                self::STATUS_TO_BE_CONFIRMED,
                self::STATUS_CONFIRMED,
                self::STATUS_AWAITING_CONFIRM,
                self::STATUS_AVAILABLE_IN_STORE,
                self::STATUS_PICKED_UP,
                self::STATUS_NON_PICKED_UP,
                self::STATUS_TO_BE_COLLECT_BY_LAPOSTE,
                self::STATUS_CANCELLED_BY_FULFILLER,
                self::STATUS_AZ_PENDING_ORDER,
                self::STATUS_DELIVERED,
                self::STATUS_READY_TO_SHIP
            ]);
    }
}
