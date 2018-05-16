<?php
  /**
   *
   */
  require_once 'RiminderApiException.php';
  class ResponseChecker
  {
     public static function check($response) {
       if ($response->error) {
         throw new \RiminderApiException("Error while processing request: " . $response->error, 1);
       }
       if ($response->info->http_code < 200 || $response->info->http_code > 299) {
          $decodedResponse = $response->decode_response();
         throw new \RiminderApiResponseException($decodedResponse['code'], $decodedResponse['message'], 1);
       }
    }
  }

 ?>
