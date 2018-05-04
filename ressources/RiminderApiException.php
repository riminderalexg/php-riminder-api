<?php

/**
 *
 */
class RiminderApiException extends Exception
{ }

/**
 *
 */
class RiminderApiResponseException extends RiminderApiException
{

  function __construct($httpcode, $message, $expCode = 0)
  {
      $this->httpcode = $httpcode;
      $this->httpmessage = $message;
      $message = 'HTTP Response error: \'' . strval($this->httpcode) . ': ' . $this->httpmessage . '\'';
      parent::__construct($message, $expCode);
  }

  public function getHttpCode() {
    return $this->httpcode;
  }

  public function getHttpMessage() {
    return $this->httpmessage;
  }
}


 ?>
