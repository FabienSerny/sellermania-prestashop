<?php

namespace Sellermania;

/**
 * This class overloads some SoapClient methods to implement MTOM for PHP.
 *
 * As none of our services return attachments yet, this method just decodes and
 * return responses.
 *
 * @author alain tiemblo
 * @copyright (c) 2013, Sellermania
 * @uses Pear Mail_Mime http://pear.php.net/package/Mail_Mime/
 * @uses PHP SoapClient http://php.net/manual/en/class.soapclient.php
 * @version 2.0
 */
class MTOMSoapClient extends \SoapClient
{

   /**
    * Watch raw requests and responses by setting this
    * flags to true. Requests and responses user-given
    * content is encoded using CData or xml entities
    * escaping methods, so use raw XML wisely.
    *
    * @access private
    * @var bool
    */
   private $_debug = false;

   /**
    * SoapClient's __doRequest overload to add MTOM decoding
    * on responses before it is converted to objects.
    *
    * @access public
    * @param string $request
    * @param string $location
    * @param string $action
    * @param string $version
    * @param bool $one_way
    * @return array
    * @throws Exception
    */
   public function __doRequest($request, $location, $action, $version, $one_way = 0)
   {
      $this->debug('Request', $request);
      $response = parent::__doRequest($request, $location, $action, $version, $one_way);

      // If the response is already XML, there is no need to use XOP-MTOM stuffs.
      if (@simplexml_load_string($response) !== false)
      {
         $this->debug('Response', $response);
         return $response;
      }

      $params = array(
              'include_bodies' => true,
              'decode_bodies' => true,
              'decode_headers' => true,
              'crlf' => "\n",
      );

      $decoder = new \Mail_mimeDecode($response);
      $structure = $decoder->decode($params);

      // Removes unnecessary MIME tags
      $matches = array();
      if (!preg_match("!<soap:Envelope(.*)</soap:Envelope>!u", $structure->body, $matches))
      {
         throw new Exception(sprintf("Body is not a soap response : %s\n", $structure->body));
      }

      $this->debug('Response', $matches[0]);
      return $matches[0];
   }

   /**
    * This method displays the raw request and responses
    * sent to and received by the webservice server.
    *
    * @access public
    * @param string $type
    * @param string $data
    */
   public function debug($type, $data)
   {
      if ($this->_debug)
      {
         $xml = new \SimpleXMLElement($data);
         $dom = dom_import_simplexml($xml)->ownerDocument;
         $dom->formatOutput = true;
         echo "{$type}:\n";
         echo $dom->saveXML();
         echo "\n";
      }
   }

}