<?php

namespace Sellermania;

/**
 * This class wraps the Sellermania OrderAPIs calls.
 *
 * @author alain tiemblo
 * @see User Manual API Client.doc
 * @see Docs\EN-OrderAPIS.pdf
 * @see Docs\EN-Order cycles.pdf
 */
class OrderClient extends GenericClient
{
   // ORDER STATUS
   const STATUS_TO_BE_CONFIRMED = 6;
   const STATUS_AWAITING_CONFIRM = 10;
   const STATUS_CONFIRMED = 9;
   const STATUS_CANCELLED_CUSTOMER = 3;
   const STATUS_CANCELLED_SELLER = 4;
   const STATUS_TO_DISPATCH = 1;
   const STATUS_AWAITING_DISPATCH = 5;
   const STATUS_DISPATCHED = 2;

   // MARKETPLACES ALLOWED
   const MKP_AMAZON_FR = 'AMAZON.FR';
   const MKP_AMAZON_COM = "AMAZON.COM";
   const MKP_AMAZON_DE = "AMAZON.DE";
   const MKP_AMAZON_UK = "AMAZON.UK";
   const MKP_AMAZON_CA = "AMAZON.CA";
   const MKP_AMAZON_IT = "AMAZON.IT";
   const MKP_AMAZON_ES = "AMAZON.ES";
   const MKP_2XMOINSCHER = "2XMOINSCHER";
   const MKP_FNAC_COM = "FNAC.COM";
   const MKP_PRICEMINISTER_FR = "PRICEMINISTER.FR";
   const MKP_EBAY_FR = "EBAY.FR";
   const MKP_EBAY_DE = "EBAY.DE";
   const MKP_EBAY_UK = "EBAY.UK";
   const MKP_PIXMANIA_FR = "PIXMANIA.FR";
   const MKP_PIXMANIA_UK = "PIXMANIA.UK";
   const MKP_PIXMANIA_DE = "PIXMANIA.DE";
   const MKP_PIXMANIA_IT = "PIXMANIA.IT";
   const MKP_PIXMANIA_ES = "PIXMANIA.ES";
   const MKP_RUEDUCOMMERCE_FR = "RUEDUCOMMERCE.FR";
   const MKP_CDISCOUNT_COM = "CDISCOUNT.COM";

   /**
    * This method let you get your orders between 2 dates. Dates are given as
    * \DateTime instances, and if no end date is given, the current date (NOW)
    * will be used.
    *
    * This service throws an exception when a SoapFault occurs or when the
    * server is not available. To know precisely how to traverse and use the
    * response, use a var_dump on the returned array.
    *
    * @access public
    * @param \DateTime $startDate
    * @param \DateTime $endDate
    * @return array
    * @throws \Sellermania\Exception
    * @throws \SoapFault
    */
   public function getOrderByDate(\DateTime $startDate, \DateTime $endDate = null)
   {
      if (is_null($endDate))
      {
         $endDate = $startDate;
      }

      $params = array(
              'date' => $startDate->format("Y-m-d"),
              'enddate' => $endDate->format("Y-m-d"),
      );

      $return = $this->operation('GetOrderByDate', $params);
      return $return;
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
    * @access public
    * @param string $orderId
    * @return array
    * @throws \Sellermania\Exception
    * @throws \SoapFault
    */
   public function getOrderById($orderId)
   {
      $params = array(
              'orderId' => $orderId,
      );

      $return = $this->operation('GetOrderById', $params);
      return $return;
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
    * @access public
    * @param int $orderStatus
    * @param string $marketplace
    * @param \DateTime $startDate
    * @param \DateTime $endDate
    * @return array
    * @throws \Sellermania\Exception
    * @throws \SoapFault
    */
   public function getOrderByStatus($orderStatus, $marketplace, \DateTime $startDate, \DateTime $endDate = null)
   {
      if (!$this->_isValidOrderStatus($orderStatus))
      {
         throw new Exception(sprintf("Invalid order status given: %s. See available constants for details.",
            $orderStatus));
      }

      if ((!is_null($marketplace)) && (!$this->_isValidMarketplace($marketplace)))
      {
         throw new Exception(sprintf("Invalid marketplace given: %s. See available constants for details.", $marketplace));
      }

      if (is_null($endDate))
      {
         $endDate = $startDate;
      }

      $params = array(
              'status' => $orderStatus,
              'marketplace' => $marketplace,
              'date' => $startDate->format("Y-m-d"),
              'enddate' => $endDate->format("Y-m-d"),
      );

      $return = $this->operation('GetOrderByStatus', $params);
      return $return;
   }

   /**
    * This private method returns true if the given order status
    * is valid; false otherwise.
    *
    * @access private
    * @param string $orderStatus
    * @return bool
    */
   private function _isValidOrderStatus($orderStatus)
   {
      return in_array($orderStatus,
         array(
              self::STATUS_TO_BE_CONFIRMED,
              self::STATUS_AWAITING_CONFIRM,
              self::STATUS_CONFIRMED,
              self::STATUS_CANCELLED_CUSTOMER,
              self::STATUS_CANCELLED_SELLER,
              self::STATUS_TO_DISPATCH,
              self::STATUS_AWAITING_DISPATCH,
              self::STATUS_DISPATCHED,
      ));
   }

   /**
    * This private method returns true if the given marketplace
    * is valid; false otherwise.
    *
    * @access private
    * @param string $marketplace
    * @return string
    */
   private function _isValidMarketplace($marketplace)
   {
      return in_array($marketplace,
         array(
              self::MKP_AMAZON_FR,
              self::MKP_AMAZON_COM,
              self::MKP_AMAZON_DE,
              self::MKP_AMAZON_UK,
              self::MKP_AMAZON_CA,
              self::MKP_AMAZON_IT,
              self::MKP_AMAZON_ES,
              self::MKP_2XMOINSCHER,
              self::MKP_FNAC_COM,
              self::MKP_PRICEMINISTER_FR,
              self::MKP_EBAY_FR,
              self::MKP_EBAY_DE,
              self::MKP_EBAY_UK,
              self::MKP_PIXMANIA_FR,
              self::MKP_PIXMANIA_UK,
              self::MKP_PIXMANIA_DE,
              self::MKP_PIXMANIA_IT,
              self::MKP_PIXMANIA_ES,
              self::MKP_RUEDUCOMMERCE_FR,
              self::MKP_CDISCOUNT_COM,
      ));
   }

}