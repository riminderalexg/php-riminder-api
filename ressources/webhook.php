<?php

  require_once 'RiminderConstant.php';

  /**
   *
   */
  class RiminderWebhook
  {
    public function __construct($parent) {
      $this->riminder = $parent;
      $this->handlers = [
        RiminderEvents::PROFILE_PARSE_SUCCESS => null,
        RiminderEvents::PROFILE_PARSE_ERROR   => null,
        RiminderEvents::PROFILE_SCORE_SUCCESS => null,
        RiminderEvents::PROFILE_SCORE_ERROR   => null,
        RiminderEvents::FILTER_TRAIN_SUCCESS  => null,
        RiminderEvents::FILTER_TRAIN_ERROR    => null,
        RiminderEvents::FILTER_SCORE_SUCCESS  => null,
        RiminderEvents::FILTER_SCORE_ERROR    => null,
      ];
    }

    public function check() {
      $resp = $this->riminder->_rest->post("webhook/check");

      return json_decode($resp->getBody(), true);
    }

    public function setHandler($eventName, $func) {
      if (!array_key_exists($eventName, $this->handlers)){
        throw new \RiminderApiArgumentException($eventName."is not a valid event.");
      }
      $this->handlers[$eventName] = $func;
    }

    public function isHandlerPresent($eventName) {
      if (!array_key_exists($eventName, $this->handlers)){
        throw new \RiminderApiArgumentException($eventName."is not a valid event.");
      }
      return $this->handlers[$eventName] != null;
    }

    public function removeHandler($eventName) {
      if (!array_key_exists($eventName, $this->handlers)){
        throw new \RiminderApiArgumentException($eventName."is not a valid event.");
      }
      $this->handlers[$eventName] = null;
    }

    private static function base64UrlDecode($inp) {
      return base64_decode(strtr($inp, '-_', '+/'));
    }

    private function is_signature_valid($sign, $payload) {
      $exp_sign = hash_hmac('sha256', $payload, $this->riminder->webhookSecret, $raw=true);
      return $sign === $exp_sign;
    }

    private function decode_request($encodedRequest) {
      list($encoded_sign, $payload) = explode('.', $encodedRequest, 2);

      $sign = self::base64UrlDecode($encoded_sign);
      $data = self::base64UrlDecode($payload);
      if (!$this->is_signature_valid($sign, $data)) {
        throw new \RiminderApiWebhookException("Error: invalid signature.");
      }

      return json_decode($data, true);
    }

    private function getHandlerForEvent($eventName) {
      if (!array_key_exists($eventName, $this->handlers)) {
        throw new \RiminderApiWebhookException("Error: ".$eventName." is a invalid type.");
      }
      $handler = $this->handlers[$eventName];
      return $handler;
    }

    public function handleRequest($encodedRequest) {
      if (is_null($this->riminder->webhookSecret)) {
        throw new \RiminderApiArgumentException("No webhook secret.");
      }
      $decoded_request = $this->decode_request($encodedRequest);
      $handler = $this->getHandlerForEvent($decoded_request['type']);
      if (is_null($handler)){
        return;
      }
      $handler($decoded_request['type'], $decoded_request);
    }
  }

 ?>
