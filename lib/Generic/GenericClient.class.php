<?php

namespace Sellermania;

/**
 * This generic client intends to be extended by Sellermania specific clients,
 * such as InventoryClient and OrdersClient.
 *
 * It does all API calls taking care of WS-Security authentification, and wrapping
 * our custom MTOM SoapClient. This is the most important class, as it fits the
 * main technical requirements used to communicate with the Sellermania API
 * server.
 *
 * @author alain tiemblo
 * @copyright (c) 2013, Sellermania
 * @uses Pear Mail_Mime http://pear.php.net/package/Mail_Mime/
 * @uses PHP SoapClient http://php.net/manual/en/class.soapclient.php
 * @version 2.0
 */
abstract class GenericClient
{

   protected $endpoint;
   protected $email;
   protected $token;
   private $client;

   /**
    * Set Sellermania API endpoint , given to you during your subscription.
    *
    * This is an URL that leads to a specific Sellermania WSDL.
    *
    * @access public
    * @param string $endpoint
    * @return \Sellermania\GenericClient
    * @throws \Sellermania\Exception
    */
   public function setEndpoint($endpoint)
   {
      if (!is_string($endpoint))
      {
         throw new Exception(sprintf("Endpoint should be a string, variable of %s type given", gettype($endpoint)));
      }
      if (!filter_var($endpoint, FILTER_VALIDATE_URL))
      {
         throw new Exception(sprintf("Endpoint should be a valid URL, %s given.", $endpoint));
      }
      $this->endpoint = $endpoint;
      return $this;
   }

   /**
    * Return the stored Sellermania Endpoint.
    *
    * @access public
    * @return string|null
    */
   public function getEndpoint()
   {
      return $this->endpoint;
   }

   /**
    * Set the email used to authentificate.
    *
    * @access public
    * @param string $email
    * @return \Sellermania\GenericClient
    * @throws \Sellermania\Exception
    */
   public function setEmail($email)
   {
      if (!is_string($email))
      {
         throw new Exception(sprintf("Email should be a string, variable of %s type given", gettype($email)));
      }
      if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      {
         throw new Exception(sprintf("Email should be a valid email, %s given.", $email));
      }
      $this->email = $email;
      return $this;
   }

   /**
    * Return the stored authentification email
    *
    * @access public
    * @return string
    */
   public function getEmail()
   {
      return $this->email;
   }

   /**
    * Set your authentification token.
    *
    * A token is something like a password to log-in and get access to the
    * Sellermania APIs.
    *
    * @access public
    * @param string $token
    * @return \Sellermania\GenericClient
    * @throws \Sellermania\Exception
    */
   public function setToken($token)
   {
      if (!is_string($token))
      {
         throw new Exception(sprintf("Token should be a string, variable of %s type given", gettype($token)));
      }
      $this->token = $token;
      return $this;
   }

   /**
    * Return the stored authentification token.
    *
    * @access public
    * @return string
    */
   public function getToken()
   {
      return $this->token;
   }

   /**
    * This method, used by specific clients, does an operation to the current endpoint.
    * The operation returns an array on success, and throws an exception on failure.
    *
    * @access protected
    * @param string $operation
    * @param array $params
    * @return array
    * @throws \Exception
    */
   protected function operation($operation, array $params = array())
   {
      ini_set('default_socket_timeout', 18000);
      $this->client = new MTOMSoapClient($this->endpoint, array(
              'trace' => true,
      ));
      $this->client->__setSoapHeaders($this->_getWSSecuritySoapHeader());
      $response = $this->client->$operation($params);
      if (is_null($response))
      {
         $sent = preg_replace("/[\n\r\t]/u", '', $this->client->__getLastRequest());
         $received = preg_replace("/[\n\r\t]/u", '', $this->client->__getLastResponse());
         throw new Exception("Response expects to match with given WSDL: sent {$sent} - received {$received} - Please contact support with this trace");
      }
      if (property_exists($response, 'sellermaniaWs'))
      {
         return $this->_convertObjectToArray($response->SellermaniaWs);
      }
      return $this->_convertObjectToArray($response);
   }

