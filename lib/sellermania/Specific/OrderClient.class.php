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

    // MARKETPLACES ALLOWED
    const MKP_AMAZON_FR = 'AMAZON.FR';
    const MKP_AMAZON_DE = 'AMAZON.DE';
    const MKP_AMAZON_GB = 'AMAZON.GB';
    const MKP_AMAZON_IT = 'AMAZON.IT';
    const MKP_AMAZON_ES = 'AMAZON.ES';
    const MKP_AMAZON_NL = 'AMAZON.NL';
    const MKP_ATLAS4MEN_FR = 'ATLAS4MEN.FR';
    const MKP_AUCHAN_FR = 'AUCHAN.FR';
    const MKP_BACKMARKET_FR = 'BACKMARKET.FR';
    const MKP_BOULANGER_FR = 'BOULANGER.FR';
    const MKP_CDISCOUNT_FR = 'CDISCOUNT.FR';
    const MKP_COMPTOIRSANTE_FR = 'COMPTOIRSANTE.FR';
    const MKP_CONFORAMA_FR = 'CONFORAMA.FR';
    const MKP_DARTY_FR = 'DARTY.FR';
    const MKP_DELAMAISON_FR = 'DELAMAISON.FR';
    const MKP_DOCTIPHARMA_FR = 'DOCTIPHARMA.FR';
    const MKP_EBAY_FR = 'EBAY.FR';
    const MKP_ELCORTEINGLES_ES = 'ELCORTEINGLES.ES';
    const MKP_EPRICE_IT = 'EPRICE.IT';
    const MKP_FNAC_FR = 'FNAC.FR';
    const MKP_FNAC_ES = 'FNAC.ES';
    const MKP_FNAC_BE = 'FNAC.BE';
    const MKP_FNAC_PT = 'FNAC.PT';
    const MKP_GALERIESLAFAYETTE_FR = 'GALERIESLAFAYETTE.FR';
    const MKP_GAME_GB = 'GAME.GB';
    const MKP_GOSPORT_FR = 'GOSPORT.FR';
    const MKP_INTERMARCHE_FR = 'INTERMARCHE.FR';
    const MKP_LAREDOUTE_FR = 'LAREDOUTE.FR';
    const MKP_LEQUIPE_FR = 'LEQUIPE.FR';
    const MKP_MACWAY_FR = 'MACWAY.FR';
    const MKP_MANOMANO_FR = 'MANOMANO.FR';
    const MKP_MATY_FR = 'MATY.FR';
    const MKP_MENLOOK_FR = 'MENLOOK.FR';
    const MKP_METRO_FR = 'METRO.FR';
    const MKP_NATUREETDECOUVERTE_FR = 'NATUREETDECOUVERTE.FR';
    const MKP_OUTIZ_FR = 'OUTIZ.FR';
    const MKP_PRIVALIA_FR = 'PRIVALIA.FR';
    const MKP_RAKUTEN_FR = 'RAKUTEN.FR';
    const MKP_RETIF_FR = 'RETIF.FR';
    const MKP_RUEDUCOMMERCE_FR = 'RUEDUCOMMERCE.FR';
    const MKP_SHOPPINGACTIONS_FR = 'SHOPPINGACTIONS.FR';
    const MKP_THEBEAUTIST_FR = 'THEBEAUTIST.FR';
    const MKP_TRUFFAUT_FR = 'TRUFFAUT.FR';
    const MKP_UBALDI_FR = 'UBALDI.FR';
    const MKP_LEROYMERLIN_FR = 'LEROYMERLIN.FR';
    const MKP_LAPOSTE_FR = 'LAPOSTE.FR';
    const MKP_LDLC_FR = 'LDLC.FR';
    const MKP_ARAMISAUTO_FR = 'ARAMISAUTO.FR';
    const MKP_ALLTRICKS_FR = 'ALLTRICKS.FR';
    const MKP_BULEVIP_ES = 'BULEVIP.ES';
    const MKP_WORETEN_PT = 'WORTEN.PT';
    const MKP_SHOWROOMPRIVE_FR = 'SHOWROOMPRIVE.FR';
    const MKP_ANIMALIS_FR = 'ANIMALIS.FR';
    const MKP_BRICOPRIVE_FR = 'BRICOPRIVE.FR';
    const MKP_BHV_FR = 'BHV.FR';
    const MKP_LECLERC_FR = 'LECLERC.FR';
    const MKP_MAISONSDUMONDE_FR = 'MAISONSDUMONDE.FR';
    const MKP_VIDAXL_NL = 'VIDAXL.NL';
    const MKP_VANDENBORRE_BE = 'VANDENBORRE.BE';
    const MKP_BUT_FR = 'BUT.FR';

    /**
     * This method let you get your orders between 2 dates. Dates are given as
     * \DateTime instances, and if no end date is given, the current date (NOW)
     * will be used.
     *
     * This service throws an exception when a SoapFault occurs or when the
     * server is not available. To know precisely how to traverse and use the
     * response, use a var_dump on the returned array.
     *
     * @param \DateTime $endDate
     * @param null      $invoiceAvailable
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

        if ($this->getCustomerSystemName() != '') {
            $params['customer-system-name'] = $this->getCustomerSystemName();
            $params['customer-system-version'] = $this->getCustomerSystemVersion();
            $params['customer-module-version'] = $this->getCustomerModuleVersion();
        }

        if ($this->isValidInvoiceavailableValue($invoiceAvailable)) {
            $params['invoiceavailable'] = $invoiceAvailable;
        }

        return $this->operation('GetOrderByDate', $params);
    }

    /**
     * On Sellermania, each of your orders has an unique ID. This method let
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
     * @param int       $orderStatus
     * @param string    $marketplace
     * @param \DateTime $endDate
     * @param null      $invoiceAvailable
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

        if ((!is_null($marketplace)) && (!$this->_isValidMarketplace($marketplace))) {
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
            ]);
    }

    /**
     * This private method returns true if the given marketplace
     * is valid; false otherwise.
     *
     * @param string $marketplace
     *
     * @return string
     */
    private function _isValidMarketplace($marketplace)
    {
        return in_array($marketplace,
            [
                self::MKP_AMAZON_FR,
                self::MKP_AMAZON_DE,
                self::MKP_AMAZON_GB,
                self::MKP_AMAZON_IT,
                self::MKP_AMAZON_ES,
                self::MKP_AMAZON_NL,
                self::MKP_ATLAS4MEN_FR,
                self::MKP_AUCHAN_FR,
                self::MKP_BACKMARKET_FR,
                self::MKP_BOULANGER_FR,
                self::MKP_CDISCOUNT_FR,
                self::MKP_COMPTOIRSANTE_FR,
                self::MKP_CONFORAMA_FR,
                self::MKP_DARTY_FR,
                self::MKP_DELAMAISON_FR,
                self::MKP_DOCTIPHARMA_FR,
                self::MKP_EBAY_FR,
                self::MKP_ELCORTEINGLES_ES,
                self::MKP_EPRICE_IT,
                self::MKP_FNAC_FR,
                self::MKP_FNAC_BE,
                self::MKP_FNAC_ES,
                self::MKP_FNAC_PT,
                self::MKP_GALERIESLAFAYETTE_FR,
                self::MKP_GAME_GB,
                self::MKP_GOSPORT_FR,
                self::MKP_INTERMARCHE_FR,
                self::MKP_LAREDOUTE_FR,
                self::MKP_LEQUIPE_FR,
                self::MKP_MACWAY_FR,
                self::MKP_MANOMANO_FR,
                self::MKP_MATY_FR,
                self::MKP_MENLOOK_FR,
                self::MKP_METRO_FR,
                self::MKP_NATUREETDECOUVERTE_FR,
                self::MKP_OUTIZ_FR,
                self::MKP_PRIVALIA_FR,
                self::MKP_RAKUTEN_FR,
                self::MKP_RETIF_FR,
                self::MKP_RUEDUCOMMERCE_FR,
                self::MKP_SHOPPINGACTIONS_FR,
                self::MKP_THEBEAUTIST_FR,
                self::MKP_TRUFFAUT_FR,
                self::MKP_UBALDI_FR,
                self::MKP_LEROYMERLIN_FR,
                self::MKP_LAPOSTE_FR,
                self::MKP_LDLC_FR,
                self::MKP_ARAMISAUTO_FR,
                self::MKP_ALLTRICKS_FR,
                self::MKP_BULEVIP_ES,
                self::MKP_WORETEN_PT,
                self::MKP_SHOWROOMPRIVE_FR,
                self::MKP_ANIMALIS_FR,
                self::MKP_BRICOPRIVE_FR,
                self::MKP_BHV_FR,
                self::MKP_LECLERC_FR,
                self::MKP_MAISONSDUMONDE_FR,
                self::MKP_VIDAXL_NL,
                self::MKP_VANDENBORRE_BE,
                self::MKP_BUT_FR,
            ]
        );
    }
}
