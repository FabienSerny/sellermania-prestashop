<?php

namespace Sellermania;

/**
 * This class wraps the Sellermania OrderConfirmAPIs calls.
 *
 * @author alain tiemblo
 * @see User Manual API Client.doc
 */
class OrderConfirmClient extends GenericClient
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

   /**
    * This method takes an array of orders, and confirm them.
    *
    * This service throws an exception when a SoapFault occurs or when the
    * server is not available. To know precisely how to traverse and use the
    * response, use a var_dump on the returned array.
    *
    * @access public
    * @param array $orderItems
    * @return array
    * @throws \Sellermania\Exception
    * @throws \SoapFault
    */
   public function confirmOrder(array $orderItems)
   {
      $params = array(
              'OrderItem' => $orderItems,
      );

      $return = $this->operation('confirmOrder', $params);
      return $return;
   }

   /**
    * Same as confirmOrder
    *
    * @access public
    * @param array $orderItems
    * @return array
    * @throws \Sellermania\Exception
    * @throws \SoapFault
    * @see self::confirmOrder
    */
   public function confirmOrders(array $orderItems)
   {
      return $this->confirmOrder($orderItems);
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

}