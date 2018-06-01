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
      return $resp->decode_response()['data'];
    }

    public function get($source_id) {
      $query = array('source_id' => $source_id);
      $resp = $this->riminder->_rest->get("source", $query);
      ResponseChecker::check($resp);
      return $resp->decode_response()['data'];
    }
  }
 ?>
