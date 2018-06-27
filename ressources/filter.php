<?php

  require_once 'RequestBodyUtils.php';

  class RiminderFilter
  {
    public function __construct($parent) {
      $this->riminder = $parent;
    }

    public function list() {
      $resp = $this->riminder->_rest->get("filters");

      return json_decode($resp->getBody(), true)['data'];
    }

    public function get(RiminderFilterIdent $filter_ident) {
      $query = [];
      $filter_ident->addToArray($query);
      $resp = $this->riminder->_rest->get("filter", $query);

      return json_decode($resp->getBody(), true)['data'];
    }
  }
 ?>
