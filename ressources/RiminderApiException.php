<?php

/**
 *
 */
class RiminderApiException extends Exception
{ }

class RiminderApiArgumentException extends Exception
{

    function __construct($expectedType, $argumentName, $expCode = 0)
    {
        $message = $argumentName . 'is not a valid ' . $expectedType;
        parent::__construct($message, $expCode);
    }
}

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
