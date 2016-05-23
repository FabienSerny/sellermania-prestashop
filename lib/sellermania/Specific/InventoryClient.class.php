<?php

namespace Sellermania;

/**
 * This class wraps the Sellermania InventoryAPIs calls.
 *
 * @author alain tiemblo
 * @see User Manual API Client.doc
 * @see Docs\EN-InventoryAPIS.pdf
 */
class InventoryClient extends GenericClient
{

   /**
    * This method checks your credentials (email + token), and returns
    * an OK response if your client is allowed to use the other Inventory
    * API services.
    *
    * This service throws an exception when a SoapFault occurs or when the
    * server is not available. To know precisely how to traverse and use the
    * response, use a var_dump on the returned array.
    *
    * @access public
    * @return array
    * @throws \Sellermania\Exception
    * @throws \SoapFault
    */
   public function checkConnexion()
   {
      $return = $this->operation('CheckConnexion');
      return $return;
   }

   /**
    * This service checks for quantities of the given skus in your inventory.
    *
    * This service throws an exception when a SoapFault occurs or when the
    * server is not available. To know precisely how to traverse and use the
    * response, use a var_dump on the returned array.
    *
    * @access public
    * @param array $skus
    * @throws \Sellermania\Exception
    * @throws \SoapFault
    */
   public function getSkuQuantity(array $skus)
   {
      foreach ($skus as $key => $sku)
      {
         if (!is_scalar($sku))
         {
            throw new Exception(sprintf("Sku at index %d is invalid, each sku should be strings, %s given.", $key,
               gettype($sku)));
         }
         $skus[$key] = "{$sku}";
      }

      $params = array(
              'Sku' => $skus,
      );

      $return = $this->operation('GetSkuQuantity', $params);
      return $return;
   }

   /**
    * This service gives information about a sku in your inventory.
    * - Available quantity
    * - Product title
    * - Item note
    *
    * This service throws an exception when a SoapFault occurs or when the
    * server is not available. To know precisely how to traverse and use the
    * response, use a var_dump on the returned array.
    *
    * @access public
    * @param string $sku
    * @return array
    * @throws \Sellermania\Exception
    * @throws \SoapFault
    */
   public function getInventory($sku)
   {
      if (!is_scalar($sku))
      {
         throw new Exception(sprintf("Sku should be a valid string, %s given.", gettype($sku)));
      }

      $params = array(
              'Sku' => "{$sku}",
      );

      $return = $this->operation('GetInventory', $params);
      return $return;
   }

   /**
    * This service updates small parts of your items in your inventory, such as
    * the Quantity field.
    *
    * This service throws an exception when a SoapFault occurs or when the
    * server is not available. To know precisely how to traverse and use the
    * response, use a var_dump on the returned array.
    *
    * @access public
    * @param string $file
    * @return array
    * @throws \Sellermania\Exception
    * @throws \SoapFault
    */
   public function updateInventory($file)
   {
      $contents = $this->fileContents($file);

      $params = array(
              'dataHandler' => $contents,
      );

      $return = $this->operation('UpdateInventory', $params);
      return $return;
   }

}