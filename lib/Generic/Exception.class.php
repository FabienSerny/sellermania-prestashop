<?php

namespace Sellermania;

/**
 * This exception namespaced Sellermania is absolutely similar to the native
 * PHP \Exception, except that the current date and time are concatained to
 * the exception message.
 *
 * @author alain tiemblo
 * @copyright (c) 2013, Sellermania
 * @uses Pear Mail_Mime http://pear.php.net/package/Mail_Mime/
 * @uses PHP SoapClient http://php.net/manual/en/class.soapclient.php
 * @version 2.0
 */
class Exception extends \Exception
{

   /**
    * Class cosntructor
    *
    * Constructs an exception with the message concatained to the current datetime.
    * Initialize default timezone if not set on your php.ini file to avoid using UTC.
    *
    * @access public
    * @param string $message
    * @param int $code
    * @param \Exception $previous
    */
   public function __construct($message = null, $code = 0, \Exception $previous = null)
   {
      if (!date_default_timezone_get())
      {
         date_default_timezone_set('Europe/Paris');
      }

      $message =  date("Y-m-d H:i:s") . " {$message}\n";
      parent::__construct($message, $code, $previous);
   }

}