   /**
    * This method gets file contents or throws the right exception if they
    * are not available.
    *
    * @access protected
    * @param string $file
    * @return string
    * @throws Exception
    */
   protected function fileContents($file)
   {
      // File does not exists or is not available
      if (!is_file($file))
      {
         throw new Exception("File '{$file}' does not exist.");
      }

      // File is not readable (bad permissions, ...)
      if (!is_readable($file))
      {
         throw new Exception("File '{$file}' is not readable.");
      }

      // Getting file contents
      $content = file_get_contents($file);
      if ($content === false)
      {
         throw new Exception("Unable to get '{$file}': file_get_contents() failed.");
      }

      return $content;
   }

   /**
    * This method returns a new SoapHeader with the <wsse:Security> tag, that contains
    * the WS-Security authentification content.
    *
    * @access private
    * @return \SoapHeader
    */
   private function _getWSSecuritySoapHeader()
   {
      return new \SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
         'Security', new \SoapVar($this->_generateWSSecurity($this->email, $this->token), XSD_ANYXML), true
      );
   }

   /**
    * This method implements a WS-Security digest authentification for PHP.
    *
    * @access private
    * @param string $user
    * @param string $password
    * @return string
    */
   private function _generateWSSecurity($user, $password)
   {
      // Creating date using yyyy-mm-ddThh:mm:ssZ format
      $timezone = date_default_timezone_get();
      date_default_timezone_set('Europe/Paris');
      $tm_created = gmdate('Y-m-d\TH:i:s\Z', time());
      $tm_expires = gmdate('Y-m-d\TH:i:s\Z', time() + 180);
      date_default_timezone_set($timezone);

      // Generating, packing and encoding a random number
      $simple_nonce = mt_rand();
      $encoded_nonce = base64_encode(pack('H*', $simple_nonce));

      // Compiling WSS string
      $passdigest = base64_encode(pack('H*',
            sha1(pack('H*', $simple_nonce) . pack('a*', $tm_created) . pack('a*', $password))));

      // Initializing namespaces
      $ns_wsse = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
      $ns_wsu = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
      $password_type = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest';
      $encoding_type = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';

      // Creating WSS identification header using SimpleXML
      $root = new \SimpleXMLElement('<root/>');

      $security = $root->addChild('wsse:Security', null, $ns_wsse);

      $timestamp = $security->addChild('wsu:Timestamp', null, $ns_wsu);
      $timestamp->addAttribute('wsu:Id', 'Timestamp-28');
      $timestamp->addChild('wsu:Created', $tm_created, $ns_wsu);
      $timestamp->addChild('wsu:Expires', $tm_expires, $ns_wsu);

      $usernameToken = $security->addChild('wsse:UsernameToken', null, $ns_wsse);
      $usernameToken->addChild('wsse:Username', $user, $ns_wsse);

      $password = $usernameToken->addChild('wsse:Password', $passdigest, $ns_wsse);
      $password->addAttribute('Type', $password_type);

      $nonce = $usernameToken->addChild('wsse:Nonce', $encoded_nonce, $ns_wsse);
      $nonce->addAttribute('EncodingType', $encoding_type);

      $usernameToken->addChild('wsu:Created', $tm_created, $ns_wsu);

      // Recovering XML value from that object
      $root->registerXPathNamespace('wsse', $ns_wsse);
      $full = $root->xpath('/root/wsse:Security');
      $auth = $full[0]->asXML();

      return $auth;
   }

   /**
    * As natively, PHP soap returns objects, this method converts them
    * to arrays, to be retrocompatible with the first version of this
    * Sellermania client.
    *
    * @access private
    * @param \stdClass|array $object
    * @return array
    */
   private function _convertObjectToArray($object)
   {
      $array = array();
      if (!($object instanceof \stdClass) && (!is_array($object)))
      {
         throw new Exception("Invalid argument given to convertObjectToArray, expected array or \stdClass instance.");
      }
      foreach ($object as $property => $value)
      {
         if (((is_object($value)) && ($value instanceof \stdClass)) || (is_array($value)))
         {
            $array[$property] = $this->_convertObjectToArray($value);
         }
         else if (is_scalar($value))
         {
            $array[$property] = "{$value}";
         }
         else
         {
            $array[$property] = $value;
         }
      }
      return $array;
   }

}