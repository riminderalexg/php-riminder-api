<?php
require_once 'ResponseChecker.php';

  class RiminderSource
  {
    public function __construct($parent) {
      $this->riminder = $parent;
    }

    public function getSources() {
      $resp = $this->riminder->_rest->get("sources");
      ResponseChecker::check($resp);
      return $resp->decode_response();
    }

    public function get($source_id) {
      $resp = $this->riminder->_rest->get("source/$source_id");
      ResponseChecker::check($resp);
      return $resp->decode_response();
    }
  }
 ?>
