<?php
  /**
   *
   */
  require __DIR__ . '/../vendor/autoload.php';
  require_once 'RiminderApiException.php';

  class ReqExpHandler
  {
     public static function exec($reqLambda) {

       try {
         return $reqLambda();

       } catch (GuzzleHttp\Exception\BadResponseException $e) {
         $httpcode = $e->getResponse()->getStatusCode();
         $httpreason = $e->getResponse()->getReasonPhrase();
         // $httpbody = $e->getResponse()->getBody();
         throw new \RiminderApiResponseException($httpcode, $httpreason, 1);

       } catch (GuzzleHttp\Exception\TransferException $e) {
         throw new \RiminderApiException("Error Processing Request: ". $e->getMessage(), 1);
       }
     }
  }

 ?>
