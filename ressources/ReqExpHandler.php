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
         $url = $e->getRequest()->getUri();
         if (!empty($e->getResponse()->getBody())) {
           $body = json_decode($e->getResponse()->getBody(), true);
           $httpreason = $httpreason.': '.$body['message'];
         }
         throw new \RiminderApiResponseException($httpcode, $httpreason, $url,1);

       } catch (GuzzleHttp\Exception\TransferException $e) {
         throw new \RiminderApiException("Error Processing Request for url'".$e->getRequest()->getUri()."': ". $e->getMessage(), 1);
       }
     }
  }

 ?>
