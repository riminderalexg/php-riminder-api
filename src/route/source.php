<?php

  namespace riminder\rimindapi\route;

  class RimindapiSource
  {
    public function _construct($parent) {
      $this->rimindapi = $parent;
    }

    public function getSources() {
      $resp = $this->rimindapi->_rest->get("sources");
      return $resp->decode_response();
    }

    public function get($source_id) {
      $resp = $this->rimindapi->_rest->get("source/$source_id");
      return $resp->decode_response();
    }
  }
 ?>
