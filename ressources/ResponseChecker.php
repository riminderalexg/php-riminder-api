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
         try {
          $decodedResponse = $response->decode_response();
         } catch (\RestClientException $e) {
            throw new \RiminderApiResponseException($response->info->http_code, '...', 1);
         }
         throw new \RiminderApiResponseException($decodedResponse['code'], $decodedResponse['message'], 1);
       }
    }
  }

 ?>
