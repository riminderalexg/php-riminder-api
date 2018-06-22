<?php


  class RiminderSource
  {
    public function __construct($parent) {
      $this->riminder = $parent;
    }

    public function getSources() {
      $resp = $this->riminder->_rest->get("sources");

      return json_decode($resp->getBody(), true)['data'];
    }

    public function getSource($source_id) {
      $query = array('source_id' => $source_id);
      $resp = $this->riminder->_rest->get("source", $query);

      return json_decode($resp->getBody(), true)['data'];
    }
  }
 ?>
