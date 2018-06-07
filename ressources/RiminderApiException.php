<?php

/**
 *  RiminderApiException is the standard exception for Riminder sdk.
 *  All other riminder exception inhertits from it.
 */
class RiminderApiException extends Exception
{ }

/**
 *  RiminderApiArgumentException occurs when arguments passed to a route
 *  are invalid (bad types, missing, ...).
 */
class RiminderApiArgumentException extends RiminderApiException
{

    function __construct($message, $expCode = 0)
    {
        parent::__construct($message, $expCode);
    }
}

/**
 * RiminderApiResponseException occurs when the server responde an error code.
 * It containt the code and the reason.
 */
class RiminderApiResponseException extends RiminderApiException
{

  function __construct($httpcode, $message, $url,$expCode = 0)
  {
      $this->httpcode = $httpcode;
      $this->httpmessage = $message;
      $this->url = $url;
      $message = 'HTTP Response error for url\''.$url.'\': \'' . strval($this->httpcode) . ': ' . $this->httpmessage . '\'';
      parent::__construct($message, $expCode);
  }

  public function getHttpCode() {
    return $this->httpcode;
  }

  public function getHttpMessage() {
    return $this->httpmessage;
  }

  public function getUrl()
  {
    return $this->url;
  }
}


 ?>
